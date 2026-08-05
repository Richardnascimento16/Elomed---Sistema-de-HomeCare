<?php

/**
 * Middleware de segurança de sessão
 * Gerencia configurações de segurança da sessão
 */

class Session {
    private static bool $started = false;
    
    /**
     * Inicia a sessão com configurações de segurança
     */
    public static function start(): void {
        if (self::$started) {
            return;
        }
        
        $config = Config::get('session');
        
        // Configurações de segurança da sessão
        ini_set('session.cookie_httponly', $config['httponly'] ?? true);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', $config['secure'] ?? false);
        ini_set('session.cookie_samesite', $config['samesite'] ?? 'Strict');
        ini_set('session.gc_maxlifetime', $config['lifetime'] ?? 3600);
        
        // Nome da sessão customizado
        if (!empty($config['name'])) {
            session_name($config['name']);
        }
        
        session_start();
        
        // Regenera o ID da sessão periodicamente para prevenir fixation
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) { // 30 minutos
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
        
        self::$started = true;
    }
    
    /**
     * Destrói a sessão
     */
    public static function destroy(): void {
        if (!self::$started) {
            return;
        }
        
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
        self::$started = false;
    }
    
    /**
     * Define um valor na sessão
     */
    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtém um valor da sessão
     */
    public static function get(string $key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Verifica se uma chave existe na sessão
     */
    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove um valor da sessão
     */
    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Define uma mensagem flash (exibe uma vez e é removida)
     */
    public static function flash(string $key, $value): void {
        self::set('_flash_' . $key, $value);
    }
    
    /**
     * Obtém e remove uma mensagem flash
     */
    public static function getFlash(string $key, $default = null) {
        $value = self::get('_flash_' . $key, $default);
        self::remove('_flash_' . $key);
        return $value;
    }
    
    /**
     * Verifica se há uma mensagem flash
     */
    public static function hasFlash(string $key): bool {
        return self::has('_flash_' . $key);
    }
    
    /**
     * Obtém o ID da sessão
     */
    public static function getId(): string {
        self::start();
        return session_id();
    }
    
    /**
     * Verifica se a sessão está ativa
     */
    public static function isActive(): bool {
        return self::$started && session_status() === PHP_SESSION_ACTIVE;
    }
}
