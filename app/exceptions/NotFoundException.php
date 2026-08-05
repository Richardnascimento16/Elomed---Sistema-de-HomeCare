<?php

/**
 * Exceção de recurso não encontrado
 * Lançada quando um recurso não é encontrado
 */

class NotFoundException extends AppException {
    public function __construct(string $message = "Recurso não encontrado", array $context = []) {
        parent::__construct($message, $context, 404);
    }
}
