<?php
session_start();

function usuario_logado() {
    return isset($_SESSION['funcionario_id']);
}

function tipo() {
    return $_SESSION['funcionario_tipo'] ?? null;
}

function require_login() {
    if (!usuario_logado()) {
        header("Location: index.php");
        exit;
    }
}

function require_role($roles = []) {
    require_login();
    $t = tipo();
    if (!in_array($t, $roles)) {
        header("Location: acesso_negado.php");
        exit;
    }
}
?>