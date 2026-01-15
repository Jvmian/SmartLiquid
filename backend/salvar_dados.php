<?php
header('Content-Type: text/plain; charset=utf-8');

/**
 * Smart Liquid - Endpoint de Recebimento de Dados IoT
 * Recebe dados enviados pelo ESP32 via HTTP (GET)
 * e armazena no banco de dados.
 */

// Conexão com o banco
$conn = new mysqli("localhost", "root", "", "smartliquid");
if ($conn->connect_error) {
    die("ERRO: " . $conn->connect_error);
}

// Parâmetros obrigatórios
$required = ['peso', 'temperatura', 'torneira', 'manutencao', 'vazando', 'usuario'];
foreach ($required as $param) {
    if (!isset($_GET[$param])) {
        die("ERRO: Parâmetro '$param' faltando");
    }
}

// Processamento dos dados
$peso = (float) $_GET['peso'];
$temperatura = (float) $_GET['temperatura'];
$torneira = (int) $_GET['torneira'];
$manutencao = (int) $_GET['manutencao'];
$vazando = (int) $_GET['vazando'];
$usuario = $conn->real_escape_string($_GET['usuario']);

// Operação tentada é opcional (apenas para usuário não autorizado)
$operacao_tentada = isset($_GET['operacao'])
    ? $conn->real_escape_string($_GET['operacao'])
    : null;

// Query
$sql = "INSERT INTO data 
        (peso, temperatura, torneira, manutencao, vazando, usuario, operacao_tentada, data_hora)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("ERRO: " . $conn->error);
}

// Bind dos parâmetros
$stmt->bind_param(
    "ddiiiss",
    $peso,
    $temperatura,
    $torneira,
    $manutencao,
    $vazando,
    $usuario,
    $operacao_tentada
);

// Execução
if ($stmt->execute()) {
    echo "OK: Dados salvos com sucesso";
} else {
    echo "ERRO: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
