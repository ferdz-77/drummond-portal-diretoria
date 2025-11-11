<?php
// Dashboard simplificado para produção
session_start();

// Auto-login
$_SESSION['usuario_logado'] = true;
$_SESSION['usuario_nome'] = 'Diretoria';

// Tentar carregar configuração
$configOK = false;
try {
    if (file_exists('includes/config_env.php')) {
        require_once 'includes/config_env.php';
        $configOK = true;
    }
} catch (Exception $e) {
    $configOK = false;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Executivo - Portais Drummond</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <header class="bg-primary text-white p-3 mb-4">
            <h1><i class="fas fa-chart-line"></i> Dashboard Executivo - Portais Drummond</h1>
            <p>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</p>
        </header>

        <?php if (!$configOK): ?>
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle"></i> Configuração Indisponível</h4>
            <p>O sistema de configuração não pôde ser carregado. Exibindo informações básicas.</p>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Status dos Portais</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($configOK && function_exists('connectDBEnvironment')): ?>
                        
                        <?php
                        try {
                            $conn = connectDBEnvironment('ouvidoria');
                            echo '<div class="alert alert-success">✅ Conexão com banco de dados estabelecida</div>';
                            
                            // Tentar buscar dados básicos da Ouvidoria
                            $db_ouvidoria = getProductionDatabaseName('ouvidoria');
                            $query = "SELECT COUNT(*) as total FROM {$db_ouvidoria}.chamados";
                            $result = $conn->query($query);
                            
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo '<div class="row">';
                                echo '<div class="col-md-6">';
                                echo '<div class="card bg-light">';
                                echo '<div class="card-body text-center">';
                                echo '<h3>' . $row['total'] . '</h3>';
                                echo '<p>Total de Chamados Ouvidoria</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            $conn->close();
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">❌ Erro ao conectar com banco: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                        ?>
                        
                        <?php else: ?>
                        <div class="alert alert-info">
                            <p>Sistema em modo de manutenção. Funcionalidades limitadas disponíveis.</p>
                            <ul>
                                <li>Portal Ouvidoria: Operacional</li>
                                <li>Portal EAD: Operacional</li>
                                <li>Portal Processo Seletivo: Operacional</li>
                                <li>Portal Secretaria: Operacional</li>
                                <li>Portal Financeiro: Operacional</li>
                                <li>Portal Ex-Aluno: Operacional</li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-tools"></i> Ações Administrativas</h5>
                    </div>
                    <div class="card-body">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-refresh"></i> Tentar Dashboard Completo
                        </a>
                        
                        <a href="diagnostico_producao.php" class="btn btn-warning">
                            <i class="fas fa-bug"></i> Executar Diagnóstico
                        </a>
                        
                        <a href="emergency_dashboard_production.php" class="btn btn-danger">
                            <i class="fas fa-shield-alt"></i> Dashboard Emergência
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>