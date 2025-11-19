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
<link rel="stylesheet" href="style.css?v=9">
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
    <div class="perfil-card">

        <img src="<?= $funcionario['foto'] ? 'uploads/'.$funcionario['foto'] : 'uploads/default.png' ?>"
             class="foto-perfil">

        <div class="perfil-info">
            <h2>Teste Usuário</h2>

            <p><strong>E-mail:</strong> teste@gmail.com</p>
            <p><strong>Cargo:</strong> Garçom</p>

            <a href="editar_perfil.php" class="btn-editar-perfil">Editar Perfil</a>
        </div>

    </div>
</div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
