-- Banco e tabelas para o projeto Vira Copos
CREATE DATABASE IF NOT EXISTS vira_copos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE vira_copos;
CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente VARCHAR(100) NOT NULL,
  itens TEXT NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'Pendente'
);
INSERT INTO pedidos (cliente, itens, status) VALUES
('João', '2x Cerveja, 1x Batata Frita', 'Pendente'),
('Ana', '1x Suco, 1x Porção de Calabresa', 'Preparando');
CREATE TABLE IF NOT EXISTS estoque (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produto VARCHAR(100) NOT NULL,
  quantidade VARCHAR(50) NOT NULL
);
INSERT INTO estoque (produto, quantidade) VALUES
('Cerveja', '50 unidades'),
('Refrigerante', '30 unidades'),
('Batata Frita', '15 porções');
CREATE TABLE IF NOT EXISTS atendimento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mesa INT NOT NULL,
  mensagem TEXT NOT NULL
);
INSERT INTO atendimento (mesa, mensagem) VALUES
(4, 'Cliente pediu mais guardanapos.'),
(7, 'Cliente quer ver a conta.');
-- Tabela de funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL
);
-- OBS: cadastre um usuário via cadastro_funcionario.php para criar o primeiro administrador.
