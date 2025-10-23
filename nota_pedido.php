<?php
session_start();
include 'conexao.php';

// Verifica login
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

// Se não veio nada via POST, volta
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['produtos'])) {
    header('Location: pedidos.php');
    exit;
}

// Dados do formulário
$produtosSelecionados = $_POST['produtos'];
$cliente = trim($_POST['cliente'] ?? '');
$mesa = trim($_POST['mesa'] ?? '');
$funcionario_id = intval($_SESSION['funcionario_id']);

if (empty($cliente) || empty($mesa)) {
    echo "<script>alert('Preencha o cliente e a mesa.'); window.history.back();</script>";
    exit;
}

// Busca produtos selecionados
$placeholders = implode(',', array_fill(0, count($produtosSelecionados), '?'));
$sql = "SELECT * FROM produtos WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
if (!$stmt) die("Erro no prepare produtos: " . $conn->error);

$stmt->bind_param(str_repeat('i', count($produtosSelecionados)), ...array_map('intval', $produtosSelecionados));
$stmt->execute();
$result = $stmt->get_result();

$produtos = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
    $total += floatval($row['preco']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nota do Pedido - Vira Copos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">🧾 Revisar Pedido</h2>
        <a href="pedidos.php" class="btn btn-outline-danger rounded-pill px-4 py-2 shadow-sm">
            ⬅ Voltar
        </a>
    </div>

    <div class="card shadow-sm p-4 border-0 rounded-3">
        <div class="mb-3"><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></div>
        <div class="mb-3"><strong>Mesa:</strong> <?= htmlspecialchars($mesa) ?></div>
        <div class="mb-3"><strong>Status:</strong> Em andamento</div>
        <hr>
        <h4 class="mb-3">Itens:</h4>
        <ul class="list-group mb-3">
            <?php foreach ($produtos as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($p['nome']) ?>
                    <span class="badge bg-success">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <h4 class="text-end">💰 Total: <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></h4>

        <form method="POST" action="salvar_pedido.php" class="text-center mt-4">
            <input type="hidden" name="cliente" value="<?= htmlspecialchars($cliente) ?>">
            <input type="hidden" name="mesa" value="<?= htmlspecialchars($mesa) ?>">
            <?php foreach ($produtosSelecionados as $produto_id): ?>
                <input type="hidden" name="produtos[]" value="<?= htmlspecialchars($produto_id) ?>">
            <?php endforeach; ?>

            <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 py-2 shadow-sm">
                ✅ Confirmar Pedido
            </button>
        </form>
    </div>
</div>
</body>
</html>
