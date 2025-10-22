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

// Recebe dados do formulário
$produtosSelecionados = $_POST['produtos'];
$cliente = trim($_POST['cliente'] ?? '');
$mesa = trim($_POST['mesa'] ?? '');
$funcionario_id = intval($_SESSION['funcionario_id']);

if (empty($cliente) || empty($mesa)) {
    echo "<script>alert('Preencha o cliente e a mesa.'); window.history.back();</script>";
    exit;
}

// Busca produtos selecionados para calcular total
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

// Insere pedido na tabela 'pedidos'
$status = 'Em andamento';
$sqlPedido = "INSERT INTO pedidos (cliente, mesa, funcionario_id, status, total) VALUES (?, ?, ?, ?, ?)";
$stmtPedido = $conn->prepare($sqlPedido);
if (!$stmtPedido) die("Erro no prepare pedido: " . $conn->error);

$stmtPedido->bind_param("ssisd", $cliente, $mesa, $funcionario_id, $status, $total);
$stmtPedido->execute();
$pedido_id = $conn->insert_id;

// Insere itens do pedido na tabela 'itens_pedido'
$sqlItem = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade) VALUES (?, ?, ?)";
$stmtItem = $conn->prepare($sqlItem);
if (!$stmtItem) die("Erro no prepare itens: " . $conn->error);

foreach ($produtosSelecionados as $produto_id) {
    $quantidade = 1; // por enquanto, sempre 1
    $produto_id = intval($produto_id);
    $stmtItem->bind_param("iii", $pedido_id, $produto_id, $quantidade);
    $stmtItem->execute();
}

// ==================== TELA DE NOTA ====================
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nota do Pedido #<?= $pedido_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5 mb-5">
    <div class="card shadow-sm p-4">
        <h2 class="text-center mb-4">🧾 Nota do Pedido #<?= $pedido_id ?></h2>
        <div class="mb-3"><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></div>
        <div class="mb-3"><strong>Mesa:</strong> <?= htmlspecialchars($mesa) ?></div>
        <div class="mb-3"><strong>Status:</strong> <?= htmlspecialchars($status) ?></div>
        <hr>
        <h4>Itens:</h4>
        <ul>
            <?php foreach ($produtos as $p): ?>
                <li><?= htmlspecialchars($p['nome']) ?> - R$ <?= number_format($p['preco'], 2, ',', '.') ?></li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <h4>Total: R$ <?= number_format($total, 2, ',', '.') ?></h4>
        <div class="text-center mt-4">
            <a href="pedidos.php" class="btn btn-primary btn-lg">Confirmar pedido?</a>
        </div>
    </div>
</div>
</body>
</html>
