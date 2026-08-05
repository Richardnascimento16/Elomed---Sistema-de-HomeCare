<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Paciente - Conexau</title>
    <link rel="stylesheet" href="public/Assets/css/app.css">
</head>
<body>
<?php include 'app/views/header.php'; ?>

    <div class="main-container">
    <header>
        <h1>Painel do Paciente</h1>
        <p>Bem-vindo ao seu painel de controle</p>
    </header>

    <!-- GRID DE CARDS RÁPIDOS -->
    <div class="metrics-grid">
            
            <!-- CARD 1: ÚLTIMA TRIAGEM -->
            <div class="metric-card primary">
                <div class="metric-label">Última Solicitação</div>
               <?php if ($ultima_triagem): ?>
                    <div class="metric-value"><?= htmlspecialchars($ultima_triagem['nome_solicitante']) ?></div>
                    <?php 
                        $status_triagem = $ultima_triagem['status'];
                        $badge_class = 'badge-warning';
                        if ($status_triagem === 'aprovada') $badge_class = 'badge-success';
                        elseif ($status_triagem === 'recusada') $badge_class = 'badge-error';
                    ?>
                    <span class="badge <?= $badge_class ?>"><?= ucfirst($status_triagem) ?></span>
                <?php else: ?>
                    <div class="metric-value sem-dados">Nenhuma triagem</div>
                <?php endif; ?>
            </div>

            <!-- CARD 2: PRÓXIMO AGENDAMENTO -->
            <div class="metric-card success">
                <div class="metric-label">Próximo Atendimento</div>
                <?php if (is_array($proximo_agendamento) && $proximo_agendamento['status'] !== 'pendente' && $proximo_agendamento['status'] !== 'cancelado'): ?>
                    <div class="metric-value" style="font-size: 18px;">
                        <?= htmlspecialchars($proximo_agendamento['nome_profissional'] ?? '') ?>
                    </div>
                    <div style="font-size: 14px; color: #64748b;">
                        <?php 
                            $data = $proximo_agendamento['data_agendamento'] ?? null;
                            echo $data ? date('d/m/Y', strtotime($data)) : ''; 
                        ?> 
                        às <?= substr($proximo_agendamento['horario_inicio'] ?? '', 0, 5) ?>
                    </div>
                    <span class="badge badge-success" style="margin-top: 10px;">Confirmado</span>
                <?php else: ?>
                    <div class="metric-value sem-dados">Nenhum agendamento</div>
                <?php endif; ?>
            </div>

            <!-- CARD 3: AÇÃO RÁPIDA -->
            <div class="metric-card warning">
                <div class="metric-label">Ação Rápida</div>
                <div class="metric-value" style="font-size: 16px;">Solicitar Agendamento</div>
                <a href="index.php?controller=agendamento&action=solicitar_agendamento" class="btn btn-primary" style="margin-top: 10px;">Iniciar Triagem</a>
            </div>

        </div>

        <!-- TABELA DE AGENDAMENTOS FUTUROS -->
        <div class="table-container">
            <h2>Agenda de Compromissos</h2>
            <?php if (empty($agendamentos_futuros)): ?>
                <p class="sem-dados">Nenhum agendamento futuro encontrado.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Profissional</th>
                        <th>Especialidade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos_futuros as $ag): 
                        $data_br = date('d/m/Y', strtotime($ag['data_agendamento']));
                        $horario = substr($ag['horario_inicio'], 0, 5) . ' - ' . substr($ag['horario_fim'], 0, 5);
                        $status = $ag['status'];
                        $badge_class = $status;
                    ?>
                    <tr>
                        <td data-label="Data"><?= $data_br ?></td>
                        <td data-label="Horário"><?= $horario ?></td>
                        <td data-label="Profissional"><?= htmlspecialchars($ag['nome_profissional']) ?></td>
                        <td data-label="Especialidade"><?= htmlspecialchars($ag['especialidade']) ?></td>
                        <td data-label="Status"><span class="status-table <?= $badge_class ?>"><?= ucfirst($status) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>