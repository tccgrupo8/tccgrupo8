<?php
// Ajuste usuário/senha se necessário
$host = "localhost";
$usuario = "u557720587_2025_php02";
$senha = "Mtec@php2";
$banco = "u557720587_2025_php02";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
