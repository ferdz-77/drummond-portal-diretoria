<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Se estiver logado, redirecionar para dashboard
header('Location: dashboard.php');
exit;
?>

    header('Location: login.php');require_once 'data/data_ouvidoria.php';

    exit;require_once 'data/data_ead.php';

}require_once 'data/data_processo_seletivo.php';

require_once 'data/data_secretaria.php';

// Se estiver logado, redirecionar para dashboardrequire_once 'data/data_financeiro.php';

header('Location: dashboard.php');require_once 'data/data_exaluno.php';

exit;

?>// Buscar dados para cada portal
$ouvidoria_status = getChamadosStatusOuvidoria();
$ouvidoria_sla = getSLAMedioOuvidoria();
$ouvidoria_tipos = getTiposManifestacaoOuvidoria();

$ead_status = getChamadosStatusEAD();
$ead_sla = getSLAMedioEAD();
$ead_servicos = getServicosSolicitadosEAD();

// Similar para outros...
$processo_status = getChamadosStatusProcessoSeletivo();
$processo_sla = getSLAMedioProcessoSeletivo();
$processo_servicos = getServicosSolicitadosProcessoSeletivo();

$secretaria_status = getSolicitacoesStatusSecretaria();
$secretaria_tempo = getTempoMedioSecretaria();
$secretaria_servicos = getServicosSolicitadosSecretaria();

$financeiro_status = getChamadosStatusFinanceiro();
$financeiro_sla = getSLAMedioFinanceiro();

$exaluno_status = getChamadosStatusExAluno();
$exaluno_sla = getSLAMedioExAluno();
$exaluno_servicos = getServicosSolicitadosExAluno();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Executivo - Portais Drummond</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Dashboard Executivo - Portais Drummond</h1>
    </header>

    <main>
        <section id="consolidado">
            <h2>Visão Consolidada</h2>
            <canvas id="chartConsolidado"></canvas>
        </section>

        <section id="ouvidoria">
            <h2>Portal da Ouvidoria</h2>
            <canvas id="chartOuvidoriaStatus"></canvas>
            <p>SLA Médio: <?php echo $ouvidoria_sla; ?> dias</p>
            <canvas id="chartOuvidoriaTipos"></canvas>
        </section>

        <section id="ead">
            <h2>Portal do EAD</h2>
            <canvas id="chartEADStatus"></canvas>
            <p>SLA Médio: <?php echo $ead_sla; ?> dias</p>
            <canvas id="chartEADServicos"></canvas>
        </section>

        <!-- Similar para outros portais -->
        <section id="processo">
            <h2>Portal do Processo Seletivo</h2>
            <canvas id="chartProcessoStatus"></canvas>
            <p>SLA Médio: <?php echo $processo_sla; ?> dias</p>
            <canvas id="chartProcessoServicos"></canvas>
        </section>

        <section id="secretaria">
            <h2>Portal da Secretaria Acadêmica</h2>
            <canvas id="chartSecretariaStatus"></canvas>
            <p>Tempo Médio: <?php echo $secretaria_tempo; ?> dias</p>
            <canvas id="chartSecretariaServicos"></canvas>
        </section>

        <section id="financeiro">
            <h2>Portal Financeiro</h2>
            <canvas id="chartFinanceiroStatus"></canvas>
            <p>SLA Médio: <?php echo $financeiro_sla; ?> dias</p>
        </section>

        <section id="exaluno">
            <h2>Portal do Ex-Aluno</h2>
            <canvas id="chartExAlunoStatus"></canvas>
            <p>SLA Médio: <?php echo $exaluno_sla; ?> dias</p>
            <canvas id="chartExAlunoServicos"></canvas>
        </section>
    </main>

    <script src="js/dashboard.js"></script>
    <script>
        // Passar dados PHP para JS
        var ouvidoriaStatus = <?php echo json_encode($ouvidoria_status); ?>;
        var ouvidoriaSLA = <?php echo $ouvidoria_sla; ?>;
        var ouvidoriaTipos = <?php echo json_encode($ouvidoria_tipos); ?>;

        var eadStatus = <?php echo json_encode($ead_status); ?>;
        var eadSLA = <?php echo $ead_sla; ?>;
        var eadServicos = <?php echo json_encode($ead_servicos); ?>;

        var processoStatus = <?php echo json_encode($processo_status); ?>;
        var processoSLA = <?php echo $processo_sla; ?>;
        var processoServicos = <?php echo json_encode($processo_servicos); ?>;

        var secretariaStatus = <?php echo json_encode($secretaria_status); ?>;
        var secretariaTempo = <?php echo $secretaria_tempo; ?>;
        var secretariaServicos = <?php echo json_encode($secretaria_servicos); ?>;

        var financeiroStatus = <?php echo json_encode($financeiro_status); ?>;
        var financeiroSLA = <?php echo $financeiro_sla; ?>;

        var exalunoStatus = <?php echo json_encode($exaluno_status); ?>;
        var exalunoSLA = <?php echo $exaluno_sla; ?>;
        var exalunoServicos = <?php echo json_encode($exaluno_servicos); ?>;
    </script>
</body>
</html>