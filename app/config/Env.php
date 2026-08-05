<?php

/**
 * Carrega variáveis de ambiente do arquivo .env
 */

class Env {
    private static bool $loaded = false;
    
    /**
     * Carrega o arquivo .env e define as variáveis de ambiente
     */
    public static function load(string $path = null): void {
        if (self::$loaded) {
            return;
        }
        
        $path = $path ?? __DIR__ . '/../../.env';
        
        if (!file_exists($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse linha
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Remove aspas se existirem
                if (strpos($value, '"') === 0 || strpos($value, "'") === 0) {
                    $value = substr($value, 1, -1);
                }
                
                // Define como variável de ambiente
                if (!getenv($name)) {
                    putenv("$name=$value");
                    $_ENV[$name] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Obtém uma variável de ambiente
     */
    public static function get(string $key, $default = null) {
        self::load();
        return getenv($key) ?: $default;
    }
}
