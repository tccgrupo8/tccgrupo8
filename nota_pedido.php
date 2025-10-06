<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['produtos'])) {
    header('Location: pedidos.php');
    exit;
}

$produtosSelecionados = $_POST['produtos'];
$placeholders = implode(',', array_fill(0, count($produtosSelecionados), '?'));

$sql = "SELECT * FROM produtos WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($produtosSelecionados)), ...$produtosSelecionados);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$produtos = [];
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
    $total += $row['preco'];
}

if (isset($_POST['finalizar'])) {
    // Cria o pedido
    $conn->query("INSERT INTO pedidos (status) VALUES ('Em andamento')");
    $pedido_id = $conn->insert_id;

    // Insere os itens do pedido
    $stmt = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id) VALUES (?, ?)");
    foreach ($produtosSelecionados as $produto_id) {
        $stmt->bind_param("ii", $pedido_id, $produto_id);
        $stmt->execute();
    }

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
                        <td><?= number_format($p['preco'], 2, ',', '.') ?></td>
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

    <form method="POST" action="">
        <?php foreach ($produtosSelecionados as $id): ?>
            <input type="hidden" name="produtos[]" value="<?= $id ?>">
        <?php endforeach; ?>
        <div class="text-center mt-4">
            <button type="submit" name="finalizar" class="btn btn-primary btn-lg">Finalizar Pedido</button>
            <a href="adicionar_pedido.php" class="btn btn-secondary btn-lg">Voltar</a>
        </div>
    </form>
</div>
</body>
</html>
