<?php
// 1. Requisita a conexão com o banco
$pdo = require __DIR__ . '/conexao.php';

$mensagem = '';

// 2. Processa as requisições (INSERT, UPDATE e DELETE via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // --- CADASTRAR TAREFA (INSERT) ---
    if ($acao === 'cadastrar') {
        $titulo = trim($_POST['titulo'] ?? '');

        if (!empty($titulo)) {
            $stmt = $pdo->prepare('INSERT INTO tarefas (titulo) VALUES (:titulo)');
            $stmt->bindValue(':titulo', $titulo);
            $stmt->execute();

            header('Location: tarefas.php?msg=cadastrado');
            exit;
        } else {
            $mensagem = 'Por favor, digite o título da tarefa.';
        }
    }

    // --- CONCLUIR / ALTERAR STATUS (UPDATE) ---
    if ($acao === 'alternar_status') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $concluida = filter_input(INPUT_POST, 'concluida', FILTER_VALIDATE_INT);

        if ($id !== false && $concluida !== false) {
            // Inverte o status: se era 0 vira 1, se era 1 vira 0
            $novoStatus = $concluida ? 0 : 1;

            $stmt = $pdo->prepare('UPDATE tarefas SET concluida = :concluida WHERE id = :id');
            $stmt->bindValue(':concluida', $novoStatus, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: tarefas.php?msg=atualizado');
            exit;
        }
    }

    // --- EXCLUIR TAREFA (DELETE) ---
    if ($acao === 'excluir') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id !== false) {
            $stmt = $pdo->prepare('DELETE FROM tarefas WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: tarefas.php?msg=excluido');
            exit;
        }
    }
}

// 3. Executa o SELECT para buscar todas as tarefas cadastradas
$stmt = $pdo->query('SELECT * FROM tarefas ORDER BY id DESC');
$tarefas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciador de Tarefas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .container { max-width: 650px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        form.inline { display: inline; }
        input[type="text"] { padding: 8px; width: 70%; }
        button { padding: 6px 12px; cursor: pointer; border: none; border-radius: 4px; }
        .btn-add { background-color: #28a745; color: white; }
        .btn-delete { background-color: #dc3545; color: white; margin-left: 5px; }
        .btn-status { background-color: #6c757d; color: white; }
        ul { list-style-type: none; padding: 0; }
        li { padding: 10px; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; }
        .concluida { text-decoration: line-through; color: #888; }
        .sucesso { color: #155724; background-color: #d4edda; padding: 10px; border-radius: 4px; }
        .erro { color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Gerenciador de Tarefas</h1>

    <!-- Mensagens de Feedback -->
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'cadastrado'): ?>
            <p class="sucesso">Tarefa adicionada com sucesso!</p>
        <?php elseif ($_GET['msg'] === 'atualizado'): ?>
            <p class="sucesso">Status da tarefa atualizado!</p>
        <?php elseif ($_GET['msg'] === 'excluido'): ?>
            <p class="sucesso">Tarefa excluída com sucesso!</p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($mensagem)): ?>
        <p class="erro"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <!-- Formulário de inserção (INSERT) -->
    <form action="tarefas.php" method="POST">
        <input type="hidden" name="acao" value="cadastrar">
        <input type="text" name="titulo" placeholder="Digite uma nova tarefa..." required>
        <button type="submit" class="btn-add">Adicionar</button>
    </form>

    <hr>

    <h2>Minhas Tarefas</h2>

    <!-- Lista com SELECT, UPDATE e DELETE -->
    <?php if (count($tarefas) > 0): ?>
        <ul>
            <?php foreach ($tarefas as $tarefa): ?>
                <li>
                    <span class="<?= $tarefa['concluida'] ? 'concluida' : '' ?>">
                        <?= htmlspecialchars($tarefa['titulo']) ?>
                    </span>

                    <div>
                        <!-- Botão Concluir/Desfazer (UPDATE) -->
                        <form action="tarefas.php" method="POST" class="inline">
                            <input type="hidden" name="acao" value="alternar_status">
                            <input type="hidden" name="id" value="<?= $tarefa['id'] ?>">
                            <input type="hidden" name="concluida" value="<?= $tarefa['concluida'] ?>">
                            <button type="submit" class="btn-status">
                                <?= $tarefa['concluida'] ? 'Desfazer' : 'Concluir' ?>
                            </button>
                        </form>

                        <!-- Botão Excluir (DELETE) -->
                        <form action="tarefas.php" method="POST" class="inline" onsubmit="return confirm('Deseja realmente excluir esta tarefa?');">
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id" value="<?= $tarefa['id'] ?>">
                            <button type="submit" class="btn-delete">Excluir</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nenhuma tarefa cadastrada.</p>
    <?php endif; ?>
</div>

</body>
</html>