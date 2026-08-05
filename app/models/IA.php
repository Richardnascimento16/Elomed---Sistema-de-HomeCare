<?php
require_once __DIR__ . '/Model.php';

class IA extends Model {
    
    /**
     * Retorna a chave da API (Groq ou OpenAI)
     * Prioriza Groq (gratuito), depois OpenAI, ou usa variável de ambiente
     */
    public function getApiKey() {
        // Tenta carregar chave do Groq (prioridade - gratuito)
        $groqConfigFile = __DIR__ . '/../../config/groq_api_key.php';
        if (file_exists($groqConfigFile)) {
            $config = require $groqConfigFile;
            if (!empty($config['groq_api_key'])) {
                return ['key' => $config['groq_api_key'], 'provider' => 'groq', 'model' => $config['model'] ?? 'llama-3.1-8b-instant'];
            }
        }
        
        // Tenta usar variável de ambiente do Groq
        $envGroqKey = getenv('GROQ_API_KEY');
        if (!empty($envGroqKey)) {
            return ['key' => $envGroqKey, 'provider' => 'groq', 'model' => getenv('GROQ_MODEL') ?: 'llama-3.1-8b-instant'];
        }

        // Fallback para OpenAI (se configurado)
        $openaiConfigFile = __DIR__ . '/../../config/api_key.php';
        if (file_exists($openaiConfigFile)) {
            $config = require $openaiConfigFile;
            if (!empty($config['openai_api_key'])) {
                return ['key' => $config['openai_api_key'], 'provider' => 'openai', 'model' => 'gpt-4o-mini'];
            }
        }
        
        // Tenta usar variável de ambiente do OpenAI
        $envOpenaiKey = getenv('OPENAI_API_KEY');
        if (!empty($envOpenaiKey)) {
            return ['key' => $envOpenaiKey, 'provider' => 'openai', 'model' => 'gpt-4o-mini'];
        }
        
        // Se não encontrar, retorna placeholder e o sistema usará fallback
        return ['key' => '', 'provider' => 'none', 'model' => ''];
    }

    /**
     * Chama a API de IA (Groq ou OpenAI)
     */
    public function chamarApi($apiConfig, $prompt) {
        // Se não tem chave de API configurada, retorna simulação para demonstração
        if (empty($apiConfig['key'])) {
            return $this->simularResposta($prompt);
        }

        $provider = $apiConfig['provider'];
        $apiKey = $apiConfig['key'];
        $model = $apiConfig['model'];

        // Configura URL e headers baseados no provider
        if ($provider === 'groq') {
            $url = 'https://api.groq.com/openai/v1/chat/completions';
        } else {
            $url = 'https://api.openai.com/v1/chat/completions';
        }

        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result && $httpCode === 200) {
            $jsonRes = json_decode($result, true);
            return $jsonRes['choices'][0]['message']['content'] ?? '';
        }

        // Se a API falhar, retorna simulação para não travar o fluxo
        return $this->simularResposta($prompt);
    }

    /**
     * Função de fallback que simula resposta da IA quando não há API key configurada
     */
    private function simularResposta($prompt) {
        // Extrai informações básicas do prompt para gerar resposta contextual
        $queixa = '';
        if (preg_match('/Queixa Principal:\s*(.+)/', $prompt, $matches)) {
            $queixa = $matches[1];
        }

        $alergia = 'Nenhuma';
        if (preg_match('/Alergias relatadas:\s*(.+)/', $prompt, $matches)) {
            $alergia = $matches[1];
        }

        $pa = '';
        if (preg_match('/PA=([^,]+)/', $prompt, $matches)) {
            $pa = $matches[1];
        }

        $fc = '';
        if (preg_match('/FC=([^,]+)/', $prompt, $matches)) {
            $fc = $matches[1];
        }

        // Gera resposta simulada baseada nos dados extraídos
        $resposta = [
            'correlacao' => "Análise clínica da queixa '{$queixa}': Considerando os sinais vitais (PA: {$pa}, FC: {$fc}) e histórico de alergias ({$alergia}), recomenda-se investigação detalhada. Possíveis diagnósticos diferenciais devem ser considerados conforme evolução do quadro.",
            'alerta' => "Atenção: Verificar contraindicações medicamentosas considerando histórico de alergias ({$alergia}). Monitorar sinais vitais de perto. Se houver piora, considerar encaminhamento para urgência.",
            'recomendacoes' => "1. Monitoramento contínuo dos sinais vitais\n2. Documentar evolução dos sintomas\n3. Considerar exames complementares conforme necessidade\n4. Reavaliação em 24-48 horas ou antes se houver piora\n5. Orientar paciente sobre sinais de alerta"
        ];

        return json_encode($resposta);
    }
}
