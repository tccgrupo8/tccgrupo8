<?php
include 'conexao.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: pedidos.php');
    exit;
}

$id_pedido = intval($_GET['id']);

// Atualiza o status do pedido
$sql = "UPDATE pedidos SET status = 'Finalizado' WHERE id = $id_pedido";

if ($conn->query($sql) === TRUE) {
    header("Location: pedidos.php?msg=finalizado");
} else {
    echo "Erro ao finalizar pedido: " . $conn->error;
}

$conn->close();
?>
