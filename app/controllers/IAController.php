<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../models/IA.php';
require_once __DIR__ . '/../models/Protocolo.php';

class IAController {
    private $iaModel;
    private $protocoloModel;

    public function __construct() {
        Session::start();
        if (!Session::has('usuario_id')) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }
        $this->iaModel = new IA();
        $this->protocoloModel = new Protocolo();
    }

    /**
     * Processa a anamnese e chama a API da IA para gerar insights
     */
    public function gerar_diagnostico() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=painel&action=index');
            exit;
        }

        // Coleta dados do formulário
        $id_agendamento = $_POST['id_agendamento'] ?? null;
        $protocolo_id = $_POST['protocolo_id'] ?? null;
        $queixa = $_POST['queixa_principal'] ?? '';
        $tempo = $_POST['tempo_sintomas'] ?? '';
        $frequencia = $_POST['frequencia_sintomas'] ?? '';
        $alergia = ($_POST['tem_alergia'] ?? 'nao') === 'sim' ? ($_POST['quais_alergias'] ?? 'Sim (não especificado)') : 'Nenhuma';
        $historico = $_POST['historico_medico'] ?? '';

        // Coleta os sinais vitais
        $pa = $_POST['pa'] ?? '';
        $fc = $_POST['fc'] ?? '';
        $temperatura = $_POST['temperatura'] ?? '';
        $glicemia = $_POST['glicemia'] ?? '';

        // Coleta respostas das perguntas do protocolo
        $respostas_protocolo = [];
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'pergunta_') === 0) {
                    $pergunta_id = str_replace('pergunta_', '', $key);
                    $respostas_protocolo[$pergunta_id] = $value;
                }
            }
        }

        // Busca perguntas do protocolo para mapeamento
        $perguntas_protocolo = [];
        $protocolo_info = '';
        if ($protocolo_id) {
            $protocolo = $this->protocoloModel->getById($protocolo_id);
            if ($protocolo) {
                $protocolo_info = "- Protocolo Selecionado: {$protocolo['nome']} ({$protocolo['categoria']})\n";
                $perguntas_protocolo = $this->protocoloModel->getPerguntas($protocolo_id);
                
                // Adiciona respostas do protocolo ao prompt
                if (!empty($respostas_protocolo)) {
                    $protocolo_info .= "\nRespostas do Protocolo:\n";
                    foreach ($perguntas_protocolo as $pergunta) {
                        $pergunta_id = $pergunta['id'];
                        if (isset($respostas_protocolo[$pergunta_id])) {
                            $protocolo_info .= "- {$pergunta['pergunta']}: {$respostas_protocolo[$pergunta_id]}\n";
                        }
                    }
                }
            }
        }

        // Monta o Prompt estruturado para a IA
        $prompt = "Você é um assistente clínico especialista de suporte à decisão médica. Analise o seguinte cenário clínico:\n\n";
        $prompt .= "- Queixa Principal: {$queixa}\n";
        $prompt .= "- Tempo dos Sintomas: {$tempo}\n";
        $prompt .= "- Padrão dos Sintomas: {$frequencia}\n";
        $prompt .= "- Alergias relatadas: {$alergia}\n";
        $prompt .= "- Histórico do Paciente: {$historico}\n";
        $prompt .= "- Sinais Vitais: PA={$pa}, FC={$fc}, Temp={$temperatura}, Glicemia={$glicemia}\n";
        if ($protocolo_info) {
            $prompt .= "\n" . $protocolo_info;
        }
        $prompt .= "\nForneça um JSON válido contendo exatamente três chaves com valores STRING (não objetos):\n";
        $prompt .= "1. 'correlacao': Texto simples com análise do diagnóstico e diagnósticos diferenciais.\n";
        $prompt .= "2. 'alerta': Texto simples com riscos e contraindicações baseadas no histórico.\n";
        $prompt .= "3. 'recomendacoes': Texto simples com condutas complementares e orientações de suporte.\n";
        $prompt .= "CRÍTICO: Cada valor deve ser uma STRING simples, não um objeto ou array. Não use formatações markdown, retorne apenas o objeto JSON puro.";

        try {
            // Dispara a requisição para a API usando Config para obter a chave
            $apiConfig = Config::get('IA_PROVIDER', 'groq');
            $apiKey = Config::get('GROQ_API_KEY');
            
            if ($apiConfig === 'openai') {
                $apiKey = Config::get('OPENAI_API_KEY');
            }
            
            $responseIa = $this->iaModel->chamarApi($apiKey, $prompt);

            // Decodifica o retorno da IA
            $insights = json_decode($responseIa, true);

            // Caso a IA falhe ou não devolva o JSON correto, garante valores padrão
            if (!$insights || json_last_error() !== JSON_ERROR_NONE || !isset($insights['correlacao'])) {
                Logger::warning("IA response invalid or failed", [
                    'id_agendamento' => $id_agendamento,
                    'json_error' => json_last_error_msg()
                ]);
                
                $insights = [
                    'correlacao' => 'Não foi possível gerar a correlação automática neste momento. Reveja os dados informados.',
                    'alerta' => 'Falha ao analisar alertas farmacológicos de segurança. Verifique manualmente.',
                    'recomendacoes' => 'Prossiga com a avaliação clínica convencional e documente no prontuário.'
                ];
            }
            
            Logger::audit(Constants::AUDITORIA_ACAO_USO_IA, [
                'id_agendamento' => $id_agendamento,
                'protocolo_id' => $protocolo_id
            ]);
        } catch (Exception $e) {
            Logger::error("Error calling IA API", [
                'error' => $e->getMessage(),
                'id_agendamento' => $id_agendamento
            ]);
            
            $insights = [
                'correlacao' => 'Erro ao conectar com o serviço de IA. Verifique a configuração.',
                'alerta' => 'Serviço indisponível no momento.',
                'recomendacoes' => 'Prossiga com a avaliação clínica convencional e documente no prontuário.'
            ];
        }

        // Retorna os dados processados
        return [
            'id_agendamento' => $id_agendamento,
            'queixa' => $queixa,
            'tempo' => $tempo,
            'frequencia' => $frequencia,
            'alergia' => $alergia,
            'historico' => $historico,
            'pa' => $pa,
            'fc' => $fc,
            'temperatura' => $temperatura,
            'glicemia' => $glicemia,
            'protocolo_id' => $protocolo_id,
            'respostas_protocolo' => $respostas_protocolo,
            'perguntas_protocolo' => $perguntas_protocolo,
            'insights' => $insights
        ];
    }
}
