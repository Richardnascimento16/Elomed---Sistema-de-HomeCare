<?php include 'app/views/header.php'; ?>

<div class="main-container">
    <header>
        <h1>Histórico de Visitas</h1>
        <p>Visualize todas as visitas concluídas realizadas.</p>
    </header>

    <div class="table-container">
        <h2>Visitas Concluídas</h2>
        <?php if (empty($visitas_concluidas)): ?>
            <p class="sem-dados">Nenhuma visita concluída encontrada.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Paciente</th>
                    <th>Especialidade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visitas_concluidas as $visita): 
                    $data_br = date('d/m/Y', strtotime($visita['data_agendamento']));
                    $horario = substr($visita['horario_inicio'], 0, 5) . ' - ' . substr($visita['horario_fim'], 0, 5);
                    $status = $visita['status'];
                ?>
                <tr>
                    <td><?= $data_br ?></td>
                    <td><?= $horario ?></td>
                    <td><?= htmlspecialchars($visita['nome_paciente']) ?></td>
                    <td><?= htmlspecialchars($visita['especialidade']) ?></td>
                    <td><span class="status-table <?= $status ?>"><?= ucfirst($status) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
