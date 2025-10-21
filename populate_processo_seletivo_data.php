<?php
require_once 'includes/config.php';

// Conectar ao banco do Processo Seletivo
try {
    $conn_processo = new mysqli($host, $user, $password, $dbname_processo_seletivo);
    if ($conn_processo->connect_error) {
        die("Erro de conexão: " . $conn_processo->connect_error);
    }

    echo "<h1>🎯 Adicionar Dados de Exemplo - Portal Processo Seletivo</h1>";

    // Verificar quantos registros existem atualmente
    $sql = "SELECT COUNT(*) as total FROM chamados";
    $result = $conn_processo->query($sql);
    $total_atual = $result->fetch_assoc()['total'];
    echo "<p>📊 <strong>Registros atuais:</strong> $total_atual</p>";

    // Definir dados de exemplo realistas para o Portal Processo Seletivo
    $dados_exemplo = [
        [
            'protocolo' => 'PS001',
            'senha_acesso' => 'ps001',
            'nome_completo' => 'Ana Silva Santos',
            'email' => 'ana.santos@email.com',
            'estado' => 'SP',
            'cidade' => 'São Paulo',
            'telefone' => '11988881234',
            'unidade_campus' => 'Campus São Paulo',
            'perfil' => 'lead',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Informações de valores',
            'subcategoria' => null,
            'descricao' => 'Gostaria de saber os valores dos cursos de Administração',
            'status' => 'em análise',
            'nivel_ensino' => 'faculdade',
            'ra' => null,
            'curso' => 'Administração'
        ],
        [
            'protocolo' => 'PS002',
            'senha_acesso' => 'ps002',
            'nome_completo' => 'Carlos Oliveira',
            'email' => 'carlos.oliveira@email.com',
            'estado' => 'RJ',
            'cidade' => 'Rio de Janeiro',
            'telefone' => '21977772345',
            'unidade_campus' => 'Campus Rio',
            'perfil' => 'lead',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Matrícula (Novos Alunos)',
            'subcategoria' => 'Link para matrícula',
            'descricao' => 'Como faço para me matricular no curso de Direito?',
            'status' => 'respondido',
            'nivel_ensino' => 'faculdade',
            'ra' => null,
            'curso' => 'Direito'
        ],
        [
            'protocolo' => 'PS003',
            'senha_acesso' => 'ps003',
            'nome_completo' => 'Mariana Costa',
            'email' => 'mariana.costa@email.com',
            'estado' => 'MG',
            'cidade' => 'Belo Horizonte',
            'telefone' => '31966663456',
            'unidade_campus' => 'Campus BH',
            'perfil' => 'ex-aluno',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Reabertura de Matrícula (Ex-alunos)',
            'subcategoria' => 'Processo de reabertura',
            'descricao' => 'Gostaria de reabrir minha matrícula no curso de Pedagogia',
            'status' => 'finalizado',
            'nivel_ensino' => 'faculdade',
            'ra' => '201800123',
            'curso' => 'Pedagogia'
        ],
        [
            'protocolo' => 'PS004',
            'senha_acesso' => 'ps004',
            'nome_completo' => 'Roberto Ferreira',
            'email' => 'roberto.ferreira@email.com',
            'estado' => 'RS',
            'cidade' => 'Porto Alegre',
            'telefone' => '51955554567',
            'unidade_campus' => 'Campus Porto Alegre',
            'perfil' => 'lead',
            'manifestacao' => 'consulta',
            'categoria' => 'Análise de Grade',
            'subcategoria' => null,
            'descricao' => 'Preciso saber quais disciplinas serão aproveitadas do meu curso anterior',
            'status' => 'Atribuído',
            'nivel_ensino' => 'faculdade',
            'ra' => null,
            'curso' => 'Engenharia Civil'
        ],
        [
            'protocolo' => 'PS005',
            'senha_acesso' => 'ps005',
            'nome_completo' => 'Juliana Almeida',
            'email' => 'juliana.almeida@email.com',
            'estado' => 'SP',
            'cidade' => 'Campinas',
            'telefone' => '19944445678',
            'unidade_campus' => 'Campus Campinas',
            'perfil' => 'lead',
            'manifestacao' => 'elogio',
            'categoria' => 'Informações de valores',
            'subcategoria' => null,
            'descricao' => 'Excelente atendimento da equipe de vendas!',
            'status' => 'respondido',
            'nivel_ensino' => 'faculdade',
            'ra' => null,
            'curso' => 'Psicologia'
        ],
        [
            'protocolo' => 'PS006',
            'senha_acesso' => 'ps006',
            'nome_completo' => 'Fernando Santos',
            'email' => 'fernando.santos@email.com',
            'estado' => 'SP',
            'cidade' => 'Santos',
            'telefone' => '13933336789',
            'unidade_campus' => 'Campus Santos',
            'perfil' => 'ex-aluno',
            'manifestacao' => 'documentação',
            'categoria' => 'Reabertura de Matrícula (Ex-alunos)',
            'subcategoria' => '2ª via',
            'descricao' => 'Preciso da segunda via do meu histórico escolar',
            'status' => 'Transferido',
            'nivel_ensino' => 'colegio',
            'ra' => '201700456',
            'curso' => null
        ]
    ];

    echo "<h2>📝 Inserindo dados de exemplo...</h2>";

    $inserted = 0;
    foreach ($dados_exemplo as $dados) {
        // Verificar se o protocolo já existe
        $check_sql = "SELECT COUNT(*) as record_exists FROM chamados WHERE protocolo = ?";
        $check_stmt = $conn_processo->prepare($check_sql);
        $check_stmt->bind_param("s", $dados['protocolo']);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->fetch_assoc()['record_exists'];
        
        if ($exists == 0) {
            // Inserir o registro
            $insert_sql = "INSERT INTO chamados (
                protocolo, senha_acesso, nome_completo, email, estado, cidade, 
                telefone, unidade_campus, perfil, manifestacao, categoria, 
                subcategoria, descricao, status, nivel_ensino, ra, curso
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $insert_stmt = $conn_processo->prepare($insert_sql);
            $insert_stmt->bind_param("sssssssssssssssss", 
                $dados['protocolo'], $dados['senha_acesso'], $dados['nome_completo'],
                $dados['email'], $dados['estado'], $dados['cidade'], $dados['telefone'],
                $dados['unidade_campus'], $dados['perfil'], $dados['manifestacao'],
                $dados['categoria'], $dados['subcategoria'], $dados['descricao'],
                $dados['status'], $dados['nivel_ensino'], $dados['ra'], $dados['curso']
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
    $result = $conn_processo->query($sql);
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
    $result = $conn_processo->query($sql);
    
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
    $result = $conn_processo->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Status</th><th>Quantidade</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['status']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";

    echo "<h2>🎯 Próximos Passos</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50;'>";
    echo "<p><strong>Dados inseridos com sucesso!</strong></p>";
    echo "<p>Agora os gráficos do Portal Processo Seletivo mostrarão:</p>";
    echo "<ul>";
    echo "<li>📊 <strong>4 categorias diferentes</strong>: Informações de valores, Matrícula (Novos Alunos), Reabertura de Matrícula (Ex-alunos), Análise de Grade</li>";
    echo "<li>📈 <strong>6 status variados</strong>: em análise, respondido, finalizado, Atribuído, Transferido, Não informado</li>";
    echo "<li>🥧 <strong>Gráficos pizza mais informativos</strong></li>";
    echo "</ul>";
    echo "<p><a href='dashboard.php?periodo=todos&portal=processo_seletivo' target='_blank'>🔗 Visualizar Dashboard Processo Seletivo Atualizado</a></p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn_processo)) {
        $conn_processo->close();
    }
}
?>