<?php include 'conexao.php'; ?>
<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pedidos - Vira Copos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-dark bg-black sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand ms-2" href="painel.php">Vira Copos - Pedidos</a>
    <a class="btn btn-light" href="painel.php">Voltar</a>
  </div>
</nav>

<div class="d-flex">
  <aside class="sidebar bg-black text-white d-none d-md-block p-3">
    <h4>Funcionário</h4>
    <hr class="text-white">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link text-white" href="painel.php">🏠 Painel</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="pedidos.php">📦 Pedidos</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="estoque.php">📊 Estoque</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="atendimento.php">🎧 Atendimento</a></li>
    </ul>
  </aside>

  <main class="content p-4">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="m-0">📦 Pedidos</h1>
        <div class="d-flex gap-2">
          <a href="adicionar_pedido.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Adicionar Pedido
          </a>
          <a href="adicionar_produto.php" class="btn btn-primary">
            <i class="bi bi-plus-square"></i> Adicionar Produto
          </a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered mt-3">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Mesa</th>
              <th>Itens</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $sql = "
              SELECT p.id, p.cliente, p.mesa, p.status,
              GROUP_CONCAT(CONCAT(i.quantidade, 'x ', pr.nome) SEPARATOR ', ') AS itens
              FROM pedidos p
              LEFT JOIN itens_pedido i ON p.id = i.pedido_id
              LEFT JOIN produtos pr ON i.produto_id = pr.id
              GROUP BY p.id
              ORDER BY p.id DESC
              ";

              $res = $conn->query($sql) or die($conn->error);

              if ($res->num_rows > 0) {
                while($r = $res->fetch_assoc()) {
                  echo '<tr>';
                  echo '<td>'.$r['id'].'</td>';
                  echo '<td>'.$r['cliente'].'</td>';
                  echo '<td>'.$r['mesa'].'</td>';
                  echo '<td>'.$r['itens'].'</td>';
                  echo '<td>'.$r['status'].'</td>';
                  echo '<td>
                    <a class="btn btn-sm btn-success" href="atualizar_status.php?id='.$r['id'].'&status=Preparando">Preparar</a>
                    <a class="btn btn-sm btn-primary" href="atualizar_status.php?id='.$r['id'].'&status=Concluído">Concluir</a>
                  </td>';
                  echo '</tr>';
                }
              } else {
                echo '<tr><td colspan="6" class="text-center">Nenhum pedido encontrado.</td></tr>';
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
