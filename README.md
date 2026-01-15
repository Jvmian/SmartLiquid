SMART LIQUID
Sistema Inteligente de Monitoramento e Controle de Líquidos (IoT)

==================================================

VISÃO GERAL
O Smart Liquid é um sistema IoT desenvolvido para monitorar, registrar e controlar líquidos de forma inteligente, integrando hardware, firmware, backend e interface web.

O projeto foi apresentado como Trabalho de Conclusão de Curso (TCC) na ETEC, no curso de Desenvolvimento de Sistemas, em 05 de dezembro de 2025.

O sistema funciona de forma autônoma, realizando validações de acesso, acionando atuadores e registrando todas as operações e tentativas em um banco de dados, com posterior visualização via painel web.

--------------------------------------------------

FUNCIONALIDADES PRINCIPAIS
- Monitoramento de:
  - Peso do líquido
  - Temperatura
  - Estado da torneira
  - Status de manutenção
  - Detecção de vazamento
- Controle de acesso por senha
- Registro de tentativas não autorizadas
- Acionamento automático de servo motor e buzzer
- Envio de dados via HTTP para servidor
- Visualização dos dados em painel web (monitoramento)
- Sistema totalmente autônomo (não depende do site para operar)

--------------------------------------------------

ARQUITETURA DO SISTEMA

ESP32
  ↓ (HTTP GET)
Backend PHP (salvar_dados.php)
  ↓
Banco de Dados MySQL
  ↓
Painel Web de Monitoramento

--------------------------------------------------

MINHAS RESPONSABILIDADES NO PROJETO

Atuei como líder técnico do projeto, sendo responsável pelo desenvolvimento completo dos seguintes módulos:

HARDWARE
- Desenho da placa PCB (face simples) no PCB Wizard
- Montagem manual da placa
- Projeto e construção da maquete física
- Integração dos sensores e atuadores
- Desenvolvimento da fonte de alimentação externa (5V no ESP32 via borne)

FIRMWARE (ESP32)
- Programação completa do ESP32
- Leitura de sensores
- Lógica de validação de senha
- Controle de servo motor e buzzer
- Comunicação HTTP com o servidor
- Envio estruturado dos dados para o backend

BACKEND (PHP)
- Desenvolvimento do endpoint salvar_dados.php
- Validação dos parâmetros recebidos
- Inserção segura no banco de dados (prepared statements)
- Registro de operações e tentativas não autorizadas

BANCO DE DADOS
- Modelagem e criação da tabela de telemetria (data)
- Definição dos campos de monitoramento
- Criação de schema mínimo para demonstração

FRONT-END (MONITORAMENTO)
- Desenvolvimento e refatoração da interface de exibição de dados
- Correções visuais e funcionais
- Tratamento e apresentação das informações
- Painel web utilizado na apresentação do TCC

--------------------------------------------------

OBSERVAÇÃO SOBRE TRABALHO EM GRUPO
O projeto original foi desenvolvido em grupo e continha módulos adicionais, como sistema de pedidos e área administrativa comercial.

Neste repositório estão disponíveis apenas os módulos desenvolvidos e mantidos por mim, com foco no sistema IoT, monitoramento e controle.

--------------------------------------------------

BANCO DE DADOS (MODO DEMONSTRAÇÃO)
Este repositório inclui um schema mínimo do banco de dados, contendo apenas a tabela necessária para o funcionamento do monitoramento.

- Dados reais não são expostos
- Inclui dados mock para demonstração
- Permite que o painel funcione sem o sistema completo

--------------------------------------------------

DADOS MOCK
O projeto utiliza dados fictícios para simular cenários reais, como:
- Usuário autorizado realizando manutenção
- Tentativa de manutenção por usuário desconhecido
- Operação normal da torneira

Os dados mock são utilizados exclusivamente para fins de demonstração.

--------------------------------------------------

TECNOLOGIAS UTILIZADAS

HARDWARE
- ESP32
- Sensores de peso e temperatura
- Servo motor
- Buzzer
- PCB face simples
- Fonte externa 5V

SOFTWARE
- C / C++ (ESP32)
- PHP
- MySQL
- HTML / CSS
- HTTP

--------------------------------------------------

ESTRUTURA DO REPOSITÓRIO

SmartLiquid/
- firmware/
- backend/
- database/
- frontend/
- hardware/
- docs/
- README.txt

--------------------------------------------------

IMAGENS DO PROJETO
O repositório contém imagens da:
- Maquete física
- Placa PCB (protótipo e versão final)
- Sistema montado e em funcionamento
- Interface web de monitoramento

--------------------------------------------------

CONTEXTO ACADÊMICO
Instituição: ETEC
Curso: Desenvolvimento de Sistemas
Tipo: Trabalho de Conclusão de Curso (TCC)
Ano: 2025

--------------------------------------------------

CONSIDERAÇÕES FINAIS
O Smart Liquid representa a integração prática entre eletrônica, programação embarcada, backend e sistemas web, demonstrando domínio de todo o fluxo de um projeto IoT real, do hardware ao software.

Projeto desenvolvido para fins acadêmicos e de portfólio.
