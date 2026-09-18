DROP DATABASE IF EXISTS db_ferrovia;
CREATE DATABASE db_ferrovia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_ferrovia;

CREATE TABLE trens (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    prefixo VARCHAR(20) NOT NULL UNIQUE,
    modelo VARCHAR(80) NOT NULL,
    ano_fabricacao SMALLINT NOT NULL,
    capacidade_t DECIMAL(8, 2) NOT NULL,
    situacao ENUM('operando', 'oficina', 'parado') NOT NULL DEFAULT 'operando'
);

CREATE TABLE leituras (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    trem_id INT NOT NULL,
    registrada_em DATETIME NOT NULL,
    velocidade_kmh DECIMAL(6, 2) NOT NULL,
    temperatura_c DECIMAL(6, 2) NOT NULL,
    consumo_lh DECIMAL(6, 2) NOT NULL,
    vibracao_mms DECIMAL(6, 2) NOT NULL,
    CONSTRAINT fk_leitura_trem FOREIGN KEY (trem_id) REFERENCES trens (id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_leitura_data (registrada_em)
);

INSERT INTO trens (prefixo, modelo, ano_fabricacao, capacidade_t, situacao) VALUES
    ('SC-3110', 'Wabtec ES44AC', 2016, 6450.00, 'operando'),
    ('SC-3125', 'Progress Rail SD80MAC', 2009, 5920.00, 'oficina'),
    ('SC-3140', 'Stadler Tier 4', 2021, 6100.50, 'operando'),
    ('PR-0701', 'Alstom Prima H4', 2013, 1250.00, 'operando'),
    ('PR-0702', 'GM SD40-2', 1987, 4300.00, 'parado'),
    ('MN-0055', 'Toshiba U20C', 1995, 3180.25, 'oficina');
