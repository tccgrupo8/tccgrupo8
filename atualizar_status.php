<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}
?>

<?php
include 'conexao.php';
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header('Location: pedidos.php');
    exit;
}
$id = intval($_GET['id']);
$status = $conn->real_escape_string($_GET['status']);
$sql = "UPDATE pedidos SET status = '$status' WHERE id = $id";
$conn->query($sql) or die($conn->error);
header('Location: pedidos.php');
exit;
?>
