-- =========================================
-- Smart Liquid - Dados Mock (Demonstração)
-- =========================================
-- Estes dados simulam operações reais do sistema
-- para fins de visualização no painel web.
-- =========================================

INSERT INTO data (
  peso,
  temperatura,
  torneira,
  manutencao,
  vazando,
  usuario,
  operacao_tentada,
  data_hora
) VALUES

-- 1️⃣ Maria: usuário autorizado, manutenção realizada com sucesso
(
  3,            -- peso
  23.5,            -- temperatura
  0,               -- torneira fechada
  1,               -- manutenção autorizada
  0,               -- sem vazamento
  'Maria',         -- usuário
  '',              -- operação tentada vazia (usuário autorizado)
  NOW()
),

-- 2️⃣ Desconhecido: tentativa de manutenção NÃO autorizada
(
  2.8,                -- peso
  23.7,                -- temperatura
  0,                   -- torneira fechada
  0,                   -- manutenção não autorizada
  1,                   -- sem vazamento
  'Desconhecido',      -- usuário
  'MANUTENCAO',        -- operação tentada registrada
  NOW()
),

-- 3️⃣ Messi: usuário autorizado operando a torneira
(
  3,            -- peso
  24.1,            -- temperatura
  1,               -- torneira aberta
  0,               -- sem manutenção
  0,               -- sem vazamento
  'Messi',         -- usuário
  '',              -- operação tentada vazia (usuário autorizado)
  NOW()
);
