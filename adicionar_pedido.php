<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}

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

<style>
.produto-card { min-height: 380px; }
.quantidade-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
}
.quantidade-input {
    width: 45px;
    text-align: center;
    font-weight: bold;
}
.sticky-top { top: 90px; }
</style>

</head>
<body class="bg-light">

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">🍽️ Adicionar Pedido</h2>
        <a href="pedidos.php" class="btn btn-outline-danger rounded-pill px-4 py-2 shadow-sm">⬅ Voltar</a>
    </div>

<form method="POST" action="nota_pedido.php">

    <div class="row">
        <div class="col-lg-8">

            <div class="card shadow-sm mb-4 border-0 rounded-3">
                <div class="card-body">
                    <h4 class="card-title mb-3">🧍 Informações do Pedido</h4>

                    <div class="row">
                        <div class="mb-4 col-md-6">
                            <label class="form-label">Nome do Cliente:</label>
                            <input type="text" name="cliente" class="form-control" required>
                        </div>

                        <div class="mb-4 col-md-6">
                            <label class="form-label">Mesa:</label>
                            <select name="mesa" class="form-select" required>
                                <option value="">Selecione a mesa</option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="<?= $i ?>">Mesa <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach ($categorias as $categoria => $produtos): ?>
                <h4 class="mt-4"><?= htmlspecialchars($categoria) ?></h4>
                <hr>

                <div class="row">
                <?php foreach ($produtos as $produto): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card produto-card shadow-sm">

                            <img src="<?= $produto['imagem'] ?: 'imagens/sem_imagem.jpg' ?>" 
                                 class="card-img-top rounded-top"
                                 style="height:150px; object-fit:contain">

                            <div class="card-body text-center">

                                <div class="fw-bold"><?= $produto['nome'] ?></div>
                                <div class="text-muted small"><?= $produto['descricao'] ?></div>
                                <div class="text-primary fw-bold">R$ <?= number_format($produto['preco'],2,',','.') ?></div>

                                <div class="quantidade-container mt-2">
                                    <button type="button" class="btn btn-light btn-sm"
                                        onclick="alterarQtd('qtd<?= $produto['id'] ?>', -1)">-</button>

                                    <input type="number" id="qtd<?= $produto['id'] ?>" 
                                           name="quantidades[<?= $produto['id'] ?>]"
                                           value="1" min="1"
                                           class="quantidade-input">

                                    <button type="button" class="btn btn-light btn-sm"
                                        onclick="alterarQtd('qtd<?= $produto['id'] ?>', 1)">+</button>
                                </div>

                                <input type="checkbox" name="produtos[]" value="<?= $produto['id'] ?>"
                                       id="chk<?= $produto['id'] ?>" style="display:none">

                                <button type="button"
                                    class="btn btn-warning w-100 mt-3"
                                    onclick="addProduto('chk<?= $produto['id'] ?>',
                                        '<?= htmlspecialchars($produto['nome']) ?>',
                                        <?= $produto['preco'] ?>,
                                        this,
                                        'qtd<?= $produto['id'] ?>')">
                                    Adicionar
                                </button>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="col-lg-4">
            <div class="card p-3 sticky-top shadow-sm">
                <h5>Resumo do Pedido</h5>
                <ul id="listaResumo" class="list-group mb-3"></ul>

                <h6>Total: R$ <span id="totalPedido">0,00</span></h6>

                <button type="submit"
                        class="btn btn-success mt-3 w-100 fs-5">
                    ✅ Finalizar Pedido
                </button>
            </div>
        </div>
    </div>
</form>

</div>

<script>
let pedido = {};

function formatar(valor) {
    return valor.toLocaleString('pt-BR',{minimumFractionDigits:2});
}

function atualizarResumo() {
    const lista = document.getElementById('listaResumo');
    lista.innerHTML = '';
    let total = 0;

    for (const id in pedido) {
        const item = pedido[id];
        if (item.qtd > 0) {
            total += item.valor * item.qtd;
            lista.innerHTML += `
            <li class="list-group-item d-flex justify-content-between">
                ${item.nome} x${item.qtd}
                <strong>R$ ${formatar(item.valor * item.qtd)}</strong>
            </li>`;
        }
    }

    document.getElementById('totalPedido').innerText = formatar(total);
}

function addProduto(idChk, nome, valor, btn, qtdCampo) {
    const chk = document.getElementById(idChk);
    const qtd = document.getElementById(qtdCampo);

    chk.checked = !chk.checked;

    if (chk.checked) {
        btn.textContent = "Adicionado ✅";
        btn.classList.remove("btn-warning");
        btn.classList.add("btn-success");

        pedido[idChk] = {
            nome: nome,
            valor: valor,
            qtd: parseInt(qtd.value)
        };
    } else {
        btn.textContent = "Adicionar";
        btn.classList.remove("btn-success");
        btn.classList.add("btn-warning");

        delete pedido[idChk];
    }

    atualizarResumo();
}

function alterarQtd(campo, valor) {
    const input = document.getElementById(campo);
    let qtd = parseInt(input.value);
    qtd += valor;
    if (qtd < 1) qtd = 1;
    input.value = qtd;
}
</script>

</body>
</ht
