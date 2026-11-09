-- TaskFlow PHP: estrutura do banco MySQL
CREATE DATABASE IF NOT EXISTS db_e1cf43e2c84c CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_e1cf43e2c84c;

CREATE TABLE IF NOT EXISTS tarefas (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    titulo     VARCHAR(255) NOT NULL,
    concluida  TINYINT(1)   NOT NULL DEFAULT 0,
    criada_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);
