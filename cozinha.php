<?php
include 'auth.php';
require_role(['cozinha', 'admin']);
include 'conexao.php';

$sql = "
SELECT 
    p.id AS pedido_id, 
    p.mesa, 
    p.cliente,
    ip.id AS item_id,
    ip.produto_id,
    ip.quantidade,
    ip.status_item,
    pr.nome AS produto_nome
FROM itens_pedido ip
JOIN pedidos p ON p.id = ip.pedido_id
JOIN produtos pr ON pr.id = ip.produto_id
WHERE ip.status_item != 'pronto'
ORDER BY p.id ASC, ip.id ASC
";
$res = $conn->query($sql);

$pedidos = [];
while ($r = $res->fetch_assoc()) {
    $pedidos[$r['pedido_id']]['dados'] = [
        'mesa' => $r['mesa'],
        'cliente' => $r['cliente']
    ];
    $pedidos[$r['pedido_id']]['itens'][] = $r;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cozinha - Vira Copos</title>
<link rel="stylesheet" href="style.css?v=20">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-black sticky-top">
  <div class="container-fluid">
    <span class="navbar-brand">Cozinha - Vira Copos</span>
    <a href="painel.php" class="btn btn-light">Voltar</a>
  </div>
</nav>
<div class="container mt-4">
<h2 class="text-success mb-4">Pedidos em Preparo</h2>
<?php if (empty($pedidos)): ?>
    <div class="alert alert-secondary">Nenhum pedido aguardando preparo.</div>
<?php endif; ?>
<?php foreach ($pedidos as $pid => $info): ?>
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-dark text-success">
        <h4 class="mb-0">
            Pedido #<?= $pid ?> — Mesa <?= $info['dados']['mesa'] ?> —
            <small>Cliente: <?= htmlspecialchars($info['dados']['cliente']) ?></small>
        </h4>
    </div>
    <div class="card-body">
        <?php foreach ($info['itens'] as $item): ?>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            <div>
                <strong><?= $item['produto_nome'] ?></strong><br>
                <small>Qtd: <?= $item['quantidade'] ?></small><br>
                <small>Status: <span class="text-info"><?= $item['status_item'] ?></span></small>
            </div>
            <div class="d-flex gap-2">
                <?php if ($item['status_item'] == 'aguardando'): ?>
                    <a href="update_item.php?id=<?= $item['item_id'] ?>&status=preparando"
                       class="btn btn-warning">Em Preparo</a>
                <?php endif; ?>
                <a href="update_item.php?id=<?= $item['item_id'] ?>&status=pronto"
                   class="btn btn-success">Pronto</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
</div>
</body>
</html>