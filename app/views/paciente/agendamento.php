<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - Conexau</title>
    <link rel="stylesheet" href="public/Assets/css/app.css">
</head>
<body>
<?php include 'app/views/header.php'; ?>

    <div class="main-container">
        <header>
            <h1>Meus Agendamentos</h1>
            <p>Visualize e gerencie seus agendamentos médicos.</p>
        </header>

        <div class="table-container">
            <h2>Agendamentos</h2>
            <?php if (empty($agendamentos)): ?>
                <p class="sem-dados">Nenhum agendamento encontrado.</p>
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
                    <?php foreach ($agendamentos as $ag): 
                        $data_br = date('d/m/Y', strtotime($ag['data_agendamento']));
                        $horario = substr($ag['horario_inicio'], 0, 5) . ' - ' . substr($ag['horario_fim'], 0, 5);
                        $status = $ag['status'];
                    ?>
                    <tr>
                        <td data-label="Data"><?= $data_br ?></td>
                        <td data-label="Horário"><?= $horario ?></td>
                        <td data-label="Profissional"><?= htmlspecialchars($ag['nome_profissional']) ?></td>
                        <td data-label="Especialidade"><?= htmlspecialchars($ag['especialidade']) ?></td>
                        <td data-label="Status"><span class="status-table <?= $status ?>"><?= ucfirst($status) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
