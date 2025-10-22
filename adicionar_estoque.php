<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

include 'conexao.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto = $_POST['produto'] ?? '';
    $quantidade = $_POST['quantidade'] ?? '';
    $data_entrada = $_POST['data_entrada'] ?? '';
    $validade = $_POST['validade'] ?? '';
    $responsavel_id = $_SESSION['funcionario_id']; // pega quem está logado

    if (!empty($produto) && !empty($quantidade) && !empty($data_entrada) && !empty($validade)) {
        $sql = "INSERT INTO estoque (produto, quantidade, data_entrada, validade, responsavel_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $produto, $quantidade, $data_entrada, $validade, $responsavel_id);

        if ($stmt->execute()) {
            $msg = "✅ Produto adicionado com sucesso!";
        } else {
            $msg = "❌ Erro ao adicionar produto: " . $conn->error;
        }
    } else {
        $msg = "⚠️ Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>➕ Adicionar Produto ao Estoque</h2>
        <?php if ($msg): ?>
            <div class="alert alert-info mt-3"><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="produto" class="form-label">Produto</label>
                <input type="text" class="form-control" name="produto" id="produto" required>
            </div>

            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="text" class="form-control" name="quantidade" id="quantidade" required>
            </div>

            <div class="mb-3">
                <label for="data_entrada" class="form-label">Data de Entrada</label>
                <input type="date" class="form-control" name="data_entrada" id="data_entrada" required>
            </div>

            <div class="mb-3">
                <label for="validade" class="form-label">Validade</label>
                <input type="date" class="form-control" name="validade" id="validade" required>
            </div>

            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="estoque.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
