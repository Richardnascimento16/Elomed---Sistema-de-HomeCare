<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendências - Conexau</title>
    <style>
        /* --- RESET E CONFIGURAÇÕES GERAIS --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --bg-primary: #f4f6f9;
            --text-main: #334155;
            --accent-color: #0284c7;
            --accent-hover: #0369a1;
            --white: #ffffff;
            
            /* Cores de Status */
            --status-agendado: #def7ec;
            --status-text-agendado: #03543f;
            --status-pendente: #fef3c7;
            --status-text-pendente: #92400e;
            --status-concluido: #e2e8f0;
            --status-text-concluido: #475569;
            --status-cancelado: #fde8e8;
            --status-text-cancelado: #9b1c1c;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* --- CONTEÚDO PRINCIPAL --- */
        .main-container {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            margin-left: 0;
        }
        
        @media (min-width: 769px) {
            .main-container {
                margin-left: 260px;
            }
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h1 {
            font-size: 28px;
            color: #0f172a;
        }

        header .user-info {
            font-weight: 500;
        }

        /* --- CARDS DE RESUMO --- */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background-color: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card h3 {
            font-size: 14px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        .card .value {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            width: fit-content;
        }

        .badge.pendente {
            background-color: var(--status-pendente);
            color: var(--status-text-pendente);
        }

        .badge.agendado {
            background-color: var(--status-agendado);
            color: var(--status-text-agendado);
        }
        
        .badge.concluido {
            background-color: var(--status-concluido);
            color: var(--status-text-concluido);
        }
        
        .badge.cancelado {
            background-color: var(--status-cancelado);
            color: var(--status-text-cancelado);
        }

        .card .btn-solicitar {
            background-color: var(--accent-color);
            color: var(--white);
            text-decoration: none;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-weight: 500;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .card .btn-solicitar:hover {
            background-color: var(--accent-hover);
        }

        .sem-dados {
            color: #94a3b8;
            font-style: italic;
            font-size: 14px;
        }

        /* --- TABELA DE PRÓXIMOS EVENTOS --- */
        .section-container {
            background-color: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .section-container h2 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 15px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-table {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-table.agendado {
            background-color: var(--status-agendado);
            color: var(--status-text-agendado);
        }
        
        .status-table.pendente {
            background-color: var(--status-pendente);
            color: var(--status-text-pendente);
        }
        
        .status-table.concluido {
            background-color: var(--status-concluido);
            color: var(--status-text-concluido);
        }
        
        .status-table.cancelado {
            background-color: var(--status-cancelado);
            color: var(--status-text-cancelado);
        }

        /* RESPONSIVIDADE */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .main-container {
                padding: 20px;
            }
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr {
                border: 1px solid #e2e8f0;
                margin-bottom: 10px;
                border-radius: 8px;
                padding: 10px;
            }
            td {
                border: none;
                padding: 8px 4px;
                display: flex;
                justify-content: space-between;
            }
            td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #64748b;
            }
        }

        .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: background-color 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-aprov {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-aprov:hover {
        background-color: #bae6fd;
    }

    .btn-reject {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .btn-reject:hover {
        background-color: #fecaca;
    }
    </style>
        <link rel="stylesheet" href="public/Assets/css/index.css">
</head>
<body>
<?php include 'app/views/header.php'; ?>

    <div class="main-container">
    <!-- CONTEÚDO PRINCIPAL -->
        <!-- GRID DE CARDS RÁPIDOS -->
        <div class="cards-grid">

<!-- CARD 1: PRÓXIMO AGENDAMENTO PENDENTE -->
<div class="card">
    <h3>Agendamentos Pendentes</h3>
    
    <?php if (!empty($aprovar_agendamento) && is_array($aprovar_agendamento)): ?>
        <?php foreach ($aprovar_agendamento as $ag): ?>
            <div class="agendamento-item" style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #e2e8f0;">
                <div class="value" style="font-size: 16px; margin-bottom: 5px;">
                    <!-- Mudado para nome_paciente -->
                    <?= htmlspecialchars($ag['nome_paciente']) ?>
                </div>
                <div style="font-size: 14px; color: #64748b;">
                    <?= date('d/m/Y', strtotime($ag['data_agendamento'])) ?> 
                    às <?= substr($ag['horario_inicio'], 0, 5) ?>
                </div>
                
                <div class="action-buttons" style="margin-top: 10px;">
                    <a href="index.php?controller=Medico&action=aceitar&id=<?= (int)$ag['id'] ?>" class="btn-action btn-aprov">Aceitar</a>
                    <a href="index.php?controller=Medico&action=rejeitar&id=<?= (int)$ag['id'] ?>" class="btn-action btn-reject" onclick="return confirm('Deseja realmente rejeitar este agendamento?')">Rejeitar</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="value sem-dados">Nenhum agendamento pendente</div>
    <?php endif; ?>
</div>
    </main>

</body>
</html>