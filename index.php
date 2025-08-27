<?php
session_start();
include 'conexao.php';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $sql = "SELECT * FROM funcionarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $func = $result->fetch_assoc();
        if (password_verify($senha, $func['senha'])) {
            $_SESSION['funcionario_id'] = $func['id'];
            $_SESSION['funcionario_nome'] = $func['nome'];
            header('Location: painel.php');
            exit;
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<img src="viracopos.png" alt="Imagem de Viracopos" style="display: block; margin: 0 auto; width: 300px;">
<title>Login - Funcionário</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/login.css">
</head>
<body class="bg-black">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card bg-black p-4">
                <h3 class="mb-3 text-white">Login do Funcionário</h3>
                <?php if($erro): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label text-white">Email</label>
                        <input type="text" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button class="btn w-100 text-black" style="background-color: #1ED760; border: none;">
                            Entrar
                    </button>
                </form>
                <a href="cadastro_funcionario.php" class="d-block mt-3">Cadastrar novo funcionário</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
