<?php
include 'conexao.php';
session_start();

if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM estoque WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: estoque.php?msg=excluido");
        exit;
    } else {
        echo "Erro ao excluir o item.";
    }
} else {
    header("Location: estoque.php");
    exit;
}
?>
