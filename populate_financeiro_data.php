<?php
require_once 'includes/config.php';

// Conectar ao banco do Financeiro
try {
    $conn_financeiro = new mysqli($host, $user, $password, $dbname_financeiro);
    if ($conn_financeiro->connect_error) {
        die("Erro de conexão: " . $conn_financeiro->connect_error);
    }

    echo "<h1>🎯 Adicionar Dados de Exemplo - Portal Financeiro</h1>";

    // Verificar quantos registros existem atualmente
    $sql = "SELECT COUNT(*) as total FROM chamados";
    $result = $conn_financeiro->query($sql);
    $total_atual = $result->fetch_assoc()['total'];
    echo "<p>📊 <strong>Registros atuais:</strong> $total_atual</p>";

    // Definir dados de exemplo realistas para o Portal Financeiro
    $dados_exemplo = [
        [
            'protocolo' => 'FIN001',
            'senha_acesso' => 'fin001',
            'nome_completo' => 'Maria Fernanda Silva',
            'email' => 'maria.fernanda@email.com',
            'estado' => 'SP',
            'cidade' => 'São Paulo',
            'telefone' => '11987654321',
            'unidade_campus' => 'Campus São Paulo',
            'perfil' => 'aluno',
            'ra' => '202401001',
            'manifestacao' => 'Solicitação',
            'categoria' => 'TOTVS',
            'subcategoria' => 'Boleto de Acordo',
            'descricao' => 'Preciso do boleto para pagamento do acordo parcelado',
            'status' => 'em análise',
            'nivel_ensino' => 'faculdade',
            'curso' => 'Administração'
        ],
        [
            'protocolo' => 'FIN002',
            'senha_acesso' => 'fin002',
            'nome_completo' => 'João Carlos Santos',
            'email' => 'joao.carlos@email.com',
            'estado' => 'RJ',
            'cidade' => 'Rio de Janeiro',
            'telefone' => '21976543210',
            'unidade_campus' => 'Campus Rio',
            'perfil' => 'aluno',
            'ra' => '202401002',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Principia',
            'subcategoria' => '2ª via histórico',
            'descricao' => 'Solicito segunda via do histórico escolar para processo de transferência',
            'status' => 'respondido',
            'nivel_ensino' => 'faculdade',
            'curso' => 'Direito'
        ],
        [
            'protocolo' => 'FIN003',
            'senha_acesso' => 'fin003',
            'nome_completo' => 'Ana Paula Costa',
            'email' => 'ana.paula@email.com',
            'estado' => 'MG',
            'cidade' => 'Belo Horizonte',
            'telefone' => '31965432109',
            'unidade_campus' => 'Campus BH',
            'perfil' => 'aluno',
            'ra' => '202401003',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Rematrícula',
            'subcategoria' => 'Solicitação, Planos e Anuidade',
            'descricao' => 'Gostaria de informações sobre rematrícula e planos de pagamento',
            'status' => 'finalizado',
            'nivel_ensino' => 'faculdade',
            'curso' => 'Engenharia'
        ],
        [
            'protocolo' => 'FIN004',
            'senha_acesso' => 'fin004',
            'nome_completo' => 'Carlos Eduardo Oliveira',
            'email' => 'carlos.eduardo@email.com',
            'estado' => 'RS',
            'cidade' => 'Porto Alegre',
            'telefone' => '51954321098',
            'unidade_campus' => 'Campus Porto Alegre',
            'perfil' => 'ex-aluno',
            'ra' => '201901004',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Principia',
            'subcategoria' => '2ª via diploma',
            'descricao' => 'Preciso da segunda via do diploma para processo trabalhista',
            'status' => 'Atribuído',
            'nivel_ensino' => 'faculdade',
            'curso' => 'Psicologia'
        ],
        [
            'protocolo' => 'FIN005',
            'senha_acesso' => 'fin005',
            'nome_completo' => 'Lucia Aparecida Ferreira',
            'email' => 'lucia.ferreira@email.com',
            'estado' => 'SP',
            'cidade' => 'Campinas',
            'telefone' => '19943210987',
            'unidade_campus' => 'Campus Campinas',
            'perfil' => 'aluno',
            'ra' => '202401005',
            'manifestacao' => 'reclamação',
            'categoria' => 'TOTVS',
            'subcategoria' => 'Data vencimento',
            'descricao' => 'A data de vencimento do meu boleto não está adequada à minha situação financeira',
            'status' => 'respondido',
            'nivel_ensino' => 'faculdade',
            'curso' => 'Pedagogia'
        ],
        [
            'protocolo' => 'FIN006',
            'senha_acesso' => 'fin006',
            'nome_completo' => 'Roberto Almeida Santos',
            'email' => 'roberto.almeida@email.com',
            'estado' => 'SP',
            'cidade' => 'Santos',
            'telefone' => '13932109876',
            'unidade_campus' => 'Campus Santos',
            'perfil' => 'ex-aluno',
            'ra' => '201801006',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Principia',
            'subcategoria' => 'Declaração IR',
            'descricao' => 'Solicito declaração para Imposto de Renda referente às mensalidades pagas',
            'status' => 'Transferido',
            'nivel_ensino' => 'colegio',
            'curso' => null
        ],
        [
            'protocolo' => 'FIN007',
            'senha_acesso' => 'fin007',
            'nome_completo' => 'Fernanda Lima',
            'email' => 'fernanda.lima@email.com',
            'estado' => 'SP',
            'cidade' => 'São Paulo',
            'telefone' => '11921098765',
            'unidade_campus' => 'Campus São Paulo',
            'perfil' => 'aluno',
            'ra' => '202401007',
            'manifestacao' => 'elogio',
            'categoria' => 'TOTVS',
            'subcategoria' => 'Negociação',
            'descricao' => 'Excelente atendimento da equipe financeira na negociação da minha dívida',
            'status' => 'respondido',
            'nivel_ensino' => 'faculdade',
            'curso' => 'Nutrição'
        ]
    ];

    echo "<h2>📝 Inserindo dados de exemplo...</h2>";

    $inserted = 0;
    foreach ($dados_exemplo as $dados) {
        // Verificar se o protocolo já existe
        $check_sql = "SELECT COUNT(*) as record_exists FROM chamados WHERE protocolo = ?";
        $check_stmt = $conn_financeiro->prepare($check_sql);
        $check_stmt->bind_param("s", $dados['protocolo']);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->fetch_assoc()['record_exists'];
        
        if ($exists == 0) {
            // Inserir o registro
            $insert_sql = "INSERT INTO chamados (
                protocolo, senha_acesso, nome_completo, email, estado, cidade, 
                telefone, unidade_campus, perfil, ra, manifestacao, categoria, 
                subcategoria, descricao, status, nivel_ensino, curso
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $insert_stmt = $conn_financeiro->prepare($insert_sql);
            $insert_stmt->bind_param("sssssssssssssssss", 
                $dados['protocolo'], $dados['senha_acesso'], $dados['nome_completo'],
                $dados['email'], $dados['estado'], $dados['cidade'], $dados['telefone'],
                $dados['unidade_campus'], $dados['perfil'], $dados['ra'],
                $dados['manifestacao'], $dados['categoria'], $dados['subcategoria'],
                $dados['descricao'], $dados['status'], $dados['nivel_ensino'], $dados['curso']
            );
            
            if ($insert_stmt->execute()) {
                echo "<p>✅ Inserido: {$dados['protocolo']} - {$dados['nome_completo']}</p>";
                $inserted++;
            } else {
                echo "<p>❌ Erro ao inserir {$dados['protocolo']}: " . $insert_stmt->error . "</p>";
            }
        } else {
            echo "<p>⚠️ Protocolo {$dados['protocolo']} já existe, pulando...</p>";
        }
    }

    echo "<h2>📊 Resumo da Inserção</h2>";
    echo "<p><strong>Registros inseridos:</strong> $inserted</p>";

    // Verificar dados após inserção
    $sql = "SELECT COUNT(*) as total FROM chamados";
    $result = $conn_financeiro->query($sql);
    $total_final = $result->fetch_assoc()['total'];
    echo "<p><strong>Total de registros após inserção:</strong> $total_final</p>";

    // Mostrar distribuição por categoria
    echo "<h3>🏷️ Distribuição por Categoria</h3>";
    $sql = "SELECT 
                CASE 
                    WHEN categoria = '' OR categoria IS NULL THEN 'Categoria não informada'
                    ELSE categoria 
                END as categoria,
                COUNT(*) as count
            FROM chamados 
            GROUP BY categoria
            ORDER BY count DESC";
    $result = $conn_financeiro->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Categoria</th><th>Quantidade</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['categoria']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";

    // Mostrar distribuição por status
    echo "<h3>📊 Distribuição por Status</h3>";
    $sql = "SELECT 
                CASE 
                    WHEN status = '' OR status IS NULL THEN 'Não informado'
                    ELSE status 
                END as status,
                COUNT(*) as count
            FROM chamados 
            GROUP BY status
            ORDER BY count DESC";
    $result = $conn_financeiro->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Status</th><th>Quantidade</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['status']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";

    echo "<h2>🎯 Próximos Passos</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50;'>";
    echo "<p><strong>Dados inseridos com sucesso!</strong></p>";
    echo "<p>Agora os gráficos do Portal Financeiro mostrarão:</p>";
    echo "<ul>";
    echo "<li>📊 <strong>3 categorias principais</strong>: TOTVS, Principia, Rematrícula</li>";
    echo "<li>📈 <strong>6 status variados</strong>: em análise, respondido, finalizado, Atribuído, Transferido, Não informado</li>";
    echo "<li>🥧 <strong>Gráficos pizza mais informativos</strong></li>";
    echo "<li>💰 <strong>Contexto financeiro realista</strong>: boletos, declarações, negociações, etc.</li>";
    echo "</ul>";
    echo "<p><a href='dashboard.php?periodo=todos&portal=financeiro' target='_blank'>🔗 Visualizar Dashboard Financeiro Atualizado</a></p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn_financeiro)) {
        $conn_financeiro->close();
    }
}
?>