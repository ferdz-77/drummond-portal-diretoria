<?php
session_start();
$_SESSION['usuario_logado'] = true;

require_once 'includes/config.php';
require_once 'data/data_ouvidoria.php';
require_once 'data/data_ead.php';
require_once 'data/data_processo_seletivo.php';
require_once 'data/data_secretaria.php';
require_once 'data/data_financeiro.php';
require_once 'data/data_exaluno.php';

function calcularDiferencaPercentual($atual, $anterior) {
    if ($anterior == 0) {
        return $atual > 0 ? "+∞%" : "0%";
    }
    $diferenca = (($atual - $anterior) / $anterior) * 100;
    $sinal = $diferenca >= 0 ? "+" : "";
    return $sinal . round($diferenca, 1) . "%";
}

echo "<h1>🧪 Teste Simulação Dashboard</h1>";

// Simular exatamente o que o dashboard faz
$ouvidoria_sla = getSLAMedioOuvidoria();
$ouvidoria_sla_anterior = getSLAMedioOuvidoriaMesAnterior();

$ead_sla = getSLAMedioEAD();
$ead_sla_anterior = getSLAMedioEADMesAnterior();

$processo_sla = getSLAMedioProcessoSeletivo();
$processo_sla_anterior = getSLAMedioProcessoSeletivoMesAnterior();

$secretaria_tempo = getTempoMedioSecretaria();
$secretaria_tempo_anterior = getTempoMedioSecretariaMesAnterior();

$financeiro_sla = getSLAMedioFinanceiro();
$financeiro_sla_anterior = getSLAMedioFinanceiroMesAnterior();

$exaluno_sla = getSLAMedioExAluno();
$exaluno_sla_anterior = getSLAMedioExAlunoMesAnterior();

echo "<h2>Portal da Ouvidoria</h2>";
echo "<p>SLA Médio: $ouvidoria_sla dias</p>";
if ($ouvidoria_sla_anterior > 0) {
    echo "<p class='comparativo-mes'>". calcularDiferencaPercentual($ouvidoria_sla, $ouvidoria_sla_anterior) . " vs mês anterior</p>";
} else {
    echo "<p style='color:red'>Sem comparativo (anterior = $ouvidoria_sla_anterior)</p>";
}

echo "<h2>Portal do EAD</h2>";
echo "<p>SLA Médio: $ead_sla dias</p>";
if ($ead_sla_anterior > 0) {
    echo "<p class='comparativo-mes'>". calcularDiferencaPercentual($ead_sla, $ead_sla_anterior) . " vs mês anterior</p>";
} else {
    echo "<p style='color:red'>Sem comparativo (anterior = $ead_sla_anterior)</p>";
}

echo "<h2>Portal Processo Seletivo</h2>";
echo "<p>SLA Médio: $processo_sla dias</p>";
if ($processo_sla_anterior > 0) {
    echo "<p class='comparativo-mes'>". calcularDiferencaPercentual($processo_sla, $processo_sla_anterior) . " vs mês anterior</p>";
} else {
    echo "<p style='color:red'>Sem comparativo (anterior = $processo_sla_anterior)</p>";
}

echo "<h2>Portal Secretaria</h2>";
echo "<p>Tempo Médio: $secretaria_tempo dias</p>";
if ($secretaria_tempo_anterior > 0) {
    echo "<p class='comparativo-mes'>". calcularDiferencaPercentual($secretaria_tempo, $secretaria_tempo_anterior) . " vs mês anterior</p>";
} else {
    echo "<p style='color:red'>Sem comparativo (anterior = $secretaria_tempo_anterior)</p>";
}

echo "<h2>Portal Financeiro</h2>";
echo "<p>SLA Médio: $financeiro_sla dias</p>";
if ($financeiro_sla_anterior > 0) {
    echo "<p class='comparativo-mes'>". calcularDiferencaPercentual($financeiro_sla, $financeiro_sla_anterior) . " vs mês anterior</p>";
} else {
    echo "<p style='color:red'>Sem comparativo (anterior = $financeiro_sla_anterior)</p>";
}

echo "<h2>Portal Ex-Aluno</h2>";
echo "<p>SLA Médio: $exaluno_sla dias</p>";
if ($exaluno_sla_anterior > 0) {
    echo "<p class='comparativo-mes'>". calcularDiferencaPercentual($exaluno_sla, $exaluno_sla_anterior) . " vs mês anterior</p>";
} else {
    echo "<p style='color:red'>Sem comparativo (anterior = $exaluno_sla_anterior)</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Voltar ao Dashboard</a></p>";
?>