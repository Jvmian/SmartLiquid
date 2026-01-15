<?php
/**
 * Smart Liquid - Painel de Monitoramento IoT
 *
 * Módulo responsável exclusivamente pela exibição
 * e monitoramento dos dados enviados pelo ESP-32.
 *
 * Parte integrante do TCC apresentado na ETEC (2025).
 */
$conn = new mysqli("localhost", "root", "", "smartliquid");
if ($conn->connect_error) die("Erro de conexão");
$conn->set_charset("utf8mb4");

// --- 1. Processamento de Dados (Correção de Temperatura e Médias) ---

// Buscar os dados brutos (Últimos 50 para análise recente)
$query_events = $conn->query("SELECT * FROM data ORDER BY data_hora DESC LIMIT 50");
$events_list = [];

while ($row = $query_events->fetch_assoc()) {
    $events_list[] = $row;
}

// --- LÓGICA DE CORREÇÃO DE TEMPERATURA ---
$last_valid_temp = 0;

// Encontrar primeiro valor válido
foreach ($events_list as $evt) {
    if ($evt['temperatura'] != 85) {
        $last_valid_temp = $evt['temperatura'];
        break;
    }
}

// Corrigir o array e preparar para cálculo da média
$soma_temperatura = 0;
$conta_registros = 0;

foreach ($events_list as &$event_row) {
    if ($event_row['temperatura'] == 85) {
        $event_row['temperatura'] = $last_valid_temp;
    } else {
        $last_valid_temp = $event_row['temperatura'];
    }
    
    // Acumular para a média
    $soma_temperatura += $event_row['temperatura'];
    $conta_registros++;
}
unset($event_row);

// Calcular a média baseada NOS DADOS CORRIGIDOS
$avg_temp = ($conta_registros > 0) ? ($soma_temperatura / $conta_registros) : 0;

// --- Contadores Gerais do Banco ---
$total_events = $conn->query("SELECT COUNT(*) as t FROM data")->fetch_assoc()['t'];
$total_leaks = $conn->query("SELECT COUNT(*) as t FROM data WHERE vazando = 1")->fetch_assoc()['t'];
$total_maintenance = $conn->query("SELECT COUNT(*) as t FROM data WHERE manutencao = 1")->fetch_assoc()['t'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SmartLiquid</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #faf5ff 0%, #ffffff 50%, #faf5ff 100%); color: #111827; }
        
        /* Header & Layout */
        .header { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid #f3e8ff; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .header-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; height: 64px; }
        .logo-link { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #6d28d9); border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .logo-icon i { color: white; font-size: 20px; }
        .logo-text h1 { font-size: 24px; font-weight: 700; background: linear-gradient(to right, #7c3aed, #5b21b6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .logo-text p { font-size: 10px; color: #4b5563; }
        
        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary { background: linear-gradient(to right, #7c3aed, #6d28d9); color: white; }
        .btn-outline { background: white; color: #6d28d9; border: 2px solid #e9d5ff; }
        
        /* Main Content */
        .main { min-height: calc(100vh - 160px); padding: 40px 16px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .page-title { font-size: 36px; font-weight: 700; margin-bottom: 32px; }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 40px; }
        .stat-card { background: white; border: 1px solid #f3e8ff; border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.08); }
        .stat-icon { width: 64px; height: 64px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .stat-icon i { font-size: 28px; }
        .stat-value { font-size: 32px; font-weight: 700; }
        .stat-label { font-size: 14px; color: #4b5563; font-weight: 500; }
        
        /* Content Box (Previously Tabs) */
        .content-box { background: white; border: 1px solid #f3e8ff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1); padding: 32px; }
        
        /* Tables */
        .table-wrapper { overflow-x: auto; border-radius: 12px; border: 1px solid #f3e8ff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 16px; text-align: center; border-bottom: 1px solid #f3e8ff; white-space: nowrap; }
        th { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; font-size: 13px; text-transform: uppercase; }
        tr:hover { background: #faf5ff; }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 15px; font-size: 12px; font-weight: 600; min-width: 80px;}
        .status-on { background: rgba(76, 175, 80, 0.1); color: #16a34a; }   
        .status-off { background: rgba(128, 128, 128, 0.1); color: #666; }   
        .status-danger { background: rgba(220, 38, 38, 0.1); color: #dc2626; } 
        .status-warning { background: rgba(234, 88, 12, 0.1); color: #ea580c; } 
        .status-pending { background: rgba(37, 99, 235, 0.1); color: #2563eb; } 

        /* Estilo para linha de Alerta (Desconhecido) */
        tr.alerta-desconhecido {
            background-color: rgba(220, 38, 38, 0.08) !important;
            border-left: 4px solid #dc2626;
        }
        tr.alerta-desconhecido td {
            color: #b91c1c; 
            font-weight: 500;
        }

        .footer { background: linear-gradient(to right, #4c1d95, #5b21b6); color: white; padding: 32px 0; margin-top: 40px; }
        .footer-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; text-align: center; }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo-link">
                <div class="logo-icon"><i class="fas fa-droplet"></i></div>
                <div class="logo-text"><h1>SmartLiquid</h1><p>Controle Administrativo</p></div>
            </a>
            <div style="display:flex;gap:16px">
                <a href="index.php" class="btn btn-primary"><i class="fas fa-desktop"></i> Ver Painel</a>
                <a href="index.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <h1 class="page-title">Painel Administrativo</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $total_events ?></div>
                        <div class="stat-label">Total de Registros</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $total_leaks ?></div>
                        <div class="stat-label">Vazamentos Detectados</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(234, 88, 12, 0.1); color: #ea580c;">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $total_maintenance ?></div>
                        <div class="stat-label">Ciclos de Manutenção</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                        <i class="fas fa-temperature-high"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= number_format($avg_temp, 1) ?>°C</div>
                        <div class="stat-label">Temp. Média (Recente)</div>
                    </div>
                </div>
            </div>

            <div class="content-box">
                <h2 style="margin-bottom:24px"><i class="fas fa-history"></i> Histórico Detalhado do Sistema</h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Usuário</th>
                                <th>Operação</th>
                                <th>Torneira</th>
                                <th>Manutenção</th>
                                <th>Vazando</th>
                                <th>Temp.</th>
                                <th>Peso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($events_list as $row): 
                                
                                // 1. Traduzir Operação
                                $acao_tentada_code = $row["operacao_tentada"];
                                $acao_para_exibir = "N/A";
                                if ($acao_tentada_code == 'A') {
                                    $acao_para_exibir = "Torneira";
                                } else if ($acao_tentada_code == 'B') {
                                    $acao_para_exibir = "Manutenção";
                                } else if ($acao_tentada_code == 'C') {
                                    $acao_para_exibir = "Finalizar";
                                }
                                
                                // 2. Variáveis de Status
                                $torneira = $row['torneira'];
                                $manutencao = $row['manutencao'];
                                $vazando = $row['vazando'];
                                
                                // 3. Alerta de Usuário Desconhecido
                                $classe_linha = ($row['usuario'] == 'Desconhecido') ? 'alerta-desconhecido' : '';
                            ?>
                            <tr class="<?= $classe_linha ?>">
                                <td><?= date('d/m/Y H:i', strtotime($row['data_hora'])) ?></td>
                                <td>
                                    <?php if($row['usuario'] == 'Desconhecido'): ?>
                                        <i class="fas fa-user-secret"></i> 
                                    <?php endif; ?>
                                    <?= $row['usuario'] ?>
                                </td>
                                
                                <td><?= $acao_para_exibir ?></td>
                                
                                <td>
                                    <span class="status-badge <?= $torneira ? 'status-on' : 'status-off' ?>">
                                        <i class="fas fa-faucet"></i> <?= $torneira ? "Aberta" : "Fechada" ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="status-badge <?= $manutencao ? 'status-warning' : 'status-on' ?>">
                                        <i class="fas fa-tools"></i> <?= $manutencao ? "Em andamento" : "Concluída" ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="status-badge <?= $vazando ? 'status-danger' : 'status-on' ?>">
                                        <i class="fas fa-exclamation-triangle"></i> <?= $vazando ? "Sim" : "Não" ?>
                                    </span>
                                </td>
                                
                                <td><?= number_format($row['temperatura'], 1) ?>°C</td>
                                <td><?= number_format($row['peso'], 2) ?>kg</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>© 2025 SmartLiquid. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        setTimeout(function(){
            window.location.reload();
        }, 20000); // 20000 ms = 20 segundos
    </script>
</body>
</html>