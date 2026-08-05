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
    <style>/* Reset básico para consistência entre navegadores */
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
    padding: 20px; /* Evita que o card cole nas bordas em telas pequenas */
}

/* Container Principal do Formulário */
.form-container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    width: 100%;
    max-width: 400px;
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
    color: #de350b !important; /* Força o vermelho correto sobre o estilo inline */
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
input[type="password"],
select {
    width: 100% !important; /* Sobrescreve o inline temporariamente */
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background-color: #fff;
    color: #334155;
    font-size: 15px;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    margin-bottom: 18px; /* Controla o espaçamento sem precisar de tantos <br> */
}

input[type="text"]:focus,
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
}

/* Botão principal de envio */
form button.btn {
    margin-top: 10px;
}

form button.btn:hover {
    background-color: #2b6cb0;
}

/* Link para retornar ao Login */
.login-link {
    margin-top: 24px;
    border-top: 1px solid #e2e8f0;
    padding-top: 20px;
}

.login-link p {
    color: #718096;
    font-size: 14px;
    margin-bottom: 12px;
}

/* Estilo do botão secundário (Faça login) para diferenciar da ação principal */
.login-link .btn {
    background-color: transparent;
    color: #3182ce;
    border: 1px solid #3182ce;
}

.login-link .btn:hover {
    background-color: #ebf8ff;
}
</style>
</head>
<body>
    <div class="form-container">
    <h2>Formulário de Agendamento</h2>
    
    <?php if (isset($erro)): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('error')): ?>
        <p style="color: red;"><?= Session::getFlash('error') ?></p>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('success')): ?>
        <p style="color: #10b981; background-color: #ecfdf5; padding: 10px; border-radius: 4px; border-left: 4px solid #22c55e;"><?= Session::getFlash('success') ?></p>
    <?php endif; ?>

    <form action="index.php?controller=agendamento&action=enviar_dados" method="POST">
        <?= CSRF::getField() ?>
        
        <!-- Evite enviar controller/action via POST para não sobrescrever a rota do GET -->
        <!-- Mudamos o name para fk_paciente -->
<input type="hidden" name="fk_usuario" value="<?= (int)($id_paciente ?? 0) ?>">

        <label>Data:</label><br>
        <input type="date" name="data" required style="width: 100%;" placeholder="dd/mm/aaaa"><br><br>

        <label>Horário do início da visita:</label><br>
        <input type="time" name="horario_inicio" required style="width: 100%;" placeholder="00:00:00"><br><br>

        <label>Horário do fim da visita:</label><br>
        <input type="time" name="horario_fim" required style="width: 100%;" placeholder="00:00:00"><br><br>
        
        <div class="form-group">
            <label for="fk_profissional">Selecione o Profissional de Saúde:</label>
            <select name="fk_profissional" id="fk_profissional" required class="form-control" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="">-- Escolha quem vai te atender --</option>

                <?php if (!empty($profissionais) && is_array($profissionais)): ?>
                    <optgroup label="Profissionais">
                        <?php foreach ($profissionais as $prof): ?>
                            <option value="<?= $prof['id_profissional'] ?>">
                                <?= htmlspecialchars($prof['nome_profissional']) ?> - <?= htmlspecialchars($prof['especialidade']) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php else: ?>
                    <option value="" disabled>Nenhum profissional disponível no momento</option>
                <?php endif; ?>
            </select>
        </div>
        <br><br>
        
        <button type="submit" style="width: 100%;" class="btn">Solicitar</button>
        <button type="button" style="width: 100%; margin-top: 10px;" class="btn" onclick="window.location.href='index.php?controller=paciente&action=index'">Voltar</button>
    </form>
</div>