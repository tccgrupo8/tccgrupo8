<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

$id_func = $_SESSION['funcionario_id'];
$sql = "SELECT * FROM funcionarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_func);
$stmt->execute();
$result = $stmt->get_result();
$funcionario = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Perfil - Vira Copos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .foto-perfil {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #0d6efd;
    }
</style>
</head>
<body>
<nav class="navbar navbar-dark bg-black sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="painel.php">Vira Copos - Perfil</a>
    <a class="btn btn-light" href="painel.php">Voltar</a>
  </div>
</nav>

<div class="container mt-5">
    <div class="card shadow-sm p-4 text-center">
        <img src="<?= $funcionario['foto'] ? 'uploads/'.$funcionario['foto'] : 'uploads/default.png' ?>" alt="Foto do Funcionário" class="foto-perfil mb-3">
        <h3><?= htmlspecialchars($funcionario['nome']) ?></h3>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($funcionario['email']) ?></p>
        <p><strong>Cargo:</strong> <?= htmlspecialchars($funcionario['cargo']) ?></p>
        <a href="editar_perfil.php" class="btn btn-primary btn-lg mt-3">Editar Perfil</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
