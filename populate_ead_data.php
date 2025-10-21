<?php
require_once 'includes/config.php';

// Conectar ao banco do EAD
try {
    $conn_ead = new mysqli($host, $user, $password, $dbname_ead);
    if ($conn_ead->connect_error) {
        die("Erro de conexão: " . $conn_ead->connect_error);
    }

    echo "<h1>🎯 Adicionar Dados de Exemplo - Portal EAD</h1>";

    // Verificar quantos registros existem atualmente
    $sql = "SELECT COUNT(*) as total FROM chamados";
    $result = $conn_ead->query($sql);
    $total_atual = $result->fetch_assoc()['total'];
    echo "<p>📊 <strong>Registros atuais:</strong> $total_atual</p>";

    // Definir dados de exemplo realistas para o Portal EAD
    $dados_exemplo = [
        [
            'protocolo' => 'EAD001',
            'senha_acesso' => 'abc123',
            'nome_completo' => 'João Silva',
            'email' => 'joao@email.com',
            'estado' => 'SP',
            'cidade' => 'São Paulo',
            'telefone' => '11999998888',
            'unidade_campus' => 'Campus São Paulo',
            'perfil' => 'aluno',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Solicitações - Ambiente Virtual de Aprendizagem (AVA)',
            'subcategoria' => 'Acesso ao AVA - Login e Senha',
            'descricao' => 'Não consigo acessar o AVA com minha senha',
            'status' => 'em análise',
            'nivel_ensino' => 'faculdade',
            'ra' => '202300001',
            'curso' => 'Administração'
        ],
        [
            'protocolo' => 'EAD002',
            'senha_acesso' => 'def456',
            'nome_completo' => 'Maria Santos',
            'email' => 'maria@email.com',
            'estado' => 'RJ',
            'cidade' => 'Rio de Janeiro',
            'telefone' => '21888887777',
            'unidade_campus' => 'Campus Rio',
            'perfil' => 'aluno',
            'manifestacao' => 'reclamação',
            'categoria' => 'Solicitações - TOTVS',
            'subcategoria' => 'Lançamento, correção de notas',
            'descricao' => 'Minha nota não foi lançada corretamente no sistema',
            'status' => 'respondido',
            'nivel_ensino' => 'faculdade',
            'ra' => '202300002',
            'curso' => 'Engenharia'
        ],
        [
            'protocolo' => 'EAD003',
            'senha_acesso' => 'ghi789',
            'nome_completo' => 'Pedro Oliveira',
            'email' => 'pedro@email.com',
            'estado' => 'MG',
            'cidade' => 'Belo Horizonte',
            'telefone' => '31777776666',
            'unidade_campus' => 'Campus BH',
            'perfil' => 'aluno',
            'manifestacao' => 'sugestão',
            'categoria' => 'Solicitações Pedagógicas',
            'subcategoria' => 'Falar com o Professor da Disciplina',
            'descricao' => 'Gostaria de agendar uma reunião com o professor',
            'status' => 'finalizado',
            'nivel_ensino' => 'faculdade',
            'ra' => '202300003',
            'curso' => 'Direito'
        ],
        [
            'protocolo' => 'EAD004',
            'senha_acesso' => 'jkl012',
            'nome_completo' => 'Ana Costa',
            'email' => 'ana@email.com',
            'estado' => 'SP',
            'cidade' => 'Campinas',
            'telefone' => '19666665555',
            'unidade_campus' => 'Campus Campinas',
            'perfil' => 'aluno',
            'manifestacao' => 'Solicitação',
            'categoria' => 'Solicitações - Ambiente Virtual de Aprendizagem (AVA)',
            'subcategoria' => 'Visualização de notas',
            'descricao' => 'Não consigo ver minhas notas no sistema',
            'status' => 'Atribuído',
            'nivel_ensino' => 'faculdade',
            'ra' => '202300004',
            'curso' => 'Pedagogia'
        ],
        [
            'protocolo' => 'EAD005',
            'senha_acesso' => 'mno345',
            'nome_completo' => 'Carlos Ferreira',
            'email' => 'carlos@email.com',
            'estado' => 'RS',
            'cidade' => 'Porto Alegre',
            'telefone' => '51555554444',
            'unidade_campus' => 'Campus Porto Alegre',
            'perfil' => 'professor',
            'manifestacao' => 'elogio',
            'categoria' => 'Solicitações Pedagógicas',
            'subcategoria' => 'Falar com o Gestor do NEaD',
            'descricao' => 'Parabenizar a equipe pelo excelente suporte',
            'status' => 'respondido',
            'nivel_ensino' => 'faculdade',
            'ra' => null,
            'curso' => null
        ]
    ];

    echo "<h2>📝 Inserindo dados de exemplo...</h2>";

    $inserted = 0;
    foreach ($dados_exemplo as $dados) {
        // Verificar se o protocolo já existe
        $check_sql = "SELECT COUNT(*) as record_exists FROM chamados WHERE protocolo = ?";
        $check_stmt = $conn_ead->prepare($check_sql);
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
            
            $insert_stmt = $conn_ead->prepare($insert_sql);
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
    $result = $conn_ead->query($sql);
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
    $result = $conn_ead->query($sql);
    
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
    $result = $conn_ead->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Status</th><th>Quantidade</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['status']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";

    echo "<h2>🎯 Próximos Passos</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50;'>";
    echo "<p><strong>Dados inseridos com sucesso!</strong></p>";
    echo "<p>Agora os gráficos do Portal EAD mostrarão:</p>";
    echo "<ul>";
    echo "<li>📊 <strong>Categorias diversificadas</strong> ao invés de só 'Categoria não informada'</li>";
    echo "<li>📈 <strong>Status variados</strong> ao invés de só 'Não informado'</li>";
    echo "<li>🥧 <strong>Gráficos pizza mais informativos</strong></li>";
    echo "</ul>";
    echo "<p><a href='dashboard.php?periodo=todos&portal=ead' target='_blank'>🔗 Visualizar Dashboard EAD Atualizado</a></p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn_ead)) {
        $conn_ead->close();
    }
}
?>