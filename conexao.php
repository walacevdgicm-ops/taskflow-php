<?php

$config = require __DIR__ . '/config.php';

$dbConfig = $config['db'];

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $dbConfig['host'],
    $dbConfig['port'],
    $dbConfig['nome'],
    $dbConfig['charset']
);
$opcoes = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna arrays associativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desativa emulação de prepared statements
];

try {
    $pdo = new PDO($dsn, $dbConfig['usuario'], $dbConfig ['senha'], $opcoes);
    return $pdo;
} catch (PDOException $e) {
    dia("Erro ao conectar banco de dados: " .$e->getMessage());
}
