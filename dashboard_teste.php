<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

echo "<h1>Dashboard Teste Simples</h1>";
echo "<p>Usuário logado: " . ($_SESSION['usuario_nome'] ?? 'N/A') . "</p>";
echo "<p>Sessão ativa: SIM</p>";
echo "<p>Data: " . date('Y-m-d H:i:s') . "</p>";
?>