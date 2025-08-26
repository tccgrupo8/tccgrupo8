<?php
include 'conexao.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO funcionarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param("sss", $nome, $email, $senha);

    if ($stmt->execute()) {
        $msg = "Funcionário cadastrado com sucesso!";
    } else {
        $msg = "Erro: " . $stmt->error;
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cadastro de Funcionário</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-black">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 bg-black text-white">
                <h3 class="mb-3">Cadastro de Funcionário</h3>
                <?php if($msg): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nome do funcionario</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button class="btn btn-success bg-light text-black w-100">Cadastrar</button>
                </form>
                <a href="login.php" class="d-block mt-3">Voltar ao login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
