<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Incluir arquivo de configuração
require_once 'includes/config.php';

// Verificar se foi passado o ID do chamado
if (!isset($_POST['chamado_id']) || empty($_POST['chamado_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID do chamado não fornecido']);
    exit;
}

$chamado_id = intval($_POST['chamado_id']);

try {
    // Buscar dados do chamado de todas as bases possíveis
    $chamado = null;
    $databases = [
        'portal_ouvidoria' => 'Ouvidoria',
        'portal_ead' => 'Ead',
        'portal_processo_seletivo' => 'Processo_seletivo',
        'portal_secretaria_academica' => 'Secretaria',
        'portal_financeiro' => 'Financeiro',
        'portal_exaluno' => 'Exaluno'
    ];

    foreach ($databases as $db_name => $portal_name) {
        try {
            $pdo = connectDB($db_name);
            $stmt = $pdo->prepare("SELECT * FROM chamados WHERE id = ?");
            $stmt->execute([$chamado_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $chamado = $result;
                $chamado['portal'] = $portal_name;
                break;
            }
        } catch (Exception $e) {
            // Se o banco não existir ou houver erro, continuar para o próximo
            continue;
        }
    }

    if (!$chamado) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Chamado não encontrado']);
        exit;
    }

    // Retornar dados do chamado
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'chamado' => $chamado
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
?>