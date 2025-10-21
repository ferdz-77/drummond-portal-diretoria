<?php
/**
 * Script para inserir dados de teste nos bancos de desenvolvimento
 * Para testar filtros de período
 */

require_once 'includes/config.php';

echo "🔧 INSERINDO DADOS DE TESTE\n";
echo str_repeat("=", 50) . "\n";

// Função para inserir dados na ouvidoria
function insertOuvidoriaTestData() {
    try {
        $pdo = connectDB('portal_ouvidoria');
        
        // Criar tabela se não existir (usando estrutura correta do config.php)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS chamados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50) NOT NULL,
                manifestacao VARCHAR(100) NOT NULL,
                data_abertura DATETIME NOT NULL,
                data_encerramento DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Limpar dados existentes
        $pdo->exec("DELETE FROM chamados");
        
        $hoje = date('Y-m-d');
        $ontem = date('Y-m-d', strtotime('-1 day'));
        $esta_semana = date('Y-m-d', strtotime('-3 days')); // Dados desta semana
        $semana_passada = date('Y-m-d', strtotime('-10 days')); // Dados de semana passada
        $mes_passado = date('Y-m-d', strtotime('-30 days'));
        $ano_passado = date('Y-m-d', strtotime('-365 days'));
        
        // Dados de teste - diferentes períodos (usando colunas corretas)
        $test_data = [
            // Dados de hoje (5 registros)
            ['Aberto', 'Reclamação', $hoje . ' 09:00:00', null],
            ['Em Andamento', 'Sugestão', $hoje . ' 10:00:00', null],
            ['Resolvido', 'Elogio', $hoje . ' 08:00:00', $hoje . ' 12:00:00'],
            ['Resolvido', 'Reclamação', $hoje . ' 11:00:00', $hoje . ' 14:00:00'],
            ['Aberto', 'Sugestão', $hoje . ' 13:00:00', null],
            
            // Dados de ontem (ainda na semana atual - 3 registros)
            ['Resolvido', 'Reclamação', $ontem . ' 14:00:00', $ontem . ' 16:00:00'],
            ['Aberto', 'Sugestão', $ontem . ' 15:00:00', null],
            ['Resolvido', 'Elogio', $ontem . ' 09:00:00', $ontem . ' 11:00:00'],
            
            // Dados desta semana (3 dias atrás - 2 registros)
            ['Resolvido', 'Reclamação', $esta_semana . ' 09:00:00', $esta_semana . ' 11:00:00'],
            ['Em Andamento', 'Sugestão', $esta_semana . ' 11:00:00', null],
            
            // Dados de semana passada (10 dias atrás - outra semana - 3 registros)
            ['Resolvido', 'Reclamação', $semana_passada . ' 09:00:00', $semana_passada . ' 11:00:00'],
            ['Resolvido', 'Elogio', $semana_passada . ' 10:00:00', $semana_passada . ' 12:00:00'],
            ['Resolvido', 'Reclamação', $semana_passada . ' 13:00:00', $semana_passada . ' 15:00:00'],
            
            // Dados do mês passado (4 registros)
            ['Resolvido', 'Reclamação', $mes_passado . ' 09:00:00', $mes_passado . ' 11:00:00'],
            ['Resolvido', 'Sugestão', $mes_passado . ' 10:00:00', $mes_passado . ' 14:00:00'],
            ['Resolvido', 'Elogio', $mes_passado . ' 11:00:00', $mes_passado . ' 13:00:00'],
            ['Resolvido', 'Reclamação', $mes_passado . ' 12:00:00', $mes_passado . ' 16:00:00'],
            
            // Dados do ano passado (2 registros)
            ['Resolvido', 'Reclamação', $ano_passado . ' 09:00:00', $ano_passado . ' 12:00:00'],
            ['Resolvido', 'Sugestão', $ano_passado . ' 10:00:00', $ano_passado . ' 13:00:00'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO chamados (status, manifestacao, data_abertura, data_encerramento) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($test_data as $data) {
            $stmt->execute($data);
        }
        
        $count = count($test_data);
        echo "✅ Inseridos $count registros na ouvidoria\n";
        
    } catch (Exception $e) {
        echo "❌ Erro ao inserir dados na ouvidoria: " . $e->getMessage() . "\n";
    }
}

// Função para inserir dados no EAD
function insertEADTestData() {
    try {
        $pdo = connectDB('portal_ead');
        
        // Criar tabela se não existir (usando estrutura correta do config.php)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS chamados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50) NOT NULL,
                categoria VARCHAR(100) NOT NULL,
                data_abertura DATETIME NOT NULL,
                data_encerramento DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Limpar dados existentes
        $pdo->exec("DELETE FROM chamados");
        
        $hoje = date('Y-m-d');
        $esta_semana = date('Y-m-d', strtotime('-3 days')); // Dados desta semana
        $semana_passada = date('Y-m-d', strtotime('-10 days')); // Dados de semana passada
        $mes_passado = date('Y-m-d', strtotime('-30 days'));
        
        // Dados de teste (usando colunas corretas)
        $test_data = [
            // Dados de hoje (semana atual - 3 registros)
            ['Aberto', 'Suporte Técnico', $hoje . ' 09:00:00', null],
            ['Resolvido', 'Acesso ao Sistema', $hoje . ' 10:00:00', $hoje . ' 12:00:00'],
            ['Em Andamento', 'Material Didático', $hoje . ' 14:00:00', null],
            
            // Dados desta semana (3 dias atrás - 2 registros)
            ['Resolvido', 'Suporte Técnico', $esta_semana . ' 09:00:00', $esta_semana . ' 10:00:00'],
            ['Em Andamento', 'Acesso ao Sistema', $esta_semana . ' 14:00:00', null],
            
            // Dados de semana passada (10 dias atrás - outra semana - 1 registro)
            ['Resolvido', 'Material Didático', $semana_passada . ' 11:00:00', $semana_passada . ' 13:00:00'],
            
            // Dados do mês passado (3 registros)
            ['Resolvido', 'Suporte Técnico', $mes_passado . ' 09:00:00', $mes_passado . ' 11:00:00'],
            ['Resolvido', 'Material Didático', $mes_passado . ' 10:00:00', $mes_passado . ' 12:00:00'],
            ['Resolvido', 'Acesso ao Sistema', $mes_passado . ' 13:00:00', $mes_passado . ' 15:00:00'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO chamados (status, categoria, data_abertura, data_encerramento) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($test_data as $data) {
            $stmt->execute($data);
        }
        
        $count = count($test_data);
        echo "✅ Inseridos $count registros no EAD\n";
        
    } catch (Exception $e) {
        echo "❌ Erro ao inserir dados no EAD: " . $e->getMessage() . "\n";
    }
}

// Função para inserir dados no Processo Seletivo
function insertProcessoSeletivoTestData() {
    try {
        $pdo = connectDB('portal_processo_seletivo');
        
        // Criar tabela se não existir
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS chamados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50) NOT NULL,
                categoria VARCHAR(100) NOT NULL,
                data_abertura DATETIME NOT NULL,
                data_resposta DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Limpar dados existentes
        $pdo->exec("DELETE FROM chamados");
        
        $hoje = date('Y-m-d');
        $esta_semana = date('Y-m-d', strtotime('-3 days'));
        $semana_passada = date('Y-m-d', strtotime('-10 days'));
        $mes_passado = date('Y-m-d', strtotime('-30 days'));
        
        // Dados de teste
        $test_data = [
            // Dados de hoje
            ['Aberto', 'Vestibular', $hoje . ' 09:00:00', null],
            ['Resolvido', 'ENEM', $hoje . ' 10:00:00', $hoje . ' 14:00:00'],
            
            // Dados desta semana
            ['Resolvido', 'Vestibular', $esta_semana . ' 09:00:00', $esta_semana . ' 11:00:00'],
            ['Em Andamento', 'ProUni', $esta_semana . ' 14:00:00', null],
            
            // Dados de semana passada
            ['Resolvido', 'ENEM', $semana_passada . ' 11:00:00', $semana_passada . ' 15:00:00'],
            
            // Dados do mês passado
            ['Resolvido', 'Vestibular', $mes_passado . ' 09:00:00', $mes_passado . ' 12:00:00'],
            ['Resolvido', 'ProUni', $mes_passado . ' 10:00:00', $mes_passado . ' 13:00:00'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO chamados (status, categoria, data_abertura, data_resposta) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($test_data as $data) {
            $stmt->execute($data);
        }
        
        $count = count($test_data);
        echo "✅ Inseridos $count registros no Processo Seletivo\n";
        
    } catch (Exception $e) {
        echo "❌ Erro ao inserir dados no Processo Seletivo: " . $e->getMessage() . "\n";
    }
}

// Função para inserir dados na Secretaria
function insertSecretariaTestData() {
    try {
        $pdo = connectDB('portal_secretaria_academica');
        
        // Criar tabela se não existir
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS chamados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50) NOT NULL,
                categoria VARCHAR(100) NOT NULL,
                data_abertura DATETIME NOT NULL,
                data_encerramento DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Limpar dados existentes
        $pdo->exec("DELETE FROM chamados");
        
        $hoje = date('Y-m-d');
        $esta_semana = date('Y-m-d', strtotime('-3 days'));
        $semana_passada = date('Y-m-d', strtotime('-10 days'));
        $mes_passado = date('Y-m-d', strtotime('-30 days'));
        
        // Dados de teste
        $test_data = [
            // Dados de hoje
            ['Aberto', 'Histórico Escolar', $hoje . ' 09:00:00', null],
            ['Resolvido', 'Declaração', $hoje . ' 10:00:00', $hoje . ' 11:00:00'],
            ['Em Andamento', 'Certificado', $hoje . ' 14:00:00', null],
            
            // Dados desta semana
            ['Resolvido', 'Histórico Escolar', $esta_semana . ' 09:00:00', $esta_semana . ' 10:00:00'],
            ['Resolvido', 'Declaração', $esta_semana . ' 11:00:00', $esta_semana . ' 12:00:00'],
            
            // Dados de semana passada
            ['Resolvido', 'Certificado', $semana_passada . ' 11:00:00', $semana_passada . ' 13:00:00'],
            
            // Dados do mês passado
            ['Resolvido', 'Histórico Escolar', $mes_passado . ' 09:00:00', $mes_passado . ' 11:00:00'],
            ['Resolvido', 'Declaração', $mes_passado . ' 10:00:00', $mes_passado . ' 11:00:00'],
            ['Resolvido', 'Certificado', $mes_passado . ' 12:00:00', $mes_passado . ' 14:00:00'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO chamados (status, categoria, data_abertura, data_encerramento) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($test_data as $data) {
            $stmt->execute($data);
        }
        
        $count = count($test_data);
        echo "✅ Inseridos $count registros na Secretaria\n";
        
    } catch (Exception $e) {
        echo "❌ Erro ao inserir dados na Secretaria: " . $e->getMessage() . "\n";
    }
}

// Função para inserir dados no Financeiro
function insertFinanceiroTestData() {
    try {
        $pdo = connectDB('portal_financeiro');
        
        // Criar tabela se não existir
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS chamados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50) NOT NULL,
                categoria VARCHAR(100) NOT NULL,
                data_abertura DATETIME NOT NULL,
                data_encerramento DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Limpar dados existentes
        $pdo->exec("DELETE FROM chamados");
        
        $hoje = date('Y-m-d');
        $esta_semana = date('Y-m-d', strtotime('-3 days'));
        $semana_passada = date('Y-m-d', strtotime('-10 days'));
        $mes_passado = date('Y-m-d', strtotime('-30 days'));
        
        // Dados de teste
        $test_data = [
            // Dados de hoje
            ['Aberto', 'Boleto', $hoje . ' 09:00:00', null],
            ['Resolvido', 'Renegociação', $hoje . ' 10:00:00', $hoje . ' 16:00:00'],
            
            // Dados desta semana
            ['Resolvido', 'Boleto', $esta_semana . ' 09:00:00', $esta_semana . ' 12:00:00'],
            ['Em Andamento', 'Desconto', $esta_semana . ' 14:00:00', null],
            
            // Dados de semana passada
            ['Resolvido', 'Renegociação', $semana_passada . ' 11:00:00', $semana_passada . ' 17:00:00'],
            
            // Dados do mês passado
            ['Resolvido', 'Boleto', $mes_passado . ' 09:00:00', $mes_passado . ' 10:00:00'],
            ['Resolvido', 'Desconto', $mes_passado . ' 10:00:00', $mes_passado . ' 12:00:00'],
            ['Resolvido', 'Renegociação', $mes_passado . ' 11:00:00', $mes_passado . ' 18:00:00'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO chamados (status, categoria, data_abertura, data_encerramento) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($test_data as $data) {
            $stmt->execute($data);
        }
        
        $count = count($test_data);
        echo "✅ Inseridos $count registros no Financeiro\n";
        
    } catch (Exception $e) {
        echo "❌ Erro ao inserir dados no Financeiro: " . $e->getMessage() . "\n";
    }
}

// Função para inserir dados no Ex-Aluno
function insertExAlunoTestData() {
    try {
        $pdo = connectDB('portal_exaluno');
        
        // Criar tabela se não existir
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS chamados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50) NOT NULL,
                categoria VARCHAR(100) NOT NULL,
                data_abertura DATETIME NOT NULL,
                data_encerramento DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Limpar dados existentes
        $pdo->exec("DELETE FROM chamados");
        
        $hoje = date('Y-m-d');
        $esta_semana = date('Y-m-d', strtotime('-3 days'));
        $semana_passada = date('Y-m-d', strtotime('-10 days'));
        $mes_passado = date('Y-m-d', strtotime('-30 days'));
        
        // Dados de teste
        $test_data = [
            // Dados de hoje
            ['Aberto', 'Diploma', $hoje . ' 09:00:00', null],
            ['Resolvido', 'Histórico', $hoje . ' 10:00:00', $hoje . ' 13:00:00'],
            
            // Dados desta semana
            ['Resolvido', 'Diploma', $esta_semana . ' 09:00:00', $esta_semana . ' 14:00:00'],
            ['Em Andamento', 'Certificado', $esta_semana . ' 11:00:00', null],
            
            // Dados de semana passada
            ['Resolvido', 'Histórico', $semana_passada . ' 10:00:00', $semana_passada . ' 12:00:00'],
            
            // Dados do mês passado
            ['Resolvido', 'Diploma', $mes_passado . ' 09:00:00', $mes_passado . ' 15:00:00'],
            ['Resolvido', 'Certificado', $mes_passado . ' 10:00:00', $mes_passado . ' 13:00:00'],
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO chamados (status, categoria, data_abertura, data_encerramento) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($test_data as $data) {
            $stmt->execute($data);
        }
        
        $count = count($test_data);
        echo "✅ Inseridos $count registros no Ex-Aluno\n";
        
    } catch (Exception $e) {
        echo "❌ Erro ao inserir dados no Ex-Aluno: " . $e->getMessage() . "\n";
    }
}

// Executar inserções
echo "Inserindo dados de teste...\n\n";
insertOuvidoriaTestData();
insertEADTestData();
insertProcessoSeletivoTestData();
insertSecretariaTestData();
insertFinanceiroTestData();
insertExAlunoTestData();

echo "\n🎉 Dados de teste inseridos com sucesso!\n";
echo "Agora você pode testar os filtros de período.\n";
?>