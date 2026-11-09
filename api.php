<?php
header('Content-Type: application/json; charset=utf-8');

// Requisita a conexão PDO
$pdo = require __DIR__ . '/conexao.php';

// Captura a ação via GET ou POST
$acao = $_REQUEST['acao'] ?? '';

try {
    // --- LISTAR TAREFAS (READ) ---
    if ($acao === 'listar' || $_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query('SELECT * FROM tarefas ORDER BY id DESC');
        $tarefas = $stmt->fetchAll();

        echo json_encode(['sucesso' => true, 'dados' => $tarefas]);
        exit;
    }

    // --- CADASTRAR TAREFA (CREATE) ---
    if ($acao === 'cadastrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = trim($_POST['titulo'] ?? '');

        if (empty($titulo)) {
            echo json_encode(['sucesso' => false, 'erro' => 'O título não pode ser vazio.']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO tarefas (titulo) VALUES (:titulo)');
        $stmt->bindValue(':titulo', $titulo);
        $stmt->execute();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa adicionada com sucesso!']);
        exit;
    }

    // --- ALTERAR STATUS (UPDATE) ---
    if ($acao === 'alternar_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $concluida = filter_input(INPUT_POST, 'concluida', FILTER_VALIDATE_INT);

        if ($id === false || $concluida === false) {
            echo json_encode(['sucesso' => false, 'erro' => 'Dados inválidos.']);
            exit;
        }

        $novoStatus = $concluida ? 0 : 1;
        $stmt = $pdo->prepare('UPDATE tarefas SET concluida = :concluida WHERE id = :id');
        $stmt->bindValue(':concluida', $novoStatus, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado!']);
        exit;
    }

    // --- EXCLUIR TAREFA (DELETE) ---
    if ($acao === 'excluir' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id === false) {
            echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
            exit;
        }

        $stmt = $pdo->prepare('DELETE FROM tarefas WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa excluída!']);
        exit;
    }

    echo json_encode(['sucesso' => false, 'erro' => 'Ação não reconhecida.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
}