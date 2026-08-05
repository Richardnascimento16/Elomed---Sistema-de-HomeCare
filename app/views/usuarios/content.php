<!-- Bloco de Estilos CSS - Adicione na sua tag <head> ou arquivo CSS separado -->
<style>
    /* Estilos para a área de gerenciamento */
    .management-container {
        width: 100%;
        max-width: 1000px;
        margin: 20px auto;
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .management-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .management-header h2 {
        color: #2c3e50;
        font-size: 24px;
        font-weight: 600;
    }

    /* Formulário de Busca Alinhado */
    .search-box {
        display: flex;
        gap: 8px;
    }

    .search-input {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        width: 240px;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        border-color: #3182ce;
    }

    /* Tabela Otimizada */
    .user-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        background-color: #fff;
        font-size: 15px;
    }

    .user-table th {
        background-color: #f8fafc;
        color: #4a5568;
        font-weight: 600;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
    }

    .user-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
    }

    .user-table tr:hover {
        background-color: #f1f5f9;
    }

    /* Badges de Perfil */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-admin { background-color: #fee2e2; color: #991b1b; }
    .badge-enfermeiro { background-color: #fef3c7; color: #92400e; }
    .badge-paciente { background-color: #e0f2fe; color: #0369a1; }
    .badge-default { background-color: #f1f5f9; color: #475569; }

    /* Botões de Ações Modernos (Sem aquele '|' solto) */
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

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-delete {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .btn-delete:hover {
        background-color: #fecaca;
    }

    .btn-search {
        background-color: #3182ce;
        color: #fff;
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-search:hover { background-color: #2b6cb0; }

    /* Alertas */
    .search-alert {
        background-color: #f0fdf4;
        border-left: 4px solid #16a34a;
        color: #14532d;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>

<div class="management-container">
    <h2>Controle de Usuarios</h2>
    <!-- Feedback visual se uma busca por nome foi feita -->
    <?php if (isset($usuarioEncontrado) && !empty($usuarioEncontrado)): ?>
        <div class="search-alert">
            <strong>Filtro ativo:</strong> Exibindo resultados encontrados na busca.
        </div>
    <?php endif; ?>

    <!-- Tabela de Gerenciamento -->
    <table class="user-table">
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th style="width: 180px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b; padding: 30px;">
                        Nenhum registro encontrado no sistema.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><strong>#<?= (int)$u['id'] ?></strong></td>
                        <td><?= htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <!-- Badges Coloridas Dinâmicas conforme o Perfil -->
                            <?php 
                                $perfilLow = mb_strtolower($u['perfil']);
                                $badgeClass = 'badge-default';
                                if (str_contains($perfilLow, 'admin')) $badgeClass = 'badge-admin';
                                elseif (str_contains($perfilLow, 'enfermeiro')) $badgeClass = 'badge-enfermeiro';
                                elseif (str_contains($perfilLow, 'paciente')) $badgeClass = 'badge-paciente';
                            ?>
                            <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($u['perfil'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?controller=usuario&action=editar&id=<?= (int)$u['id'] ?>" class="btn-action btn-edit">Editar</a>
                                <a href="index.php?controller=usuario&action=excluir&id=<?= (int)$u['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Deseja realmente excluir este usuário?')">Excluir</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>