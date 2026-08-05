<?php

/**
 * Middleware de proteção CSRF
 * Gera e valida tokens CSRF para prevenir ataques
 */

class CSRF {
    private static string $tokenName = 'csrf_token';
    private static ?string $token = null;
    
    /**
     * Gera um novo token CSRF
     */
    public static function generateToken(): string {
        if (self::$token === null) {
            self::$token = bin2hex(random_bytes(32));
            $_SESSION[self::$tokenName] = self::$token;
            $_SESSION[self::$tokenName . '_time'] = time();
        }
        return self::$token;
    }
    
    /**
     * Obtém o token CSRF atual
     */
    public static function getToken(): string {
        if (self::$token === null) {
            self::$token = $_SESSION[self::$tokenName] ?? self::generateToken();
        }
        return self::$token;
    }
    
    /**
     * Valida um token CSRF
     */
    public static function validateToken(string $token): bool {
        if (!isset($_SESSION[self::$tokenName])) {
            return false;
        }
        
        $sessionToken = $_SESSION[self::$tokenName];
        $tokenTime = $_SESSION[self::$tokenName . '_time'] ?? 0;
        $lifetime = Config::get('security.csrf_token_lifetime', 3600);
        
        // Verifica se o token expirou
        if (time() - $tokenTime > $lifetime) {
            self::regenerateToken();
            return false;
        }
        
        // Verifica se o token corresponde
        if (hash_equals($sessionToken, $token)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Regenera o token CSRF
     */
    public static function regenerateToken(): void {
        self::$token = null;
        unset($_SESSION[self::$tokenName]);
        unset($_SESSION[self::$tokenName . '_time']);
    }
    
    /**
     * Obtém o campo HTML do token CSRF
     */
    public static function getField(): string {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Valida o token da requisição POST
     */
    public static function validateRequest(): bool {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }
        
        $token = $_POST[self::$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (empty($token)) {
            return false;
        }
        
        return self::validateToken($token);
    }
}
