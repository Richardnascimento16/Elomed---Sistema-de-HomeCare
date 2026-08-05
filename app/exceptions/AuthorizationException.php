<?php

/**
 * Exceção de autorização
 * Lançada quando o usuário não tem permissão para realizar uma ação
 */

class AuthorizationException extends AppException {
    public function __construct(string $message = "Permissão negada", array $context = []) {
        parent::__construct($message, $context, 403);
    }
}
