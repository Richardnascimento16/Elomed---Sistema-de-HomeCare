<?php
require_once __DIR__ . '/../../middleware/CSRF.php';
require_once __DIR__ . '/../../middleware/Session.php';
Session::start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema Escolar</title>
    <link rel="stylesheet" href="public/Assets/css/index.css">
    <style>
        /* Reset básico para garantir consistência entre navegadores */
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
}

/* Container Principal */
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

/* Mensagem de Erro */
.form-container p[style*="color: red"] {
    background-color: #fdf2f2;
    color: #de350b !important;
    padding: 10px;
    border-radius: 4px;
    border-left: 4px solid #de350b;
    margin-bottom: 20px;
    font-size: 14px;
}

/* Labels e Inputs */
label {
    display: block;
    color: #4a5568;
    margin-bottom: 6px;
    font-size: 14px;
    font-weight: 500;
}

input[type="text"],
input[type="password"],
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background-color: #fff;
    color: #334155;
    font-size: 15px;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    margin-bottom: 18px; /* Substitui a necessidade de <br> repetidos */
}

input[type="text"]:focus,
input[type="password"]:focus,
select:focus {
    border-color: #3182ce;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
}

/* Botões */
.btn {
    display: inline-block;
    width: 100%;
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

.btn:hover {
    background-color: #2b6cb0;
}

/* Link de Cadastro e ajustes finais */
.cadastro-link {
    margin-top: 24px;
    border-top: 1px solid #e2e8f0;
    padding-top: 20px;
}

.cadastro-link p {
    color: #718096;
    font-size: 14px;
    margin-bottom: 12px;
}

/* Estilo secundário para o botão de cadastro não brigar com o de login */
.cadastro-link .btn {
    background-color: transparent;
    color: #3182ce;
    border: 1px solid #3182ce;
}

.cadastro-link .btn:hover {
    background-color: #ebf8ff;
}
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Acesso ao Sistema</h2>
        
        <?php if (isset($erro)): ?>
            <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>
        
        <?php if (Session::hasFlash('error')): ?>
            <p style="color: red;"><?= Session::getFlash('error') ?></p>
        <?php endif; ?>

        <form action="index.php?controller=login&action=logar" method="POST">
            <?= CSRF::getField() ?>
            
            <label>Email:</label><br>
            <input type="email" name="email" required style="width: 100%;"><br><br>

            <label>Senha:</label><br>
            <input type="password" name="senha" required style="width: 100%;"><br><br>

            <label>Perfil:</label>
            <select name="perfil">
            <option value="admin">Administrador</option>
            <option value="medico">Médico</option>
            <option value="enfermeiro">Enfermeiro</option>
            <option value="tecnico_enfermagem">Tec.Enfermagem</option>
            <option value="paciente">Paciente</option>
        </select>

            <br><br>
            <button type="submit" style="width: 100%;" class="btn">Entrar</button>
            <div class="cadastro-link" style="margin-top: 10px; text-align: center;">
                <p>Ainda não tem uma conta?</p>
            
            <a href="index.php?controller=cadastro&action=index" class="btn">Cadastrar</a>
            </div>
        </form>
    </div>
</body>
</html>