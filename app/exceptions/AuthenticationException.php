<?php

/**
 * Exceção de autenticação
 * Lançada quando há erros de autenticação
 */

class AuthenticationException extends AppException {
    public function __construct(string $message = "Falha na autenticação", array $context = []) {
        parent::__construct($message, $context, 401);
    }
}
