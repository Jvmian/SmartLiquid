# SMART LIQUID
## Sistema Inteligente de Monitoramento e Controle de Líquidos (IoT)

---

## Visão Geral
O **Smart Liquid** é um sistema IoT desenvolvido para monitorar, registrar e controlar líquidos de forma inteligente, integrando hardware, firmware, backend e interface web.

O projeto foi apresentado como **Trabalho de Conclusão de Curso (TCC)** na **ETEC**, no curso de **Desenvolvimento de Sistemas**, no dia **05 de dezembro de 2025**.

O sistema opera de forma **autônoma**, validando acessos, acionando atuadores e registrando todas as operações e tentativas em banco de dados, com posterior visualização via painel web.

---

## Funcionalidades Principais
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
- Painel web exclusivo para monitoramento
- Funcionamento independente do site (sistema embarcado autônomo)

---

## Arquitetura do Sistema
ESP32  
↓ (HTTP GET)  
Backend PHP (salvar_dados.php)  
↓  
Banco de Dados MySQL  
↓  
Painel Web de Monitoramento

---

## Minhas Responsabilidades no Projeto

Atuei como **líder técnico do projeto**, sendo responsável pelo desenvolvimento completo dos seguintes módulos:

### Hardware
- Desenho da placa PCB (face simples) no **PCB Wizard**
- Montagem manual da placa
- Desenvolvimento da maquete física
- Integração de sensores e atuadores
- Desenvolvimento da fonte de alimentação externa  
  (alimentação 5V do ESP32 via carregador de celular e borne na placa)

### Firmware (ESP32)
- Programação completa do ESP32
- Leitura de sensores
- Lógica de validação de senha
- Controle de servo motor e buzzer
- Comunicação HTTP com o servidor
- Envio estruturado de dados para o backend

### Backend (PHP)
- Desenvolvimento do endpoint **salvar_dados.php**
- Validação dos parâmetros recebidos
- Inserção segura dos dados no banco (prepared statements)
- Registro de operações realizadas e tentativas não autorizadas

### Banco de Dados
- Modelagem e criação da tabela de telemetria (`data`)
- Definição dos campos de monitoramento
- Estrutura mínima para funcionamento do sistema de exibição

### Front-end (Monitoramento)
- Desenvolvimento e refatoração da interface de exibição de dados
- Correções visuais e funcionais
- Tratamento e apresentação das informações
- Painel web utilizado na apresentação do TCC

---

## Observação sobre Trabalho em Grupo
O projeto original foi desenvolvido em grupo e continha módulos adicionais, como sistema de pedidos e área administrativa comercial.

Este repositório contém **apenas os módulos desenvolvidos e mantidos por mim**, com foco no sistema IoT, monitoramento e controle.

---

## Banco de Dados (Modo Demonstração)
O repositório inclui apenas o **schema mínimo** necessário para o funcionamento do monitoramento.

- Nenhum dado real é exposto
- Utiliza dados mock para demonstração
- Permite execução do painel sem o sistema completo

---

## Dados Mock
Os dados fictícios simulam cenários reais, como:
- Usuário autorizado realizando manutenção
- Tentativa de manutenção por usuário desconhecido
- Operação normal da torneira

Esses dados são utilizados exclusivamente para fins de demonstração.

---

## Tecnologias Utilizadas

### Hardware
- ESP32
- Sensores de peso e temperatura
- Servo motor
- Buzzer
- PCB face simples
- Fonte externa 5V

### Software
- C / C++ (ESP32)
- PHP
- MySQL
- HTML / CSS
- HTTP

---

## Estrutura do Repositório
SmartLiquid/
- firmware/
- backend/
- database/
- frontend/
- hardware/
- docs/
- README.txt

---

## Imagens do Projeto
O repositório contém imagens da:
- Maquete física
- Placa PCB (protótipo e versão final)
- Sistema montado e em funcionamento
- Interface web de monitoramento

---

## Contexto Acadêmico
- Instituição: ETEC  
- Curso: Desenvolvimento de Sistemas  
- Tipo: Trabalho de Conclusão de Curso (TCC)  
- Ano: 2025  

---

## Considerações Finais
O **Smart Liquid** representa a integração prática entre eletrônica, programação embarcada, backend e sistemas web, demonstrando domínio de todo o fluxo de um projeto IoT real, do hardware ao software.

Projeto desenvolvido para fins acadêmicos e de portfólio.
