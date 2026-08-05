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
    <title>Registro de Consulta Médica</title>
    <style>
        :root {
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --secondary: #0f172a;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border: #e2e8f0;
            --text: #334155;
            --text-light: #64748b;
            --success: #10b981;
            --danger: #ef4444;
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
            margin-bottom: 35px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 20px;
        }

        header h1 {
            color: var(--secondary);
            font-size: 24px;
            margin-bottom: 5px;
        }

        header p {
            color: var(--text-light);
            font-size: 14px;
        }

        fieldset {
            border: none;
            margin-bottom: 35px;
        }

        legend {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            width: 100%;
            border-bottom: 1px solid var(--border);
            padding-bottom: 5px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }

        .col-12 { grid-column: span 12; }
        .col-6  { grid-column: span 6; }
        .col-4  { grid-column: span 4; }
        .col-3  { grid-column: span 3; }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--secondary);
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            color: var(--text);
            background-color: #fff;
            outline: none;
            transition: all 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .radio-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 5px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 400;
        }

        /* Container Dinâmico de Medicamentos */
        .medication-block {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px dashed var(--primary);
            margin-bottom: 15px;
            position: relative;
        }

        .btn-add {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-add:hover {
            background-color: var(--primary);
            color: white;
        }

        .btn-remove {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            float: right;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        /* Botão Salvar */
        .form-actions {
            margin-top: 30px;
            text-align: right;
            border-top: 1px solid var(--border);
            padding-top: 20px;
        }

        .btn-submit {
            background-color: var(--success);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #059669;
        }

        @media (max-width: 768px) {
            .col-6, .col-4, .col-3 { grid-column: span 12; }
            .container { padding: 20px; }
        }
    </style>
    <script>
    function carregarPerguntasProtocolo() {
        const protocoloId = document.getElementById('protocolo_id').value;
        const perguntasFieldset = document.getElementById('perguntas_protocolo_fieldset');
        const anamneseFieldset = document.getElementById('anamnese_fieldset');
        const perguntasContainer = document.getElementById('perguntas_container');
        
        console.log('Protocolo ID selecionado:', protocoloId);
        
        if (!protocoloId) {
            perguntasFieldset.style.display = 'none';
            anamneseFieldset.style.display = 'block';
            perguntasContainer.innerHTML = '';
            return;
        }
        
        // Buscar perguntas do protocolo via AJAX
        fetch(`index.php?controller=Medico&action=get_perguntas_protocolo&protocolo_id=${protocoloId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Dados recebidos:', data);
                if (data.success && data.perguntas.length > 0) {
                    perguntasFieldset.style.display = 'block';
                    anamneseFieldset.style.display = 'none';
                    renderizarPerguntas(data.perguntas);
                } else {
                    perguntasFieldset.style.display = 'none';
                    anamneseFieldset.style.display = 'block';
                    perguntasContainer.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar perguntas:', error);
                perguntasFieldset.style.display = 'none';
                anamneseFieldset.style.display = 'block';
            });
    }
    
    function renderizarPerguntas(perguntas) {
        const container = document.getElementById('perguntas_container');
        container.innerHTML = '';
        
        perguntas.forEach(pergunta => {
            const colSpan = pergunta.tipo === 'textarea' ? 'col-12' : 'col-6';
            const div = document.createElement('div');
            div.className = `form-group ${colSpan}`;
            
            const label = document.createElement('label');
            label.textContent = pergunta.pergunta;
            if (pergunta.obrigatorio) {
                label.textContent += ' *';
            }
            div.appendChild(label);
            
            let input;
            
            switch(pergunta.tipo) {
                case 'texto':
                    input = document.createElement('input');
                    input.type = 'text';
                    input.name = `pergunta_${pergunta.id}`;
                    input.placeholder = pergunta.placeholder || '';
                    if (pergunta.obrigatorio) input.required = true;
                    break;
                    
                case 'numero':
                    input = document.createElement('input');
                    input.type = 'number';
                    input.name = `pergunta_${pergunta.id}`;
                    input.placeholder = pergunta.placeholder || '';
                    if (pergunta.obrigatorio) input.required = true;
                    break;
                    
                case 'textarea':
                    input = document.createElement('textarea');
                    input.name = `pergunta_${pergunta.id}`;
                    input.rows = 3;
                    input.placeholder = pergunta.placeholder || '';
                    if (pergunta.obrigatorio) input.required = true;
                    break;
                    
                case 'select':
                    input = document.createElement('select');
                    input.name = `pergunta_${pergunta.id}`;
                    if (pergunta.obrigatorio) input.required = true;
                    
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Selecione --';
                    input.appendChild(defaultOption);
                    
                    if (pergunta.opcoes && Array.isArray(pergunta.opcoes)) {
                        pergunta.opcoes.forEach(opcao => {
                            const option = document.createElement('option');
                            option.value = opcao;
                            option.textContent = opcao;
                            input.appendChild(option);
                        });
                    }
                    break;
                    
                case 'radio':
                    const radioGroup = document.createElement('div');
                    radioGroup.className = 'radio-group';
                    
                    if (pergunta.opcoes && Array.isArray(pergunta.opcoes)) {
                        pergunta.opcoes.forEach((opcao, index) => {
                            const radioLabel = document.createElement('label');
                            radioLabel.className = 'radio-option';
                            
                            const radio = document.createElement('input');
                            radio.type = 'radio';
                            radio.name = `pergunta_${pergunta.id}`;
                            radio.value = opcao;
                            if (index === 0 && pergunta.obrigatorio) radio.required = true;
                            
                            radioLabel.appendChild(radio);
                            radioLabel.appendChild(document.createTextNode(' ' + opcao));
                            radioGroup.appendChild(radioLabel);
                        });
                    }
                    div.appendChild(radioGroup);
                    container.appendChild(div);
                    return; // Não adiciona input neste caso
                    
                case 'checkbox':
                    const checkboxGroup = document.createElement('div');
                    checkboxGroup.className = 'radio-group';
                    
                    if (pergunta.opcoes && Array.isArray(pergunta.opcoes)) {
                        pergunta.opcoes.forEach(opcao => {
                            const checkboxLabel = document.createElement('label');
                            checkboxLabel.className = 'radio-option';
                            
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = `pergunta_${pergunta.id}[]`;
                            checkbox.value = opcao;
                            
                            checkboxLabel.appendChild(checkbox);
                            checkboxLabel.appendChild(document.createTextNode(' ' + opcao));
                            checkboxGroup.appendChild(checkboxLabel);
                        });
                    }
                    div.appendChild(checkboxGroup);
                    container.appendChild(div);
                    return; // Não adiciona input neste caso
            }
            
            if (input) {
                div.appendChild(input);
            }
            container.appendChild(div);
        });
    }
    </script>
</head>
<body>

    <div class="container">
        <header>
            <h1>Atendimento Clínico e Anamnese</h1>
            <p>Formulário de triagem diagnóstica e prescrição de medicamentos</p>
        </header>

        <!-- O formulário enviará os dados da consulta vinculados ao ID do agendamento atual -->
        <form action="index.php?controller=Medico&action=dados_atendimento" method="POST">
            <?= CSRF::getField() ?>
            
            <!-- Campo oculto para mapear de qual agendamento é essa consulta -->
            <input type="hidden" name="id_agendamento" value="<?= $id_agendamento ?? '' ?>">

            <!-- 0. SELEÇÃO DE PROTOCOLO/PROPÓSITO -->
            <fieldset>
                <legend>0. Propósito do Atendimento</legend>
                <div class="form-group col-12">
                    <label for="protocolo_id">Selecione o propósito ou condição do atendimento:</label>
                    <select id="protocolo_id" name="protocolo_id" onchange="carregarPerguntasProtocolo()">
                        <option value="">-- Selecione o propósito --</option>
                        <?php foreach ($protocolos as $proto): ?>
                            <option value="<?= $proto['id'] ?>" data-categoria="<?= $proto['categoria'] ?>">
                                <?= htmlspecialchars($proto['nome']) ?> 
                                <?php if ($proto['categoria'] === 'investigacao'): ?>
                                    (Não sei o diagnóstico)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: var(--text-light); margin-top: 5px; display: block;">
                        Selecione "Investigação Diagnóstica" se não souber a condição do paciente.
                    </small>
                </div>
            </fieldset>

            <!-- 1. SINAIS VITAIS (Apoiando o diagnóstico) -->
            <fieldset>
                <legend>1. Triagem e Sinais Vitais</legend>
                <div class="form-grid">
                    <div class="form-group col-3">
                        <label for="pa">Pressão Arterial (PA)</label>
                        <input type="text" id="pa" name="pa" placeholder="Ex: 120/80 mmHg">
                    </div>
                    <div class="form-group col-3">
                        <label for="fc">Frequência Cardíaca</label>
                        <input type="number" id="fc" name="fc" placeholder="bpm">
                    </div>
                    <div class="form-group col-3">
                        <label for="temperatura">Temperatura</label>
                        <input type="text" id="temperatura" name="temperatura" placeholder="°C">
                    </div>
                    <div class="form-group col-3">
                        <label for="glicemia">Glicemia Capilar</label>
                        <input type="number" id="glicemia" name="glicemia" placeholder="mg/dL">
                    </div>
                </div>
            </fieldset>

            <!-- 2. PERGUNTAS DO PROTOCOLO (DINÂMICAS) -->
            <fieldset id="perguntas_protocolo_fieldset" style="display: none;">
                <legend>2. Perguntas Específicas do Protocolo</legend>
                <div id="perguntas_container" class="form-grid">
                    <!-- Perguntas serão carregadas dinamicamente aqui -->
                </div>
            </fieldset>

            <!-- 3. PERGUNTAS DIAGNÓSTICAS (ANAMNESE) - Mantidas como fallback -->
            <fieldset id="anamnese_fieldset">
                <legend>3. Anamnese (Perguntas para Investigação)</legend>
                <div class="form-grid">
                    
                    <div class="form-group col-12">
                        <label for="queixa_principal">Qual é a queixa principal que motivou a consulta?</label>
                        <textarea id="queixa_principal" name="queixa_principal" rows="2" placeholder="Descreva os sintomas relatados pelo paciente..."></textarea>
                    </div>

                    <div class="form-group col-6">
                        <label for="tempo_sintomas">Há quanto tempo os sintomas começaram? (Evolução)</label>
                        <input type="text" id="tempo_sintomas" name="tempo_sintomas" placeholder="Ex: 3 dias, 2 semanas...">
                    </div>

                    <div class="form-group col-6">
                        <label>Os sintomas são contínuos ou surgem em episódios?</label>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="frequencia_sintomas" value="continuo"> Contínuos</label>
                            <label class="radio-option"><input type="radio" name="frequencia_sintomas" value="intermitente"> Intermitentes (vêm e vão)</label>
                            <label class="radio-option"><input type="radio" name="frequencia_sintomas" value="pioram_noite"> Pioram em períodos específicos</label>
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <label for="fatores_alivio_piora">Existe algo que faça o sintoma melhorar ou piorar? (Alimentação, repouso, posição, etc.)</label>
                        <input type="text" id="fatores_alivio_piora" name="fatores_alivio_piora" placeholder="Ex: Piora ao tossir; melhora ao deitar...">
                    </div>

                    <div class="form-group col-6">
                        <label>O paciente possui alergia a algum medicamento?</label>
                        <div class="radio-group">
                            <label class="radio-option"><input type="radio" name="tem_alergia" value="nao" checked> Não</label>
                            <label class="radio-option"><input type="radio" name="tem_alergia" value="sim"> Sim</label>
                        </div>
                        <input type="text" id="quais_alergias" name="quais_alergias" placeholder="Se sim, quais?" style="margin-top: 10px;">
                    </div>

                    <div class="form-group col-6">
                        <label for="historico_medico">Antecedentes médicos relevantes (Ex: Hipertensão, Diabetes, Cirurgias recentes?)</label>
                        <input type="text" id="historico_medico" name="historico_medico" placeholder="Descreva brevemente...">
                    </div>
                </div>
            </fieldset>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">Enviar Dados da Consulta</button>
            </div>

        </form>
    </div>

</body>
</html>
