<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - Conexau</title>
    <link rel="stylesheet" href="public/Assets/css/app.css">
</head>
<body>
    <?php include 'app/views/header.php'; ?>

    <div class="main-container">
        <header>
            <h1>Visão Geral Operacional</h1>
            <p>Bem-vindo ao centro de controle da clínica. Veja o andamento dos atendimentos domiciliares.</p>
        </header>

        <div class="metrics-grid">
            <div class="metric-card success">
                <div class="metric-label">Pacientes Atendidos</div>
                <div class="metric-value"><?= (int)$estatisticas['pacientes_atendidos'] ?></div>
            </div>

            <div class="metric-card primary">
                <div class="metric-label">Equipe em Campo</div>
                <div class="metric-value"><?= (int)$estatisticas['equipe_campo'] ?></div>
            </div>

            <div class="metric-card warning">
                <div class="metric-label">Visitas para Hoje</div>
                <div class="metric-value"><?= (int)$estatisticas['visitas_hoje'] ?></div>
            </div>

            <div class="metric-card error">
                <div class="metric-label">Triagens Pendentes</div>
                <div class="metric-value"><?= (int)$estatisticas['triagens_pendentes'] ?></div>
            </div>
        </div>

        <div class="table-container">
            <h2>Escala de Visitas em Andamento</h2>
            <p>Acompanhe quais profissionais estão atendendo nas residências neste momento.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Profissional</th>
                        <th>Especialidade/Função</th>
                        <th>Paciente</th>
                        <th>Horário Previsto</th>
                        <th>Status da Visita</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($escalaVisitas)): ?>
                        <tr>
                            <td colspan="5" class="sem-dados">
                                Nenhuma visita em andamento ou agendada para hoje.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($escalaVisitas as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['profissional_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($v['especialidade'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($v['paciente_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= htmlspecialchars($v['horario_inicio'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($v['horario_fim'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php if ($v['status'] === 'em_andamento'): ?>
                                        <span class="status-table pendente">Em Atendimento</span>
                                    <?php elseif ($v['status'] === 'agendado'): ?>
                                        <span class="status-table agendado">Agendado</span>
                                    <?php else: ?>
                                        <span class="status-table"><?= htmlspecialchars($v['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>