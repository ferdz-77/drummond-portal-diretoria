<?php
// Script de emergência para testar dashboard com dados mock
echo "🚨 DASHBOARD DE EMERGÊNCIA - DADOS MOCK\n";
echo "=======================================\n\n";

echo "⚠️  Este dashboard mostra dados simulados enquanto as conexões são corrigidas\n\n";

// Simular dados para cada portal
$mockData = [
    'ouvidoria' => [
        'total' => 45,
        'abertos' => 12,
        'fechados' => 33,
        'sla_medio' => 2.3,
        'tipos' => [
            'Reclamação' => 20,
            'Sugestão' => 15,
            'Elogio' => 10
        ]
    ],
    'ead' => [
        'total' => 78,
        'abertos' => 25,
        'fechados' => 53,
        'sla_medio' => 1.8,
        'categorias' => [
            'Matrícula' => 30,
            'Financeiro' => 25,
            'Técnico' => 23
        ]
    ],
    'processo_seletivo' => [
        'total' => 156,
        'abertos' => 45,
        'fechados' => 111,
        'sla_medio' => 3.1,
        'categorias' => [
            'Inscrição' => 80,
            'Documentação' => 45,
            'Resultado' => 31
        ]
    ],
    'secretaria' => [
        'total' => 92,
        'abertos' => 18,
        'fechados' => 74,
        'sla_medio' => 1.5,
        'categorias' => [
            'Histórico' => 35,
            'Diploma' => 30,
            'Transferência' => 27
        ]
    ],
    'financeiro' => [
        'total' => 67,
        'abertos' => 15,
        'fechados' => 52,
        'sla_medio' => 2.7,
        'categorias' => [
            'Mensalidade' => 40,
            'Bolsa' => 15,
            'Reembolso' => 12
        ]
    ],
    'exaluno' => [
        'total' => 23,
        'abertos' => 8,
        'fechados' => 15,
        'sla_medio' => 4.2,
        'tipos' => [
            'Certificado' => 12,
            'Histórico' => 8,
            'Transferência' => 3
        ]
    ]
];

echo "📊 DASHBOARD - PORTAL DA DIRETORIA\n";
echo "==================================\n\n";

echo "🏥 OUVIDORIA\n";
echo "- Total de chamados: {$mockData['ouvidoria']['total']}\n";
echo "- Abertos: {$mockData['ouvidoria']['abertos']}\n";
echo "- Fechados: {$mockData['ouvidoria']['fechados']}\n";
echo "- SLA Médio: {$mockData['ouvidoria']['sla_medio']} dias\n";
echo "- Tipos: " . implode(', ', array_keys($mockData['ouvidoria']['tipos'])) . "\n\n";

echo "🎓 EAD (Educação a Distância)\n";
echo "- Total de chamados: {$mockData['ead']['total']}\n";
echo "- Abertos: {$mockData['ead']['abertos']}\n";
echo "- Fechados: {$mockData['ead']['fechados']}\n";
echo "- SLA Médio: {$mockData['ead']['sla_medio']} dias\n";
echo "- Categorias: " . implode(', ', array_keys($mockData['ead']['categorias'])) . "\n\n";

echo "📝 PROCESSO SELETIVO\n";
echo "- Total de chamados: {$mockData['processo_seletivo']['total']}\n";
echo "- Abertos: {$mockData['processo_seletivo']['abertos']}\n";
echo "- Fechados: {$mockData['processo_seletivo']['fechados']}\n";
echo "- SLA Médio: {$mockData['processo_seletivo']['sla_medio']} dias\n";
echo "- Categorias: " . implode(', ', array_keys($mockData['processo_seletivo']['categorias'])) . "\n\n";

echo "🏛️ SECRETARIA ACADÊMICA\n";
echo "- Total de chamados: {$mockData['secretaria']['total']}\n";
echo "- Abertos: {$mockData['secretaria']['abertos']}\n";
echo "- Fechados: {$mockData['secretaria']['fechados']}\n";
echo "- SLA Médio: {$mockData['secretaria']['sla_medio']} dias\n";
echo "- Categorias: " . implode(', ', array_keys($mockData['secretaria']['categorias'])) . "\n\n";

echo "💰 FINANCEIRO\n";
echo "- Total de chamados: {$mockData['financeiro']['total']}\n";
echo "- Abertos: {$mockData['financeiro']['abertos']}\n";
echo "- Fechados: {$mockData['financeiro']['fechados']}\n";
echo "- SLA Médio: {$mockData['financeiro']['sla_medio']} dias\n";
echo "- Categorias: " . implode(', ', array_keys($mockData['financeiro']['categorias'])) . "\n\n";

echo "🎓 EX-ALUNO\n";
echo "- Total de chamados: {$mockData['exaluno']['total']}\n";
echo "- Abertos: {$mockData['exaluno']['abertos']}\n";
echo "- Fechados: {$mockData['exaluno']['fechados']}\n";
echo "- SLA Médio: {$mockData['exaluno']['sla_medio']} dias\n";
echo "- Tipos: " . implode(', ', array_keys($mockData['exaluno']['tipos'])) . "\n\n";

echo "📋 RESUMO GERAL\n";
$totalGeral = array_sum(array_column($mockData, 'total'));
$abertosGeral = array_sum(array_column($mockData, 'abertos'));
$fechadosGeral = array_sum(array_column($mockData, 'fechados'));
$slaMedioGeral = round(array_sum(array_column($mockData, 'sla_medio')) / count($mockData), 1);

echo "- Total de chamados: $totalGeral\n";
echo "- Abertos: $abertosGeral\n";
echo "- Fechados: $fechadosGeral\n";
echo "- SLA Médio Geral: $slaMedioGeral dias\n\n";

echo "⚠️  STATUS: DADOS SIMULADOS\n";
echo "🔧 As conexões com os bancos reais estão sendo corrigidas.\n";
echo "📞 Contate o administrador do banco para verificar as credenciais.\n\n";

echo "🔗 LINKS ÚTEIS:\n";
echo "- Teste detalhado: https://gestao-protocolos.drummond.com.br/test_detailed_connections.php\n";
echo "- Debug produção: https://gestao-protocolos.drummond.com.br/debug_production.php\n";
?>