<?php
session_start();
include 'conexao.php';

// Verifica se o usuário é administrador (ou funcionário com permissão)
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'] ?? ''; // Novo campo

    // Tratamento da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeImagem = uniqid() . '.' . $extensao;
        $caminho = 'imagens/' . $nomeImagem;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $sql = "INSERT INTO produtos (nome, categoria, preco, imagem, descricao) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssdss', $nome, $categoria, $preco, $caminho, $descricao);
            if ($stmt->execute()) {
                $msg = "✅ Produto cadastrado com sucesso!";
            } else {
                $msg = "❌ Erro ao cadastrar produto.";
            }
        } else {
            $msg = "❌ Erro ao enviar imagem.";
        }
    } else {
        $msg = "❌ Selecione uma imagem válida.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">

    <!-- Cabeçalho com botão de voltar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">➕ Cadastrar Produto</h2>
        <a href="pedidos.php" class="btn btn-outline-danger rounded-pill px-4 py-2 shadow-sm">
            ⬅ Voltar
        </a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nome do Produto</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoria</label>
                    <input type="text" name="categoria" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Preço (R$)</label>
                    <input type="number" name="preco" class="form-control" step="0.01" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3" placeholder="Digite a descrição do produto" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagem do Produto</label>
                    <input type="file" name="imagem" class="form-control" accept="image/*" required>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 py-2 shadow-sm">
                        ✅ Cadastrar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
