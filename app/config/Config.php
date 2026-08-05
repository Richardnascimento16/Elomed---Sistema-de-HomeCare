<?php

require_once __DIR__ . '/Constants.php';

/**
 * Configuração Centralizada do Sistema
 * Gerencia todas as configurações do aplicativo
 */

class Config {
    private static array $config = [];
    private static bool $loaded = false;
    
    /**
     * Carrega as configurações do arquivo ou variáveis de ambiente
     */
    private static function load(): void {
        if (self::$loaded) {
            return;
        }
        
        // Carrega variáveis de ambiente
        require_once __DIR__ . '/Env.php';
        Env::load();
        
        // Configurações de banco de dados
        self::$config['db'] = [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'dbname' => getenv('DB_NAME') ?: 'conexau',
            'username' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASS') ?: '',
            'charset' => 'utf8mb4'
        ];
        
        // Configurações de sessão
        self::$config['session'] = [
            'name' => getenv('SESSION_NAME') ?: Constants::SESSION_NAME,
            'lifetime' => (int)(getenv('SESSION_LIFETIME') ?: Constants::SESSION_LIFETIME),
            'path' => '/',
            'domain' => getenv('SESSION_DOMAIN') ?: '',
            'secure' => getenv('SESSION_SECURE') === 'true',
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        
        // Configurações de segurança
        self::$config['security'] = [
            'csrf_token_lifetime' => 3600,
            'password_min_length' => 8,
            'password_require_special' => true,
            'password_require_number' => true,
            'password_require_uppercase' => true,
            'max_login_attempts' => 5,
            'login_lockout_time' => 900 // 15 minutos
        ];
        
        // Configurações de API de IA
        self::$config['ai'] = [
            'groq_api_key' => getenv('GROQ_API_KEY') ?: '',
            'groq_model' => getenv('GROQ_MODEL') ?: 'llama-3.1-8b-instant',
            'openai_api_key' => getenv('OPENAI_API_KEY') ?: '',
            'openai_model' => 'gpt-4o-mini',
            'timeout' => 30
        ];
        
        // Configurações de cache
        self::$config['cache'] = [
            'enabled' => getenv('CACHE_ENABLED') === 'true',
            'driver' => getenv('CACHE_DRIVER') ?: 'file', // file, redis, memcached
            'prefix' => 'conexau_',
            'default_lifetime' => Constants::CACHE_LIFETIME_MEDIUM
        ];
        
        // Configurações de logging
        self::$config['logging'] = [
            'enabled' => getenv('LOGGING_ENABLED') !== 'false',
            'level' => getenv('LOG_LEVEL') ?: Constants::LOG_LEVEL_INFO,
            'path' => __DIR__ . '/../../logs',
            'filename' => 'app.log',
            'max_files' => 30
        ];
        
        // Configurações de aplicação
        self::$config['app'] = [
            'name' => 'Conexau',
            'version' => '1.0.0',
            'environment' => getenv('APP_ENV') ?: 'development',
            'debug' => getenv('APP_DEBUG') !== 'false',
            'timezone' => 'America/Sao_Paulo',
            'locale' => 'pt_BR'
        ];
        
        // Configurações de email
        self::$config['mail'] = [
            'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
            'port' => (int)(getenv('MAIL_PORT') ?: 587),
            'username' => getenv('MAIL_USERNAME') ?: '',
            'password' => getenv('MAIL_PASSWORD') ?: '',
            'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
            'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@conexau.com',
            'from_name' => getenv('MAIL_FROM_NAME') ?: 'Conexau'
        ];
        
        // Configurações de upload
        self::$config['upload'] = [
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
            'path' => __DIR__ . '/../../public/uploads'
        ];
        
        self::$loaded = true;
    }
    
    /**
     * Obtém uma configuração
     */
    public static function get(string $key, $default = null) {
        self::load();
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Define uma configuração em tempo de execução
     */
    public static function set(string $key, $value): void {
        self::load();
        
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $config[$k] = $value;
            } else {
                if (!isset($config[$k])) {
                    $config[$k] = [];
                }
                $config = &$config[$k];
            }
        }
    }
    
    /**
     * Verifica se está em ambiente de desenvolvimento
     */
    public static function isDevelopment(): bool {
        return self::get('app.environment') === 'development';
    }
    
    /**
     * Verifica se está em ambiente de produção
     */
    public static function isProduction(): bool {
        return self::get('app.environment') === 'production';
    }
    
    /**
     * Verifica se o debug está ativado
     */
    public static function isDebug(): bool {
        return self::get('app.debug') === true;
    }
}
