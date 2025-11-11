<?php
// Teste de requisição POST para get_filtered_data.php
session_start();
$_SESSION['usuario_logado'] = true;

// Simular requisição POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['portal'] = 'todos';
$_POST['periodo'] = 'todos';
$_POST['acao'] = 'get_chamados';
$_POST['status'] = 'todos';
$_POST['pagina'] = '1';
$_POST['limite'] = '10';

echo "Testando requisição POST...<br>";

try {
    require_once 'get_filtered_data.php';
    echo "Requisição processada com sucesso<br>";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}
?>