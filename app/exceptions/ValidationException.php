<?php

/**
 * Exceção de validação
 * Lançada quando há erros de validação de dados
 */

class ValidationException extends AppException {
    private array $errors = [];
    
    public function __construct(string $message, array $errors = [], array $context = []) {
        $this->errors = $errors;
        parent::__construct($message, $context, 422);
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
}
