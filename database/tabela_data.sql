-- =========================================
-- Smart Liquid - Banco de Dados (Schema Mínimo)
-- =========================================

-- Criação do banco
CREATE DATABASE IF NOT EXISTS smartliquid;
USE smartliquid;

-- Tabela desenvolvida para registro dos dados enviados pelo ESP32
CREATE TABLE IF NOT EXISTS data (
  id INT AUTO_INCREMENT PRIMARY KEY,
  peso FLOAT NOT NULL,
  temperatura FLOAT NOT NULL,
  torneira TINYINT NOT NULL,
  manutencao TINYINT NOT NULL,
  vazando TINYINT NOT NULL,
  usuario VARCHAR(50) NOT NULL,
  operacao_tentada VARCHAR(50) NOT NULL,
  data_hora DATETIME NOT NULL
);
