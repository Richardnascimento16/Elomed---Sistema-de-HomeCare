<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['usuario_nome'])){
    header("Location: index.php?controller=login&action=index");
    exit();
}

$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Usuario';
$perfilUsuario = isset($_SESSION['usuario_perfil']) ? strtoupper($_SESSION['usuario_perfil']) : 'NÃO DEFINIDO';
?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - Sistema</title>
    <link rel="stylesheet" href="/ProjetoModelo/public/asset/css/layout.css">
    <link rel="stylesheet" href="/ProjetoModelo/public/asset/css/forms.css">
    <style>
        /* ==========================================================================
   TOPBAR (BARRA SUPERIOR DE USUÁRIO)
   ========================================================================== */
.topbar { 
    background-color: #ffffff; 
    padding: 15px 30px; 
    display: flex; 
    justify-content: flex-end; /* Alinha o conteúdo todo para a direita */
    align-items: center; 
    border-bottom: 1px solid #e2e8f0; 
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

/* Container interno das informações do usuário */
.topbar .user-info-container {
    display: flex;
    align-items: center;
    gap: 15px; /* Espaçamento idêntico entre os blocos de texto e botões */
    font-size: 14px;
    color: #4a5568;
}

.topbar strong {
    color: #1e293b;
}

.topbar em {
    font-style: normal;
    background-color: #edf2f7;
    color: #4a5568;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

/* Link de Sair (Substitui o style="color: red" antigo) */
.topbar .btn-sair {
    color: #ef4444; 
    font-weight: 700; 
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 4px;
    transition: background-color 0.2s, color 0.2s;
}

.topbar .btn-sair:hover {
    background-color: #fee2e2;
    color: #dc2626;
}

/* Ajuste específico para o botão de Modo Escuro na barra */
.topbar .btn-dark-toggle {
    padding: 8px 14px;
    background-color: #3182ce;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
}

.topbar .btn-dark-toggle:hover {
    background-color: #2b6cb0;
}

/* ==========================================================================
   ESTILOS DA TOPBAR PARA O DARK MODE
   ========================================================================== */
body.dark-mode .topbar {
    background-color: #1e1e1e;
    border-color: #2d2d2d;
}

body.dark-mode .topbar .user-info-container {
    color: #cbd5e1;
}

body.dark-mode .topbar strong {
    color: #ffffff;
}

body.dark-mode .topbar em {
    background-color: #2d2d2d;
    color: #94a3b8;
}

body.dark-mode .topbar .btn-sair:hover {
    background-color: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}
    </style>
</head>
<body>
            <header class="topbar">
            <div>
                Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong> 
                (Perfil: <em><?= strtoupper(htmlspecialchars($_SESSION['usuario_perfil'] ?? 'ADMIN')) ?></em>) | 
                <a href="index.php?controller=login&action=sair" class="btn-sair">Sair</a>
                <button id="botao-escuro" onclick="modoEscuro()" class="btn-dark-toggle">Modo Escuro</button>
            </div>
        </header>
</body>
</html>