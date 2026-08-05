<?php
/**
 * Configuração da Chave da API Groq
 * 
 * Como obter sua chave gratuita:
 * 1. Acesse: https://console.groq.com/keys
 * 2. Crie uma conta gratuita (sem cartão de crédito)
 * 3. Clique em "Create Key" para gerar sua API key
 * 4. Copie a chave e cole abaixo entre as aspas
 * 
 * A Groq oferece:
 * - 100% gratuito, sem cartão de crédito
 * - 30.000 tokens/minuto e 14.400 requests/dia
 * - Modelos: Llama 3.1 8B, Llama 4 Scout, Qwen3 32B, DeepSeek R1
 * - Ultra-rápido com hardware LPU
 */
return [
    'groq_api_key' => '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', // Cole sua chave da Groq aqui entre as aspas
    'model' => 'llama-3.1-8b-instant' // Modelo padrão (pode ser alterado)
];
