<?php
require_once __DIR__ . '/../../middleware/CSRF.php';
require_once __DIR__ . '/../../middleware/Session.php';
Session::start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Sistema Escolar</title>
    <link rel="stylesheet" href="public/Assets/css/index.css">
    <style>
        /* Reset básico para consistência entre navegadores */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Container Principal do Formulário */
        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px; /* Aumentado levemente para acomodar melhor a tabela */
            transition: max-width 0.3s ease;
        }

        /* Se a tabela estiver ativa, expande o card para ficar confortável de ler */
        .form-container.wide {
            max-width: 700px;
        }

        .form-container h2 {
            color: #2c3e50;
            margin-bottom: 24px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
        }

        /* Mensagem de Erro do PHP */
        .form-container p[style*="color: red"] {
            background-color: #fdf2f2;
            color: #de350b !important;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #de350b;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Labels */
        label {
            display: block;
            color: #4a5568;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Inputs e Selects */
        input[type="text"],
        input[type="search"],
        input[type="password"],
        select {
            width: 100% !important;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #fff;
            color: #334155;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            margin-bottom: 18px;
        }

        input[type="text"]:focus,
        input[type="search"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
        }

        /* Botões (Geral) */
        .btn {
            display: inline-block;
            width: 100% !important;
            padding: 12px;
            background-color: #3182ce;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-bottom: 10px;
        }

        form button.btn {
            margin-top: 10px;
        }

        form button.btn:hover, .btn:hover {
            background-color: #2b6cb0;
        }

        .btn-secondary {
            background-color: transparent;
            color: #3182ce;
            border: 1px solid #3182ce;
        }

        .btn-secondary:hover {
            background-color: #ebf8ff;
        }

        /* Estilização da Tabela Intermediária */
        .table-container {
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .resultado-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        .resultado-table th, .resultado-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .resultado-table th {
            background-color: #f8fafc;
            color: #4a5568;
            font-weight: 600;
        }

        .btn-select {
            padding: 6px 12px;
            font-size: 12px;
            background-color: #3182ce;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-select:hover {
            background-color: #2b6cb0;
        }
        
        .alert-info {
            background-color: #eff6ff;
            color: #1e40af;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body>
    <!-- A classe 'wide' é injetada dinamicamente via PHP se houver mais de um usuário na tabela -->
    <div class="form-container <?= (!empty($usuarios) && count($usuarios) > 1) ? 'wide' : '' ?>">
        <h2>Cadastrar Profissional</h2>
        
        <?php if (isset($erro)): ?>
            <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>
        
        <?php if (Session::hasFlash('error')): ?>
            <p style="color: red;"><?= Session::getFlash('error') ?></p>
        <?php endif; ?>
        
        <?php if (Session::hasFlash('success')): ?>
            <p style="color: #10b981;"><?= Session::getFlash('success') ?></p>
        <?php endif; ?>

        <!-- 1. BLOCO DE PESQUISA (Com DataList integrado) -->
        <div class="pesquisa">
            <form action="index.php" method="GET">
                <input type="hidden" name="controller" value="usuario">
                <input type="hidden" name="action" value="procurar">

                <label for="busca-nome">Buscar Usuário:</label>
                <!-- O atributo 'list' vincula este input ao datalist abaixo -->
                <input id="busca-nome" class="form-control" type="search" name="nome" placeholder="Digite o nome..." autocomplete="off" value="<?= isset($_GET['nome']) ? htmlspecialchars($_GET['nome'], ENT_QUOTES, 'UTF-8') : '' ?>">
                
                <!-- O DataList é alimentado se o seu Controller enviar a variável $todosOsUsuarios -->
                <datalist id="lista-usuarios">
                    <?php if (!empty($todosOsUsuarios)): ?>
                        <?php foreach ($todosOsUsuarios as $todUnico): ?>
                            <option value="<?= htmlspecialchars($todUnico['nome'], ENT_QUOTES, 'UTF-8') ?>"></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </datalist>

                <button class="btn" type="submit">Search</button>
            </form>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e2e8f0;">

        <!-- 2. FLUXO CONDICIONAL PHP DE RENDERIZAÇÃO -->
        <?php if (!empty($usuarios)): ?>
            
            <?php if (count($usuarios) > 1): ?>
                <!-- CASO A: Mais de um usuário encontrado -> Exibe a tabela intermediária -->
                <div class="table-container">
                    <div class="alert-info">
                        Múltiplos usuários encontrados. Escolha qual deseja configurar:
                    </div>
                    <table class="resultado-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Perfil</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($u['perfil'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <!-- Envia o ID exato para o controller trazer apenas 1 registro no próximo carregamento -->
                                        <a href="index.php?controller=usuario&action=procurar&id=<?= (int)$u['id'] ?>&nome=<?= urlencode($u['nome']) ?>" class="btn-select">
                                            Selecionar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <!-- CASO B: Apenas um usuário retornado -> Exibe o formulário final preenchido -->
                <?php $u = $usuarios[0]; // Captura o único usuário do array ?>
                
                <form action="index.php?controller=usuario&action=cadastrar_profissional" method="POST">
                    <?= CSRF::getField() ?>
                    
                    <!-- Passamos os dados de rotas do MVC ocultos -->
                    <input type="hidden" name="controller" value="usuario">
                    <input type="hidden" name="action" value="cadastrar_profissional">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">

                    <label>Nome:</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8') ?>" readonly style="background-color: #e2e8f0; cursor: not-allowed;">

                    <label>Email:</label>
                    <input type="text" name="email" value="<?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>" readonly style="background-color: #e2e8f0; cursor: not-allowed;">

                    <label>Perfil Atual:</label>
                    <input type="text" value="<?= htmlspecialchars($u['perfil'], ENT_QUOTES, 'UTF-8') ?>" readonly style="background-color: #e2e8f0; cursor: not-allowed;">

                    <label>Especialidade:</label>
                    <input type="text" name="especialidade" required placeholder="Ex: Médico Pediatra">

                    <label>Número De Registro:</label>
                    <input type="text" name="numero_registro" required placeholder="Ex: CRM/DF 12345">

                    <input type="submit" value="Finalizar Cadastro" class="btn">
                </form>
            <?php endif; ?>

        <?php endif; ?>

        <!-- Botão Voltar sempre visível na base do card -->
        <button type="button" class="btn btn-secondary" style="margin-top: 10px;" onclick="window.location.href='index.php?controller=usuario&action=index'">Voltar</button>
    </div>
</body>
</html>