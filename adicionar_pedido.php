<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

// Busca os produtos no banco, ordenados por categoria
$sql = "SELECT * FROM produtos ORDER BY categoria, nome";
$result = $conn->query($sql);

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
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-5">🍽️ Adicionar Pedido</h2>

    <!-- FORM PRINCIPAL -->
    <form method="POST" action="nota_pedido.php">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title mb-3">🧍 Informações do Pedido</h4>
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <label for="cliente" class="form-label">Nome do Cliente:</label>
                        <input type="text" name="cliente" id="cliente" class="form-control" placeholder="Digite o nome do cliente" required>
                    </div>
                    <div class="mb-4 col-md-6">
                        <label for="mesa" class="form-label">Mesa:</label>
                        <select name="mesa" id="mesa" class="form-select" required>
                        <option value="">Selecione a mesa</option>
                            <?php 
                                for($i = 1; $i <= 20; $i++) {
                                    echo "<option value='$i'>Mesa $i</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($categorias as $categoria => $produtos): ?>
            <div class="categoria-titulo mt-4"><?= htmlspecialchars($categoria) ?></div>
            <div class="row mt-3">
                <?php foreach ($produtos as $produto): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card produto-card">
                            <img src="<?= htmlspecialchars($produto['imagem'] ?: 'imagens/sem_imagem.jpg') ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($produto['nome']) ?>">
                            <div class="produto-info text-center">
                                <div class="produto-nome"><?= htmlspecialchars($produto['nome']) ?></div>
                                <div class="produto-descricao"><?= htmlspecialchars($produto['descricao']) ?></div> <!-- NOVO -->
                                <div class="produto-preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                                <input type="checkbox" name="produtos[]" value="<?= $produto['id'] ?>" class="form-check-input mt-2">
                                <button type="button" class="btn btn-escolher mt-3 w-100" onclick="toggleProduto(this)">Adicionar</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="text-center mt-5">
            <button type="submit" class="btn btn-success btn-lg">✅ Finalizar Pedido</button>
        </div>
    </form>
</div>

<script>
function toggleProduto(button) {
    const checkbox = button.parentElement.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
    button.classList.toggle('btn-success', checkbox.checked);
    button.classList.toggle('btn-escolher', !checkbox.checked);
    button.textContent = checkbox.checked ? "Adicionado ✅" : "Adicionar";
}
</script>
</body>
</html>
