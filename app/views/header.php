<?php
$paginaAtual = $_GET['controller'] ?? 'painel';
$action = $_GET['action'] ?? 'index';
$perfil = $_SESSION['usuario_perfil'] ?? '';
?>
<link rel="stylesheet" href="public/Assets/css/app.css">

<button class="menu-toggle" onclick="toggleSidebar()">☰</button>
<div class="overlay" onclick="toggleSidebar()"></div>

<nav class="sidebar" id="sidebar">
    <div class="logo">
        <span class="logo-icon">🚀</span>
        <span>Conexau</span>
    </div>

    <div class="titulo-side">Menu Principal</div>

    <a class="item <?= ($paginaAtual == 'painel') ? 'active' : '' ?>" 
       href="index.php?controller=painel&action=index">🏠 Dashboard</a>
       
    <?php if ($perfil === 'admin' || $perfil === 'medico'): ?>
    <div class="menu-item">
        <div class="item has-submenu <?= ($paginaAtual == 'Medico') ? 'active' : '' ?>" 
             onclick="toggleSubmenu('medico-submenu')">👨‍⚕️ Médico</div>
        <div id="medico-submenu" class="submenu <?= ($paginaAtual == 'Medico') ? 'open' : '' ?>">
            <a href="index.php?controller=Medico&action=index" 
               class="<?= ($paginaAtual == 'Medico' && $action == 'index') ? 'active' : '' ?>">Dashboard</a>
            <a href="index.php?controller=Medico&action=pendencias" 
               class="<?= ($paginaAtual == 'Medico' && $action == 'pendencias') ? 'active' : '' ?>">Pendências</a>
            <a href="index.php?controller=Medico&action=minha_agenda" 
               class="<?= ($paginaAtual == 'Medico' && $action == 'minha_agenda') ? 'active' : '' ?>">Minha Agenda</a>
            <a href="index.php?controller=Medico&action=historico_visitas" 
               class="<?= ($paginaAtual == 'Medico' && $action == 'historico_visitas') ? 'active' : '' ?>">Histórico de Visitas</a>
        </div>
    </div>
    <?php endif; ?>
       
    <?php if ($perfil === 'admin' || $perfil === 'paciente'): ?>
    <div class="menu-item">
        <div class="item has-submenu <?= ($paginaAtual == 'paciente') ? 'active' : '' ?>" 
             onclick="toggleSubmenu('paciente-submenu')">🤒 Paciente</div>
        <div id="paciente-submenu" class="submenu <?= ($paginaAtual == 'paciente') ? 'open' : '' ?>">
            <a href="index.php?controller=paciente&action=index" 
               class="<?= ($paginaAtual == 'paciente' && $action == 'index') ? 'active' : '' ?>">Dashboard</a>
            <a href="index.php?controller=paciente&action=minhas_triagens" 
               class="<?= ($paginaAtual == 'paciente' && $action == 'minhas_triagens') ? 'active' : '' ?>">Minhas Triagens</a>
            <a href="index.php?controller=agendamento&action=solicitar_agendamento" 
               class="<?= ($paginaAtual == 'agendamento') ? 'active' : '' ?>">Solicitar Agendamento</a>
            <a href="index.php?controller=paciente&action=agendamento" 
               class="<?= ($paginaAtual == 'paciente' && $action == 'agendamento') ? 'active' : '' ?>">Meus Agendamentos</a>
            <a href="index.php?controller=paciente&action=historico" 
               class="<?= ($paginaAtual == 'paciente' && $action == 'historico') ? 'active' : '' ?>">Histórico</a>
        </div>
    </div>
    <?php endif; ?>
       
    <?php if ($perfil === 'admin' || $perfil === 'enfermeiro'): ?>
    <div class="menu-item">
        <div class="item has-submenu <?= ($paginaAtual == 'Enfermeiro') ? 'active' : '' ?>" 
             onclick="toggleSubmenu('enfermeiro-submenu')">👩‍⚕️ Enfermeiro</div>
        <div id="enfermeiro-submenu" class="submenu <?= ($paginaAtual == 'Enfermeiro') ? 'open' : '' ?>">
            <a href="index.php?controller=Enfermeiro&action=index" 
               class="<?= ($paginaAtual == 'Enfermeiro' && $action == 'index') ? 'active' : '' ?>">Dashboard</a>
            <a href="index.php?controller=Enfermeiro&action=minha_agenda" 
               class="<?= ($paginaAtual == 'Enfermeiro' && $action == 'minha_agenda') ? 'active' : '' ?>">Minha Agenda</a>
            <a href="index.php?controller=Enfermeiro&action=historico" 
               class="<?= ($paginaAtual == 'Enfermeiro' && $action == 'historico') ? 'active' : '' ?>">Histórico</a>
        </div>
    </div>
    <?php endif; ?>
       
    <?php if ($perfil === 'admin' || $perfil === 'tecnico_enfermagem'): ?>
    <div class="menu-item">
        <div class="item has-submenu <?= ($paginaAtual == 'TecnicoEnfermagem') ? 'active' : '' ?>" 
             onclick="toggleSubmenu('tecnico-submenu')">🩺 Técnico Enfermagem</div>
        <div id="tecnico-submenu" class="submenu <?= ($paginaAtual == 'TecnicoEnfermagem') ? 'open' : '' ?>">
            <a href="index.php?controller=TecnicoEnfermagem&action=index" 
               class="<?= ($paginaAtual == 'TecnicoEnfermagem' && $action == 'index') ? 'active' : '' ?>">Dashboard</a>
            <a href="index.php?controller=TecnicoEnfermagem&action=minha_agenda" 
               class="<?= ($paginaAtual == 'TecnicoEnfermagem' && $action == 'minha_agenda') ? 'active' : '' ?>">Minha Agenda</a>
            <a href="index.php?controller=TecnicoEnfermagem&action=historico" 
               class="<?= ($paginaAtual == 'TecnicoEnfermagem' && $action == 'historico') ? 'active' : '' ?>">Histórico</a>
        </div>
    </div>
    <?php endif; ?>
       
    <a class="item <?= ($paginaAtual == 'usuario') ? 'active' : '' ?>" 
       href="index.php?controller=usuario&action=index">👤 Usuários</a>
    
    <div class="menu-separator"></div>
    
    <a class="item logout" href="index.php?controller=login&action=sair">🚪 Sair</a>
</nav>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.overlay');
    
    sidebar.classList.toggle('closed');
    overlay.classList.toggle('active');
}

function toggleSubmenu(id) {
    const submenu = document.getElementById(id);
    submenu.classList.toggle('open');
    
    // Fecha outros submenus
    const allSubmenus = document.querySelectorAll('.submenu');
    allSubmenus.forEach(sm => {
        if (sm.id !== id) {
            sm.classList.remove('open');
        }
    });
}

// Fecha sidebar ao clicar em um link em mobile
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            toggleSidebar();
        }
    });
});
</script>