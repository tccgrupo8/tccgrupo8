<?php
session_start();
include 'conexao.php';

// Verifica se está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

// Se não veio nada via POST, volta
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['produtos'])) {
    header('Location: pedidos.php');
    exit;
}

$produtosSelecionados = $_POST['produtos'];
$cliente = trim($_POST['cliente'] ?? '');
$mesa = trim($_POST['mesa'] ?? '');
$funcionario_id = intval($_SESSION['funcionario_id']);

// Busca produtos selecionados
$placeholders = implode(',', array_fill(0, count($produtosSelecionados), '?'));
$sql = "SELECT * FROM produtos WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("Erro no prepare produtos: " . $conn->error);

$stmt->bind_param(str_repeat('i', count($produtosSelecionados)), ...array_map('intval', $produtosSelecionados));
$stmt->execute();
$result = $stmt->get_result();

// Monta lista de produtos e calcula total
$produtos = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
    $total += floatval($row['preco']);
}

// Quando clicar em “Finalizar Pedido”
if (isset($_POST['finalizar'])) {
    $status = 'Em andamento';

    // Insere pedido
    $sqlPedido = "INSERT INTO pedidos (cliente, numero_mesa, funcionario_id, status, total) VALUES (?, ?, ?, ?, ?)";
    $stmtPedido->bind_param("ssisd", $cliente, $mesa, $funcionario_id, $status, $total);    
    if (!$stmtPedido) die("Erro no prepare pedido: " . $conn->error);

    $stmtPedido->bind_param("ssisd", $cliente, $mesa, $funcionario_id, $status, $total);
    $stmtPedido->execute();
    $pedido_id = $conn->insert_id;

    // Insere itens do pedido
    $sqlItem = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade) VALUES (?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);
    if (!$stmtItem) die("Erro no prepare itens: " . $conn->error);

    foreach ($produtosSelecionados as $produto_id) {
        $quantidade = 1;
        $produto_id = intval($produto_id);
        $stmtItem->bind_param("iii", $pedido_id, $produto_id, $quantidade);
        $stmtItem->execute();
    }

    // Redireciona para pedidos.php após salvar
    header("Location: pedidos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nota do Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="text-center mb-4">Resumo do Pedido</h2>

    <div class="card shadow-sm p-4 mb-4">
        <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente ?: 'Não informado') ?></p>
        <p><strong>Mesa:</strong> <?= htmlspecialchars($mesa ?: 'Não informada') ?></p>
    </div>

    <div class="card shadow-sm p-3">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td><?= number_format(floatval($p['preco']), 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th>R$ <?= number_format($total, 2, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <form method="POST">
        <input type="hidden" name="cliente" value="<?= htmlspecialchars($cliente) ?>">
        <input type="hidden" name="mesa" value="<?= htmlspecialchars($mesa) ?>">
        <?php foreach ($produtosSelecionados as $id): ?>
            <input type="hidden" name="produtos[]" value="<?= intval($id) ?>">
        <?php endforeach; ?>
        <div class="text-center mt-4">
            <button type="submit" name="finalizar" class="btn btn-primary btn-lg">Finalizar Pedido</button>
            <a href="adicionar_pedido.php" class="btn btn-secondary btn-lg">Voltar</a>
        </div>
    </form>
</div>
</body>
</html>
