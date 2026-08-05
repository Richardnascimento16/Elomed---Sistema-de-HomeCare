<?php
require_once __DIR__ . '/../../middleware/CSRF.php';
require_once __DIR__ . '/../../middleware/Session.php';
Session::start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise da IA - Diagnóstico</title>
    <style>
        :root {
            --primary: #0284c7;
            --primary-light: #e0f2fe;
            --secondary: #0f172a;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border: #e2e8f0;
            --text: #334155;
            --text-light: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --ai-glow: #8b5cf6;
            --ai-bg: #f5f3ff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text);
            padding: 20px;
            line-height: 1.5;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
            background: var(--bg-card);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 20px;
            position: relative;
        }

        .ai-badge {
            background: linear-gradient(135deg, var(--ai-glow), var(--primary));
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 10px;
        }

        header h1 {
            color: var(--secondary);
            font-size: 24px;
            margin-bottom: 5px;
        }

        .summary-card {
            background-color: #f1f5f9;
            border-left: 4px solid var(--text-light);
            padding: 15px 20px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .summary-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .ai-analysis-container {
            background-color: var(--ai-bg);
            border: 1px solid #ddd6fe;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            position: relative;
        }

        .ai-section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--ai-glow);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .insight-box {
            background: white;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #edf2f7;
            font-size: 14px;
        }

        .insight-box.alert {
            border-left: 4px solid var(--warning);
        }

        .insight-box.success {
            border-left: 4px solid var(--success);
        }

        fieldset {
            border: none;
            margin-bottom: 30px;
        }

        legend {
            font-size: 18px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 15px;
            width: 100%;
            border-bottom: 1px solid var(--border);
            padding-bottom: 5px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--secondary);
            display: block;
        }

        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            color: var(--text);
            outline: none;
            resize: vertical;
        }

        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
            padding-top: 20px;
        }

        .btn-back {
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-back:hover {
            color: var(--secondary);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), #0369a1);
            color: white;
            border: none;
            padding: 14px 35px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--ai-glow);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="ai-badge">Módulo de IA Ativo</div>
            <h1>Análise Diagnóstica Auxiliada</h1>
            <p>Validação de sintomas e recomendações farmacológicas</p>
        </header>

        <!-- 1. RESUMO DOS DADOS INFORMADOS NA PÁGINA ANTERIOR -->
        <div class="summary-card">
            <div class="summary-title">Resumo Clínico Coletado:</div>
            
            <?php if (!empty($protocolo_id) && !empty($respostas_protocolo)): ?>
                <!-- Mostra dados do protocolo se foi selecionado -->
                <p><strong>Protocolo Selecionado:</strong> Protocolo ID <?= htmlspecialchars($protocolo_id) ?></p>
                <p><strong>Respostas do Protocolo:</strong></p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <?php 
                    // Cria um mapa de perguntas para facilitar a busca
                    $perguntas_map = [];
                    if (!empty($perguntas_protocolo)) {
                        foreach ($perguntas_protocolo as $pergunta) {
                            $perguntas_map[$pergunta['id']] = $pergunta['pergunta'];
                        }
                    }
                    
                    foreach ($respostas_protocolo as $pergunta_id => $resposta): 
                        $texto_pergunta = isset($perguntas_map[$pergunta_id]) ? $perguntas_map[$pergunta_id] : "Pergunta ID $pergunta_id";
                    ?>
                        <li><strong><?= htmlspecialchars($texto_pergunta) ?>:</strong> <?= htmlspecialchars($resposta) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <!-- Mostra dados da anamnese genérica se nenhum protocolo foi selecionado -->
                <p><strong>Sintomas relatados:</strong> <?= htmlspecialchars($queixa ?? 'Não informado') ?></p>
                <p><strong>Tempo de evolução:</strong> <?= htmlspecialchars($tempo ?? 'Não informado') ?></p>
                <p><strong>Alergias:</strong> <?= htmlspecialchars($alergia ?? 'Nenhuma') ?></p>
                <p><strong>Histórico:</strong> <?= htmlspecialchars($historico ?? 'Não informado') ?></p>
            <?php endif; ?>
            
            <!-- Sinais vitais sempre são mostrados -->
            <p style="margin-top: 15px;"><strong>Sinais Vitais:</strong> 
                PA: <?= htmlspecialchars($pa ?? 'Não medido') ?> | 
                FC: <?= htmlspecialchars($fc ?? 'Não medido') ?> bpm | 
                Temp: <?= htmlspecialchars($temperatura ?? 'Não medida') ?> | 
                Glicemia: <?= htmlspecialchars($glicemia ?? 'Não medida') ?> mg/dL
            </p>
        </div>

        <!-- 2. PAINEL DE RETORNO DA INTELIGÊNCIA ARTIFICIAL -->
        <div class="ai-analysis-container">
            <div class="ai-section-title">
                ✨ Insights e Sugestões Preditivas da IA
            </div>

            <div class="insight-box">
                <strong>Correlação de Diagnósticos Prováveis:</strong>
                <p style="margin-top: 5px; color: var(--text-light);">
                    <?php
                    $correlacao = $insights['correlacao'] ?? 'Análise não disponível.';
                    if (is_array($correlacao)) {
                        $correlacao = print_r($correlacao, true);
                    }
                    echo nl2br(htmlspecialchars($correlacao));
                    ?>
                </p>
            </div>

            <div class="insight-box alert">
                <strong>⚠️ Alerta de Segurança Terapêutica:</strong>
                <p style="margin-top: 5px; color: var(--text-light);">
                    <?php
                    $alerta = $insights['alerta'] ?? 'Nenhum alerta gerado.';
                    if (is_array($alerta)) {
                        $alerta = print_r($alerta, true);
                    }
                    echo nl2br(htmlspecialchars($alerta));
                    ?>
                </p>
            </div>

            <div class="insight-box success">
                <strong>💡 Recomendações de Práticas de Suporte:</strong>
                <p style="margin-top: 5px; color: var(--text-light);">
                    <?php
                    $recomendacoes = $insights['recomendacoes'] ?? 'Recomendações não disponíveis.';
                    if (is_array($recomendacoes)) {
                        $recomendacoes = print_r($recomendacoes, true);
                    }
                    echo nl2br(htmlspecialchars($recomendacoes));
                    ?>
                </p>
            </div>
        </div>

        <!-- 3. LAUDO E PARECER FINAL DO PROFISSIONAL -->
        <form action="index.php?controller=Medico&action=salvar_diagnostico_final" method="POST">
            <?= CSRF::getField() ?>
            
            <input type="hidden" name="id_agendamento" value="<?= htmlspecialchars($id_agendamento ?? '') ?>">

            <fieldset>
                <legend>Conclusão e Ajuste do Médico</legend>
                <div style="margin-bottom: 20px;">
                    <label for="diagnostico_final">Diagnóstico Clínico Definitivo (Após validação da IA)</label>
                    <textarea id="diagnostico_final" name="diagnostico_final" rows="3" required placeholder="Digite o diagnóstico conclusivo e o CID se aplicável..."></textarea>
                </div>

                <div>
                    <label for="ajuste_conduta">Ajustes Finais na Receita ou Conduta Médica</label>
                    <textarea id="ajuste_conduta" name="ajuste_conduta" rows="3" placeholder="Caso tenha alterado dosagens ou adicionado exames complementares após os insights da IA, registre aqui..."></textarea>
                </div>
            </fieldset>

            <div class="form-actions">
                <a href="javascript:history.back()" class="btn-back">← Voltar para Anamnese</a>
                <button type="submit" class="btn-submit">Finalizar e Salvar Prontuário</button>
            </div>

        </form>
    </div>

</body>
</html>