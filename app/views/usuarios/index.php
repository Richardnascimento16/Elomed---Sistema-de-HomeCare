<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Lista de Usuários</title>
    <link rel="stylesheet" href="public/Assets/css/index.css">
    <style>
        /* ==========================================================================
   ESTILIZAÇÃO DA TABELA DE USUÁRIOS E AÇÕES
   ========================================================================== */
.user-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 20px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    transition: background 0.3s, border-color 0.3s;
}

.user-table th, .user-table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
    transition: border-color 0.3s;
}

.user-table th {
    background: #f8fafc;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.user-table tbody tr:last-child td {
    border-bottom: none;
}

/* Estilização dos Links de Ações como Botões */
.action-buttons a {
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 4px;
    transition: 0.2s;
    display: inline-block;
}

.action-buttons .btn-edit {
    background: #e0e7ff;
    color: #4338ca;
}

.action-buttons .btn-edit:hover {
    background: #c7d2fe;
}

.action-buttons .btn-delete {
    background: #fee2e2;
    color: #b91c1c;
}

.action-buttons .btn-delete:hover {
    background: #fecaca;
}

/* Regras de Modo Escuro para a Tabela */
body.dark-mode .user-table {
    background: #1a1a1a;
    border-color: #2d2d2d;
    box-shadow: none;
}

body.dark-mode .user-table th {
    background: #242424;
    color: #94a3b8;
    border-bottom: 1px solid #2d2d2d;
}

body.dark-mode .user-table td {
    border-bottom: 1px solid #2d2d2d;
    color: #e2e8f0;
}

body.dark-mode .action-buttons .btn-edit {
    background: rgba(99, 102, 241, 0.15);
    color: #818cf8;
}

body.dark-mode .action-buttons .btn-edit:hover {
    background: rgba(99, 102, 241, 0.25);
}

body.dark-mode .action-buttons .btn-delete {
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
}

body.dark-mode .action-buttons .btn-delete:hover {
    background: rgba(239, 68, 68, 0.25);
}
    </style>
</head>
<body>
    <?php
    include 'app/views/header.php'
    ?>

    <div class="main-container">
        <div class="topbar">
            <?php include 'app/views/pesquisa.php'; ?>
    </div>
    

    <?php
     include 'app/views/usuarios/content.php';
    ?>
    <nav class="navbar bg-body-tertiary">
  <form class="container-fluid justify-content-start">
    <a href="index.php?controller=usuario&action=cadastrar" class="btn btn-outline-success me-2" type="button">Novo Usuário</a>
    <a href="index.php?controller=usuario&action=cadastrar_profissional" class="btn btn-outline-success me-2" type="button">Cadastrar Profissional</a>
    <a href="index.php?controller=usuario&action=cadastrar_paciente" class="btn btn-outline-success me-2" type="button">Cadastrar Paciente</a>
  </form>
</nav>
    
    <?php
     include 'app/views/footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
</body>
</html>