<?php
include 'auth.php';
require_role(['cozinha','admin']);
include 'conexao.php';

$id = intval($_GET['id']);
$status = $_GET['status'];

$validos = ['aguardando','preparando','pronto'];

if (!in_array($status, $validos)) {
    header("Location: cozinha.php");
    exit;
}

$stmt = $conn->prepare("UPDATE itens_pedido SET status_item=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: cozinha.php");
exit;
?>