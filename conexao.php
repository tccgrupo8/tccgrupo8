<?php
// Ajuste usuário/senha se necessário
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "vira_copos";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
