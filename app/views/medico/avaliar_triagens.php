<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar Triagens - Conexau</title>
    <style>
        /* --- RESET E VARIÁVEIS COMPATÍVEIS --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --bg-primary: #f8fafc;
            --sidebar-bg: #0f172a;
            --text-main: #334155;
            --accent-medical: #0d9488;
            --accent-hover: #0f766e;
            --white: #ffffff;
            
            /* Cores de Ação */
            --color-approve: #10b981;
            --color-approve-hover: #059669;
            --color-reject: #ef4444;
            --color-reject-hover: #dc2626;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* --- SIDEBAR --- */
        aside {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--white);
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        aside .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 40px;
            text-align: center;
            color: #2dd4bf;
            letter-spacing: 1px;
        }

        aside nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        aside nav a {
            color: #94a3b8;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        aside nav a:hover, aside nav a.active {
            background-color: var(--accent-medical);
            color: var(--white);
        }

        aside .logout {
            margin-top: auto;
            color: #f87171;
            text-decoration: none;
            padding: 12px 15px;
            font-weight: 500;
        }

        /* --- CONTEÚDO PRINCIPAL --- */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        header {
            margin-bottom: 35px;
        }

        header h1 {
            font-size: 28px;
            color: #0f172a;
            margin-bottom: 5px;
        }

        header p {
            color: #64748b;
            font-size: 15px;
        }

        /* --- FILA DE CARDS DE TRIAGEM --- */
        .triagem-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 900px;
        }

        .triagem-card {
            background-color: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            border-left: 4px solid #f59e0b; /* Borda amarela indicando 'Pendente' */
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .triagem-info h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .triagem-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 14px;
            color: #64748b;
        }

        .triagem-meta span strong {
            color: #475569;
        }

        /* --- BOTÕES DE AÇÃO --- */
        .triagem-actions {
            display: flex;
            gap: 12px;
        }

        .btn-action {
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }

        .btn-action:active {
            transform: scale(0.98);
        }

        .btn-action.approve {
            background-color: var(--color-approve);
            color: var(--white);
        }

        .btn-action.approve:hover {
            background-color: var(--color-approve-hover);
        }

        .btn-action.reject {
            background-color: #fde8e8;
            color: var(--color-reject);
            border: 1px solid #fca5a5;
        }

        .btn-action.reject:hover {
            background-color: var(--color-reject);
            color: var(--white);
        }

        /* Caso não existam triagens */
        .empty-state {
            background-color: var(--white);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            color: #64748b;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        }

        /* RESPONSIVIDADE */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            aside {
                width: 100%;
            }
            main {
                padding: 20px;
            }
            .triagem-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .triagem-actions {
                width: 100%;
                margin-top: 10px;
            }
            .btn-action {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- BARRA LATERAL DO MÉDICO -->
    <aside>
        <div class="logo">CONEXAU</div>
        <nav>
            <a href="home_medico.php">Início</a>
            <a href="#" class="active">Avaliar Triagens</a>
            <a href="#">Minha Agenda</a>
            <a href="#">Histórico de Visitas</a>
        </nav>
        <a href="#" class="logout">Sair</a>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main>
        <header>
            <h1>Avaliar Triagens Pendentes</h1>
            <p>Analise os pedidos de atendimento e decida entre aprovar para agendamento ou recusar a solicitação.</p>
        </header>

        <!-- LISTA DE SOLICITAÇÕES DA TBL_TRIAGEM -->
        <div class="triagem-list">
            
            <!-- CARD 1: Exemplo Prático -->
            <div class="triagem-card">
                <div class="triagem-info">
                    <!-- Traz o nome_solicitante da tbl_triagem -->
                    <h3>Solicitante: Richard Santos</h3>
                    <div class="triagem-meta">
                        <span>Protocolo: <strong>#0023</strong></span>
                        <!-- Traz o nome da tbl_usuario vinculada à fk_paciente -->
                        <span>Paciente Alvo: <strong>Richard Santos (Próprio)</strong></span>
                        <!-- Traz o criado_em da tbl_triagem -->
                        <span>Enviado em: <strong>06/07/2026</strong></span>
                    </div>
                </div>
                <div class="triagem-actions">
                    <!-- Formulários para envio POST ao processamento backend -->
                    <form action="atualizar_status_triagem.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id_triagem" value="23">
                        <input type="hidden" name="acao" value="recusar">
                        <button type="submit" class="btn-action reject">Recusar</button>
                    </form>

                    <form action="atualizar_status_triagem.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id_triagem" value="23">
                        <input type="hidden" name="acao" value="aprovar">
                        <button type="submit" class="btn-action approve">Aprovar Pedido</button>
                    </form>
                </div>
            </div>

            <!-- CARD 2: Exemplo de quando um terceiro solicita (ex: irmão) -->
            <div class="triagem-card">
                <div class="triagem-info">
                    <h3>Solicitante: Richard Santos</h3>
                    <div class="triagem-meta">
                        <span>Protocolo: <strong>#0024</strong></span>
                        <span>Paciente Alvo: <strong>Christopher Santos (Irmão)</strong></span>
                        <span>Enviado em: <strong>06/07/2026 às 11:15</strong></span>
                    </div>
                </div>
                <div class="triagem-actions">
                    <form action="atualizar_status_triagem.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id_triagem" value="24">
                        <input type="hidden" name="acao" value="recusar">
                        <button type="submit" class="btn-action reject">Recusar</button>
                    </form>

                    <form action="atualizar_status_triagem.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id_triagem" value="24">
                        <input type="hidden" name="acao" value="aprovar">
                        <button type="submit" class="btn-action approve">Aprovar Pedido</button>
                    </form>
                </div>
            </div>

            <!-- EXEMPLO DE VISÃO DE COMPONENTE QUANDO A FILA ESTIVER LIMPA (Comente o bloco acima e tire o comentário abaixo para testar) -->
            <!-- 
            <div class="empty-state">
                <p style="font-size: 16px; font-weight: 500;">Não há nenhuma triagem pendente de análise no momento!</p>
                <p style="font-size: 14px; margin-top: 5px;">Bom trabalho. Toda a fila de solicitações foi processada.</p>
            </div> 
            -->

        </div>
    </main>

</body>
</html>