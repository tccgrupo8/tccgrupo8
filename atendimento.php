<?php include 'conexao.php'; ?>
<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: inde.php');
    exit;
}
?>


<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Atendimento - Vira Copos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-dark bg-black sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand ms-2" href="painel.php">Vira Copos - Atendimento</a>
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
      <h1>🎧 Atendimento</h1>
      <?php
        $sql = "SELECT id, mesa, mensagem FROM atendimento ORDER BY id DESC";
        $res = $conn->query($sql) or die($conn->error);
        if ($res->num_rows > 0) {
          while($r = $res->fetch_assoc()) {
            echo '<div class="card mb-2"><div class="card-body"><strong>Mesa '.htmlspecialchars($r['mesa']).':</strong> '.htmlspecialchars($r['mensagem']).'</div></div>';
          }
        } else {
          echo '<p>Nenhuma solicitação de atendimento.</p>';
        }
      ?>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
