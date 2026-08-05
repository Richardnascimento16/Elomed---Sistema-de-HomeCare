<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Triagens - Conexau</title>
    <link rel="stylesheet" href="public/Assets/css/app.css">
</head>
<body>
<?php include 'app/views/header.php'; ?>

    <div class="main-container">
        <header>
            <h1>Minhas Triagens</h1>
            <p>Solicite novos atendimentos e acompanhe a análise da nossa equipe médica.</p>
        </header>

        <div class="content-grid">
            
            <!-- COLUNA 1: FORMULÁRIO DE NOVA SOLICITAÇÃO -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nova Solicitação</h3>
                </div>
                <form action="index.php?controller=paciente&action=processar_triagem" method="POST">
                    <?= CSRF::getField() ?>
                    <div class="form-group">
                        <label for="nome_solicitante" class="form-label">Nome do Solicitante</label>
                        <input 
                            type="text" 
                            id="nome_solicitante" 
                            name="nome_solicitante" 
                            class="form-input"
                            placeholder="Ex: Richard ou Nome de um Dependente" 
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar para Análise</button>
                </form>
            </div>

            <!-- COLUNA 2: HISTÓRICO DE TRIAGENS -->
            <div class="table-container">
                <h2>Histórico de Solicitações</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Protocolo</th>
                            <th>Solicitante</th>
                            <th>Data de Envio</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($triagens)): ?>
                            <tr>
                                <td colspan="4" class="sem-dados">Nenhuma triagem encontrada.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($triagens as $triagem): 
                                $data_br = date('d/m/Y', strtotime($triagem['data_criacao']));
                                $status = $triagem['status'] ?? 'pendente';
                                $badge_class = $status;
                            ?>
                            <tr>
                                <td data-label="Protocolo">#<?= htmlspecialchars($triagem['id']) ?></td>
                                <td data-label="Solicitante"><?= htmlspecialchars($triagem['nome_solicitante']) ?></td>
                                <td data-label="Data de Envio"><?= $data_br ?></td>
                                <td data-label="Status"><span class="status-table <?= $badge_class ?>"><?= ucfirst($status) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>
</html>