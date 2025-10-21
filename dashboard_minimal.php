<?php
session_start();
require_once 'config_env.php';

if (!isset($_SESSION['usuario_logado'])) {
    $_SESSION['usuario_logado'] = true;
    $_SESSION['usuario_nome'] = 'Admin';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Simplificado</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-header">
                <h3>Dashboard Executivo - Portais Drummond</h3>
            </div>
        </div>
    </header>

    <main>
        <!-- Abas de navegação -->
        <div class="tabs">
            <button class="tab-button active" data-tab="consolidado">Visão Consolidada</button>
            <button class="tab-button" data-tab="portais">Resumos dos Portais</button>
            <button class="tab-button" data-tab="chamados">Lista de Chamados</button>
        </div>

        <!-- Conteúdo das abas -->
        <div id="consolidado" class="tab-content active">
            <section id="visao-consolidada">
                <h2>Visão Consolidada</h2>
                <p>Conteúdo da primeira aba</p>
            </section>
        </div>

        <div id="portais" class="tab-content">
            <section>
                <h2>Resumos dos Portais</h2>
                <p>Conteúdo da segunda aba</p>
            </section>
        </div>

        <div id="chamados" class="tab-content">
            <section>
                <h2>Lista de Chamados</h2>
                <p>Conteúdo da terceira aba</p>
            </section>
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript carregado');
    
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    console.log('Elementos encontrados:', tabButtons.length, tabContents.length);

    tabButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            console.log('Clicando em:', targetTab);
            
            // Remove active
            tabButtons.forEach(function(btn) { btn.classList.remove('active'); });
            tabContents.forEach(function(content) { content.classList.remove('active'); });
            
            // Add active
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            
            console.log('Aba trocada para:', targetTab);
        });
    });
});
</script>

</body>
</html>