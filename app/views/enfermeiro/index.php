<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Enfermeiro - Conexau</title>
    <link rel="stylesheet" href="public/Assets/css/app.css">
</head>
<body>
<?php include 'app/views/header.php'; ?>

    <div class="main-container">
        <header>
            <h1>Painel do Enfermeiro</h1>
            <p>Bem-vindo ao seu painel de controle de enfermagem.</p>
        </header>

        <div class="metrics-grid">
            <?php foreach ($visitas_hoje as $visitas): ?>
            <div class="metric-card primary">
                <div class="metric-label">Visitas Agendadas Hoje</div>
                <div class="metric-value"><?= htmlspecialchars($visitas['total_visitas'] ?? '') ?></div>
            </div>
            <?php endforeach; ?>
            <?php foreach ($visitas_concluidas_hoje as $visitas_conc): ?>
            <div class="metric-card success">
                <div class="metric-label">Visitas Concluídas</div>
                <div class="metric-value"><?= htmlspecialchars($visitas_conc['visitas_concluidas'] ?? '') ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="table-container">
            <h2>Fila de Atendimentos de Hoje</h2>
            <?php if (empty($fila_atendimentos)): ?>
                <p class="sem-dados">Nenhum atendimento agendado para hoje</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Paciente</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fila_atendimentos as $con): 
                        $data_banco = $con['data_agendamento'] ?? null;
                        $data_br = $data_banco ? date('d/m/Y', strtotime($data_banco)) : 'N/A';
                        
                        $inicio = substr($con['horario_inicio'] ?? '', 0, 5);
                        $fim = substr($con['horario_fim'] ?? '', 0, 5);
                        $horario = ($inicio && $fim) ? "$inicio - $fim" : 'Não informado';
                        
                        $status = $con['status'] ?? 'indisponivel';
                    ?>
                    <tr>
                        <td><?= $data_br ?></td>
                        <td><?= $horario ?></td>
                        <td><?= htmlspecialchars($con['nome_paciente'] ?? 'Não informado') ?></td>
                        <td><span class="status-table <?= $status ?>"><?= ucfirst($status) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>