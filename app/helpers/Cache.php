<?php

/**
 * Helper de Cache
 * Implementa sistema de cache simples baseado em arquivos
 */

class Cache {
    private static ?Cache $instance = null;
    private string $cachePath;
    private string $prefix;
    private int $defaultLifetime;
    private bool $enabled;
    
    private function __construct() {
        $config = Config::get('cache');
        $this->enabled = $config['enabled'] ?? false;
        $this->cachePath = __DIR__ . '/../../cache';
        $this->prefix = $config['prefix'] ?? 'conexau_';
        $this->defaultLifetime = $config['default_lifetime'] ?? Constants::CACHE_LIFETIME_MEDIUM;
        
        // Cria o diretório de cache se não existir
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }
    
    public static function getInstance(): Cache {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Armazena um valor no cache
     */
    public static function set(string $key, $value, int $lifetime = null): bool {
        if (!self::getInstance()->enabled) {
            return false;
        }
        
        $instance = self::getInstance();
        $lifetime = $lifetime ?? $instance->defaultLifetime;
        $filename = $instance->getFilename($key);
        
        $data = [
            'value' => $value,
            'expires' => time() + $lifetime
        ];
        
        return file_put_contents($filename, serialize($data)) !== false;
    }
    
    /**
     * Obtém um valor do cache
     */
    public static function get(string $key, $default = null) {
        if (!self::getInstance()->enabled) {
            return $default;
        }
        
        $instance = self::getInstance();
        $filename = $instance->getFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // Verifica se expirou
        if ($data['expires'] < time()) {
            unlink($filename);
            return $default;
        }
        
        return $data['value'];
    }
    
    /**
     * Verifica se uma chave existe no cache
     */
    public static function has(string $key): bool {
        if (!self::getInstance()->enabled) {
            return false;
        }
        
        $instance = self::getInstance();
        $filename = $instance->getFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // Verifica se expirou
        if ($data['expires'] < time()) {
            unlink($filename);
            return false;
        }
        
        return true;
    }
    
    /**
     * Remove um valor do cache
     */
    public static function forget(string $key): bool {
        $instance = self::getInstance();
        $filename = $instance->getFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    /**
     * Limpa todo o cache
     */
    public static function clear(): bool {
        $instance = self::getInstance();
        $files = glob($instance->cachePath . '/' . $instance->prefix . '*');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    /**
     * Obtém o nome do arquivo para uma chave
     */
    private function getFilename(string $key): string {
        $safeKey = md5($key);
        return $this->cachePath . '/' . $this->prefix . $safeKey . '.cache';
    }
    
    /**
     * Remove itens expirados do cache
     */
    public static function cleanup(): int {
        $instance = self::getInstance();
        $files = glob($instance->cachePath . '/' . $instance->prefix . '*');
        $removed = 0;
        
        foreach ($files as $file) {
            $data = unserialize(file_get_contents($file));
            
            if ($data['expires'] < time()) {
                unlink($file);
                $removed++;
            }
        }
        
        return $removed;
    }
    
    /**
     * Lembra um valor por um tempo específico (cache simples)
     */
    public static function remember(string $key, int $lifetime, callable $callback) {
        $value = self::get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        self::set($key, $value, $lifetime);
        
        return $value;
    }
}
