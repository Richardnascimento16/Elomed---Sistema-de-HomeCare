<?php include 'app/views/header.php'; ?>

<div class="main-container">
    <header>
        <h1>Minha Agenda</h1>
        <p>Gerencie seus agendamentos futuros.</p>
    </header>

    <div class="table-container">
        <h2>Agendamentos Futuros</h2>
        <?php if (empty($agendamentos_futuros)): ?>
            <p class="sem-dados">Nenhum agendamento futuro encontrado.</p>
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
                <?php foreach ($agendamentos_futuros as $ag): 
                    $data_br = date('d/m/Y', strtotime($ag['data_agendamento']));
                    $horario = substr($ag['horario_inicio'], 0, 5) . ' - ' . substr($ag['horario_fim'], 0, 5);
                    $status = $ag['status'];
                ?>
                <tr>
                    <td><?= $data_br ?></td>
                    <td><?= $horario ?></td>
                    <td><?= htmlspecialchars($ag['nome_paciente']) ?></td>
                    <td><?= htmlspecialchars($ag['especialidade']) ?></td>
                    <td><span class="status-table <?= $status ?>"><?= ucfirst($status) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
