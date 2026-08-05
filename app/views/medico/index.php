<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Profissional - Conexau</title>
    <link rel="stylesheet" href="public/Assets/css/app.css">
</head>
<body>
<?php include 'app/views/header.php'; ?>

    <div class="main-container">
        <header>
            <?php foreach ($nome_profissional as $nome): ?>
                <h1>Bem-vindo(a), Dr(a). <?= htmlspecialchars($nome['nome_profissional'] ?? 'Não informado') ?></h1>
                <div class="badge badge-info"><?= htmlspecialchars($nome['numero_registro'] ?? 'Não informado') ?></div>
            <?php endforeach; ?>
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
            <?php if (isset($consulta_hoje) && is_array($consulta_hoje) && !empty($consulta_hoje)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Paciente</th>
                        <th>Status</th>
                        <th>Atendimento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consulta_hoje as $con): 
                        $data_banco = $con['data_agendamento'] ?? null;
                        $data_br = $data_banco ? date('d/m/Y', strtotime($data_banco)) : 'N/A';
                        
                        $inicio = substr($con['horario_inicio'] ?? '', 0, 5);
                        $fim = substr($con['horario_fim'] ?? '', 0, 5);
                        $horario = ($inicio && $fim) ? "$inicio - $fim" : 'Não informado';
                        
                        $status = $con['status'] ?? 'indisponivel';
                        $badge_class = $status;
                    ?>
                    <tr>
                        <td data-label="Data"><?= $data_br ?></td>
                        <td data-label="Horário"><?= $horario ?></td>
                        <td data-label="Paciente"><?= htmlspecialchars($con['nome_paciente'] ?? 'Não informado') ?></td>
                        <td data-label="Status"><span class="status-table <?= $badge_class ?>"><?= ucfirst($status) ?></span></td>
                        <td><a class="btn btn-primary" href='index.php?controller=Medico&action=iniciar_atendimento&id=<?= htmlspecialchars($con["id"]) ?>'>Iniciar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="sem-dados">Nenhum atendimento agendado para hoje</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>