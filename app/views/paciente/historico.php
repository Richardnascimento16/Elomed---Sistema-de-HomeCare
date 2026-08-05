<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Conexau</title>
    <link rel="stylesheet" href="public/Assets/css/app.css">
</head>
<body>
<?php include 'app/views/header.php'; ?>

    <div class="main-container">
        <header>
            <h1>Histórico de Visitas</h1>
            <p>Visualize todas as suas consultas médicas anteriores.</p>
        </header>

        <div class="table-container">
            <h2>Histórico</h2>
            <?php if (empty($visitas)): ?>
                <p class="sem-dados">Nenhuma visita concluída encontrada.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Profissional</th>
                        <th>Especialidade</th>
                        <th>Conduta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visitas as $visita): 
                        $data_br = date('d/m/Y', strtotime($visita['data_agendamento']));
                        $horario = substr($visita['horario_inicio'], 0, 5) . ' - ' . substr($visita['horario_fim'], 0, 5);
                        $conduta = $visita['conduta_medica'] ?? 'Não informado';
                    ?>
                    <tr>
                        <td data-label="Data"><?= $data_br ?></td>
                        <td data-label="Horário"><?= $horario ?></td>
                        <td data-label="Profissional"><?= htmlspecialchars($visita['nome_profissional']) ?></td>
                        <td data-label="Especialidade"><?= htmlspecialchars($visita['especialidade']) ?></td>
                        <td data-label="Conduta"><?= htmlspecialchars(substr($conduta, 0, 50)) ?><?= strlen($conduta) > 50 ? '...' : '' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
