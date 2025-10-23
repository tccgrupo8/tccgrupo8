<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

// Verifica se veio o ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: adicionar_pedido.php');
    exit;
}

$id = intval($_GET['id']);

// Exclui o produto do banco
$sql = "DELETE FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('✅ Produto excluído com sucesso!'); window.location='adicionar_pedido.php';</script>";
} else {
    echo "<script>alert('❌ Erro ao excluir produto.'); window.location='adicionar_pedido.php';</script>";
}
?>
