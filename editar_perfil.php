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

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Senha
    if (!empty($_POST['senha'])) {
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $sql_update = "UPDATE funcionarios SET nome=?, email=?, senha=? WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssi", $nome, $email, $senha, $id_func);
    } else {
        $sql_update = "UPDATE funcionarios SET nome=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssi", $nome, $email, $id_func);
    }

    // Upload de foto
    if (!empty($_FILES['foto']['name'])) {
        $arquivo = $_FILES['foto'];
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $novo_nome = 'func_'.$id_func.'.'.$ext;
        $caminho = 'uploads/'.$novo_nome;
        move_uploaded_file($arquivo['tmp_name'], $caminho);

        $conn->query("UPDATE funcionarios SET foto='$novo_nome' WHERE id=$id_func");
    }

    if ($stmt->execute()) {
        $msg = "Perfil atualizado com sucesso!";
        $funcionario['nome'] = $nome;
        $funcionario['email'] = $email;
    } else {
        $msg = "Erro ao atualizar perfil.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Perfil - Vira Copos</title>
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
    <a class="navbar-brand" href="perfil.php">Vira Copos - Editar Perfil</a>
    <a class="btn btn-light" href="perfil.php">Voltar</a>
  </div>
</nav>

<div class="container mt-5">
    <div class="card shadow-sm p-4 text-center">
        <h3 class="mb-4">Editar Perfil</h3>

        <?php if($msg): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <img src="<?= $funcionario['foto'] ? 'uploads/'.$funcionario['foto'] : 'uploads/default.png' ?>" class="foto-perfil mb-3">
                <input type="file" class="form-control" name="foto">
            </div>

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($funcionario['nome']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($funcionario['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nova Senha <small class="text-muted">(deixe em branco para não alterar)</small></label>
                <input type="password" class="form-control" name="senha">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
