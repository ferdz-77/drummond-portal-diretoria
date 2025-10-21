<?php
require_once 'includes/config.php';
require_once 'data/data_ouvidoria.php';
require_once 'data/data_ead.php';
require_once 'data/data_processo_seletivo.php';
require_once 'data/data_secretaria.php';
require_once 'data/data_financeiro.php';
require_once 'data/data_exaluno.php';

echo "Valores do mês anterior:\n";
echo "Ouvidoria: " . getSLAMedioOuvidoriaMesAnterior() . "\n";
echo "EAD: " . getSLAMedioEADMesAnterior() . "\n";
echo "Processo: " . getSLAMedioProcessoSeletivoMesAnterior() . "\n";
echo "Secretaria: " . getTempoMedioSecretariaMesAnterior() . "\n";
echo "Financeiro: " . getSLAMedioFinanceiroMesAnterior() . "\n";
echo "Ex-Aluno: " . getSLAMedioExAlunoMesAnterior() . "\n";
?>