<?php

/**
 * Exceção base da aplicação
 * Todas as exceções customizadas devem estender esta classe
 */

class AppException extends Exception {
    protected array $context = [];
    
    public function __construct(string $message = "", array $context = [], int $code = 0, ?Throwable $previous = null) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }
    
    public function getContext(): array {
        return $this->context;
    }
}
