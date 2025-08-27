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
  <title>Painel do Funcionário - Vira Copos</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Navbar / Offcanvas for mobile -->
<nav class="navbar navbar-dark bg-black sticky-top">
  <div class="container-fluid">
    <button class="btn btn-danger d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
      ☰
    </button>
    <a class="navbar-brand ms-2" href="#">Vira Copos - Funcionário</a>
  </div>
</nav>

<div class="d-flex">
  <!-- Sidebar -->
  <aside class="sidebar bg-black text-white d-none d-md-block">
    <div class="p-3">
      <h4>Funcionário</h4>
      <hr class="text-white">
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link text-white" href="painel.php">🏠 Painel</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="pedidos.php">📦 Pedidos</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="estoque.php">📊 Estoque</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="atendimento.php">🎧 Atendimento</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="login.php">🚪 Sair</a></li>
      </ul>
    </div>
  </aside>

  <!-- Offcanvas (mobile) -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu">
    <div class="offcanvas-header bg-danger text-white">
      <h5 class="offcanvas-title">Menu</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="painel.php">🏠 Painel</a></li>
        <li class="nav-item"><a class="nav-link" href="pedidos.php">📦 Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="estoque.php">📊 Estoque</a></li>
        <li class="nav-item"><a class="nav-link" href="atendimento.php">🎧 Atendimento</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="index.php">🚪 Sair</a></li>
      </ul>
    </div>
  </div>

  <!-- Main content -->
  <main class="content p-4">
    <div class="container-fluid">
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">📦 Pedidos pendentes</h5>
              <?php
                $res = $conn->query("SELECT COUNT(*) as c FROM pedidos WHERE status = 'Pendente'") or die($conn->error);
                $row = $res->fetch_assoc();
                echo '<p class="display-6">'.($row['c'] ?? 0).'</p>';
              ?>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">📊 Produtos no estoque</h5>
              <?php
                $res = $conn->query("SELECT COUNT(*) as c FROM estoque") or die($conn->error);
                $row = $res->fetch_assoc();
                echo '<p class="display-6">'.($row['c'] ?? 0).'</p>';
              ?>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">🎧 Chamados</h5>
              <?php
                $res = $conn->query("SELECT COUNT(*) as c FROM atendimento") or die($conn->error);
                $row = $res->fetch_assoc();
                echo '<p class="display-6">'.($row['c'] ?? 0).'</p>';
              ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5>Bem-vindo ao painel do funcionário</h5>
              <p>Use o menu para navegar entre Pedidos, Estoque e Atendimento.</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
