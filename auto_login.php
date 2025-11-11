<?php
session_start();
$_SESSION['usuario_logado'] = true;
$_SESSION['usuario_nome'] = 'Admin';
echo "Login realizado com sucesso!<br>";
echo "<a href='dashboard.php'>Ir para o Dashboard</a>";
?>