<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

// Verifica se veio o POST corretamente
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['produtos'])) {
    header('Location: pedidos.php');
    exit;
}

$cliente = trim($_POST['cliente'] ?? '');
$mesa = trim($_POST['mesa'] ?? '');
$funcionario_id = intval($_SESSION['funcionario_id']);
$produtosSelecionados = $_POST['produtos'];

if (empty($cliente) || empty($mesa)) {
    echo "<script>alert('Preencha o cliente e a mesa.'); window.history.back();</script>";
    exit;
}

// Calcula o total
$placeholders = implode(',', array_fill(0, count($produtosSelecionados), '?'));
$sql = "SELECT preco FROM produtos WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($produtosSelecionados)), ...array_map('intval', $produtosSelecionados));
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
while ($row = $result->fetch_assoc()) {
    $total += floatval($row['preco']);
}

// Insere o pedido
$status = 'Em andamento';
$sqlPedido = "INSERT INTO pedidos (cliente, mesa, funcionario_id, status, total) VALUES (?, ?, ?, ?, ?)";
$stmtPedido = $conn->prepare($sqlPedido);
$stmtPedido->bind_param("ssisd", $cliente, $mesa, $funcionario_id, $status, $total);
$stmtPedido->execute();
$pedido_id = $conn->insert_id;

// Insere os itens do pedido
$sqlItem = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade) VALUES (?, ?, ?)";
$stmtItem = $conn->prepare($sqlItem);

foreach ($produtosSelecionados as $produto_id) {
    $quantidade = 1;
    $stmtItem->bind_param("iii", $pedido_id, $produto_id, $quantidade);
    $stmtItem->execute();
}

// Redireciona para pedidos.php com mensagem
header("Location: pedidos.php?msg=sucesso");
exit;
?>
