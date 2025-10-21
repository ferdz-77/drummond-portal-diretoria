<?php
// Funções auxiliares para filtros de período
// Este arquivo deve ser incluído pelos arquivos de dados

// Função para gerar cláusula WHERE baseada no período
function getPeriodWhereClause($periodo, $dataAberturaCol, $dataFechamentoCol = null) {
    if ($periodo === 'todos') {
        return '';
    }

    $now = date('Y-m-d H:i:s');
    $where = '';

    switch ($periodo) {
        case 'hoje':
            $where = "DATE($dataAberturaCol) = CURDATE()";
            break;
        case 'semana':
            $where = "YEARWEEK($dataAberturaCol, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'mes':
            $where = "MONTH($dataAberturaCol) = MONTH(CURDATE()) AND YEAR($dataAberturaCol) = YEAR(CURDATE())";
            break;
        case 'trimestre':
            $quarter = ceil(date('n') / 3);
            $year = date('Y');
            $where = "QUARTER($dataAberturaCol) = $quarter AND YEAR($dataAberturaCol) = $year";
            break;
        case 'ano':
            $where = "$dataAberturaCol >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            break;
    }

    return $where ? " AND $where" : '';
}
?>