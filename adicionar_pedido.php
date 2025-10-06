<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

// Puxa produtos do banco organizados por categoria
$sql = "SELECT * FROM produtos ORDER BY categoria, nome";
$result = $conn->query($sql);

// Organiza os produtos em um array por categoria
$categorias = [];
while ($row = $result->fetch_assoc()) {
    $categorias[$row['categoria']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .categoria-titulo {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: bold;
            font-size: 18px;
        }
        .produto-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 10px;
            margin: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .produto-card:hover {
            background-color: #f1f1f1;
        }
        .produto-card input[type="checkbox"] {
            display: none;
        }
        .produto-card.checked {
            background-color: #d1e7dd;
            border-color: #0f5132;
        }
        .checkmark {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 5px;
            border: 2px solid #0d6efd;
            margin-right: 5px;
            position: relative;
        }
        .checked .checkmark::after {
            content: "✔";
            color: white;
            background-color: #0d6efd;
            position: absolute;
            top: -3px;
            left: 3px;
            font-size: 16px;
            border-radius: 5px;
            width: 20px;
            height: 20px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="mb-4 text-center">Adicionar Pedido</h2>
    <form method="POST" action="nota_pedido.php">
        <?php foreach ($categorias as $categoria => $produtos): ?>
            <div class="categoria-titulo"><?= htmlspecialchars($categoria) ?></div>
            <div class="row">
                <?php foreach ($produtos as $produto): ?>
                    <div class="col-md-3">
                        <label class="produto-card" onclick="toggleCheck(this)">
                            <input type="checkbox" name="produtos[]" value="<?= $produto['id'] ?>">
                            <span class="checkmark"></span>
                            <strong><?= htmlspecialchars($produto['nome']) ?></strong><br>
                            <small>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></small>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg">Finalizar Pedido</button>
        </div>
    </form>
</div>

<script>
function toggleCheck(element) {
    const checkbox = element.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
    element.classList.toggle('checked', checkbox.checked);
}
</script>
</body>
</html>
