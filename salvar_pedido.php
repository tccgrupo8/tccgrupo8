<?php
// salvar_pedido.php
session_start();
include 'conexao.php';
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: index.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pedidos.php'); exit;
}

$cliente = trim($_POST['cliente'] ?? '');
$mesa = trim($_POST['mesa'] ?? '');
$produtos = $_POST['produtos'] ?? []; // array de ids
$quantidades = $_POST['quantidades'] ?? []; // caso envie quantidades associadas

if (empty($cliente) || empty($mesa) || empty($produtos)) {
    echo "<script>alert('Dados incompletos.'); window.history.back();</script>";
    exit;
}

$funcionario_id = intval($_SESSION['funcionario_id']);

// inicia transação
$conn->begin_transaction();

try {
    // insere pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (cliente, mesa, funcionario_id, status, criado_em) VALUES (?, ?, ?, 'Pendente', NOW())");
    $stmt->bind_param("ssi", $cliente, $mesa, $funcionario_id);
    $stmt->execute();
    $pedido_id = $conn->insert_id;
    $stmt->close();

    // busca preços dos produtos selecionados para garantir consistência
    // cria placeholders
    $placeholders = implode(',', array_fill(0, count($produtos), '?'));
    $types = str_repeat('i', count($produtos));
    $sql = "SELECT id, preco FROM produtos WHERE id IN ($placeholders)";
    $stmtP = $conn->prepare($sql);
    // bind dinâmico
    $stmtP->bind_param($types, ...array_map('intval', $produtos));
    $stmtP->execute();
    $res = $stmtP->get_result();
    $precos = [];
    while ($r = $res->fetch_assoc()) $precos[$r['id']] = $r['preco'];
    $stmtP->close();

    // insere itens_pedido
    $sql_item = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco, status_item) VALUES (?, ?, ?, ?, 'aguardando')";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($produtos as $pid) {
        $pid = intval($pid);
        $qtd = 1;
        if (isset($quantidades[$pid])) $qtd = max(1, intval($quantidades[$pid]));
        $preco = isset($precos[$pid]) ? floatval($precos[$pid]) : 0.0;
        $stmt_item->bind_param("iiid", $pedido_id, $pid, $qtd, $preco);
        $stmt_item->execute();
    }
    $stmt_item->close();

    $conn->commit();

    header("Location: pedidos.php?msg=criado");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Erro ao salvar pedido: " . $e->getMessage());
}
