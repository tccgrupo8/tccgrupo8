<?php
session_start();
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php');
    exit;
}
?>

<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id'])) { 
        header('Location: estoque.php'); 
        exit; 
    }
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT id, produto, quantidade, validade FROM estoque WHERE id = $id") or die($conn->error);
    if ($res->num_rows === 0) { 
        header('Location: estoque.php'); 
        exit; 
    }
    $r = $res->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $quant = $conn->real_escape_string($_POST['quantidade']);
    $validade = $conn->real_escape_string($_POST['validade']);
    $responsavel_id = $_SESSION['funcionario_id'];

    // Atualiza quantidade, validade, data_entrada e responsável
    $sql = "UPDATE estoque 
            SET quantidade = '$quant', 
                validade = '$validade', 
                data_entrada = CURRENT_DATE, 
                responsavel_id = '$responsavel_id'
            WHERE id = $id";
    
    if ($conn->query($sql)) {
        header('Location: estoque.php');
        exit;
    } else {
        die("Erro ao atualizar: " . $conn->error);
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Estoque</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container py-4">
  <h1>Editar Estoque</h1>
  <form method="post">
    <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
    <div class="mb-3">
      <label class="form-label">Produto</label>
      <input class="form-control" value="<?php echo htmlspecialchars($r['produto']); ?>" disabled>
    </div>
    <div class="mb-3">
      <label class="form-label">Quantidade</label>
      <input name="quantidade" class="form-control" value="<?php echo htmlspecialchars($r['quantidade']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Validade</label>
      <input type="date" name="validade" class="form-control" value="<?php echo htmlspecialchars($r['validade']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Validade</label>
      <input type="date" name="validade" class="form-control" value="<?php echo htmlspecialchars($r['validade']); ?>" required>
    </div>
    <button class="btn btn-primary">Salvar</button>
    <a class="btn btn-secondary" href="estoque.php">Cancelar</a>
  </form>
</div>
</body>
</html>
