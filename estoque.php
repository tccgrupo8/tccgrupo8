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
  <title>Estoque - Vira Copos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-dark bg-black sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand ms-2" href="painel.php">Vira Copos - Estoque</a>
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
      <h1>📊 Estoque</h1>
      <div class="table-responsive">
      <a href="adicionar_estoque.php" class="btn btn-success btn-lg">
        <i class="bi bi-plus-circle"></i> Adicionar Item
      </a>
      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'excluido'): ?>
      <div class="alert alert-success">Item excluído com sucesso!</div>
      <?php endif; ?>
      <table class="table table-striped table-bordered mt-3">
        <thead class="table-light">
          <tr>
              <th>Produto</th>
              <th>Quantidade</th>
              <th>Data de Entrada</th>
              <th>Validade</th>
              <th>Responsável</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $sql = "SELECT e.id, e.produto, e.quantidade, e.data_entrada, e.validade, f.nome AS responsavel
                      FROM estoque e
                      LEFT JOIN funcionarios f ON e.responsavel_id = f.id
                      ORDER BY e.produto";
              $res = $conn->query($sql) or die($conn->error);
              if ($res->num_rows > 0) {
                while($r = $res->fetch_assoc()) {
                  echo '<tr>';
                  echo '<td>'.$r['produto'].'</td>';
                  echo '<td>'.$r['quantidade'].'</td>';
                  echo '<td>'.date("d/m/Y", strtotime($r['data_entrada'])).'</td>';
                  echo '<td>'.date("d/m/Y", strtotime($r['validade'])).'</td>';
                  echo '<td>'.($r['responsavel'] ?? "Não informado").'</td>';
                  echo '<td>
                         <a class="btn btn-sm btn-warning me-2" href="editar_estoque.php?id='.$r['id'].'">Editar</a>
                          <a class="btn btn-sm btn-danger" href="excluir_estoque.php?id='.$r['id'].'" 
                          onclick="return confirm(\'Tem certeza que deseja excluir este item?\')">
                          Excluir
                         </a>
                       </td>';
                  echo '</tr>';
                }
              } else {
                echo '<tr><td colspan="6">Nenhum produto no estoque.</td></tr>';
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
