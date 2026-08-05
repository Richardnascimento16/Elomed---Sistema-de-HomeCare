<?php
require_once __DIR__ . '/../../middleware/CSRF.php';
require_once __DIR__ . '/../../middleware/Session.php';
Session::start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Home Care Admin</title>
    
    <style>
        /* ==========================================================================
           ESTILO DO FORMULÁRIO DE EDIÇÃO E SUPORTE AO MODO ESCURO
           ========================================================================== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #f4f6f9; 
            color: #1e293b;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            transition: background 0.3s, color 0.3s;
        }
        body.dark-mode { background: #121212; color: #fff; }

        .profile-section { 
            background: white; 
            padding: 30px; 
            width: 100%;
            max-width: 500px; 
            border-radius: 12px; 
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: background 0.3s, border-color 0.3s;
        }
        body.dark-mode .profile-section { background: #1a1a1a; border-color: #2d2d2d; box-shadow: none; }

        .profile-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 24px;
            letter-spacing: -0.5px;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #64748b;
        }
        body.dark-mode label { color: #94a3b8; }

        input, select { 
            width: 100%; 
            padding: 10px 14px; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
            font-size: 0.95rem;
            background-color: #fff;
            color: inherit;
            transition: border-color 0.2s, background-color 0.3s;
        }
        input:focus, select:focus { border-color: #5b6cf9; outline: none; }
        
        body.dark-mode input, body.dark-mode select {
            background-color: #242424;
            border-color: #3d3d3d;
        }
        body.dark-mode input:focus, body.dark-mode select:focus { border-color: #5b6cf9; }

        /* Container de botões */
        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 28px;
        }

        /* Botões */
        .btn { 
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 10px 20px; 
            background: #5b6cf9; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.2s; 
        }
        .btn:hover { background: #4a54e1; }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }
        .btn-secondary:hover { background: #cbd5e1; }

        body.dark-mode .btn-secondary {
            background: #2d2d2d;
            color: #e2e8f0;
        }
        body.dark-mode .btn-secondary:hover { background: #3d3d3d; }
    </style>
</head>
<body>

<div class="profile-section">
    <h2>Editar Usuário</h2>
    
    <?php if (Session::hasFlash('error')): ?>
        <p style="color: #ef4444; margin-bottom: 20px;"><?= Session::getFlash('error') ?></p>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('success')): ?>
        <p style="color: #10b981; margin-bottom: 20px;"><?= Session::getFlash('success') ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <?= CSRF::getField() ?>
        
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-group">
            <label for="senha">Senha (Deixe em branco para manter a atual):</label>
            <input type="password" id="senha" name="senha">
        </div>

        <div class="form-group">
            <label for="perfil">Perfil do Usuário:</label>
            <select id="perfil" name="perfil" required>
                <option value="admin" <?= (isset($usuario['perfil']) && $usuario['perfil'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                <option value="medico" <?= (isset($usuario['perfil']) && $usuario['perfil'] === 'medico') ? 'selected' : '' ?>>Médico</option>
                <option value="enfermeiro" <?= (isset($usuario['perfil']) && $usuario['perfil'] === 'enfermeiro') ? 'selected' : '' ?>>Enfermeiro</option>
                <option value="tecnico_enfermagem" <?= (isset($usuario['perfil']) && $usuario['perfil'] === 'tecnico_enfermagem') ? 'selected' : '' ?>>Técnico de Enfermagem</option>
                <option value="paciente" <?= (isset($usuario['perfil']) && $usuario['perfil'] === 'paciente') ? 'selected' : '' ?>>Paciente</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Atualizar</button>
            <a href="index.php?controller=usuario&action=index" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</div>

</body>
</html>