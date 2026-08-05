<?php

/**
 * Helper de validação de input
 * Valida e sanitiza dados de entrada
 */

class Validator {
    private array $errors = [];
    private array $data = [];
    
    public function __construct(array $data = []) {
        $this->data = $data;
    }
    
    /**
     * Valida um campo como obrigatório
     */
    public function required(string $field, string $message = null): self {
        if (!isset($this->data[$field]) || empty($this->data[$field])) {
            $this->errors[$field] = $message ?? "O campo {$field} é obrigatório";
        }
        return $this;
    }
    
    /**
     * Valida um campo como email
     */
    public function email(string $field, string $message = null): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "O campo {$field} deve ser um email válido";
            }
        }
        return $this;
    }
    
    /**
     * Valida o tamanho mínimo de um campo
     */
    public function min(string $field, int $min, string $message = null): self {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ter no mínimo {$min} caracteres";
        }
        return $this;
    }
    
    /**
     * Valida o tamanho máximo de um campo
     */
    public function max(string $field, int $max, string $message = null): self {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ter no máximo {$max} caracteres";
        }
        return $this;
    }
    
    /**
     * Valida se um campo é numérico
     */
    public function numeric(string $field, string $message = null): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = $message ?? "O campo {$field} deve ser numérico";
            }
        }
        return $this;
    }
    
    /**
     * Valida se um campo é um inteiro
     */
    public function integer(string $field, string $message = null): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
                $this->errors[$field] = $message ?? "O campo {$field} deve ser um inteiro";
            }
        }
        return $this;
    }
    
    /**
     * Valida se um campo está em uma lista de valores permitidos
     */
    public function in(string $field, array $allowed, string $message = null): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!in_array($this->data[$field], $allowed)) {
                $this->errors[$field] = $message ?? "O campo {$field} deve ser um dos valores permitidos";
            }
        }
        return $this;
    }
    
    /**
     * Valida se um campo corresponde a outro campo
     */
    public function matches(string $field, string $otherField, string $message = null): self {
        if (isset($this->data[$field]) && isset($this->data[$otherField])) {
            if ($this->data[$field] !== $this->data[$otherField]) {
                $this->errors[$field] = $message ?? "O campo {$field} deve corresponder ao campo {$otherField}";
            }
        }
        return $this;
    }
    
    /**
     * Valida uma senha
     */
    public function password(string $field, string $message = null): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $password = $this->data[$field];
            $config = Config::get('security');
            
            if (strlen($password) < ($config['password_min_length'] ?? 8)) {
                $this->errors[$field] = $message ?? "A senha deve ter no mínimo {$config['password_min_length']} caracteres";
                return $this;
            }
            
            if (($config['password_require_uppercase'] ?? true) && !preg_match('/[A-Z]/', $password)) {
                $this->errors[$field] = $message ?? "A senha deve conter pelo menos uma letra maiúscula";
                return $this;
            }
            
            if (($config['password_require_number'] ?? true) && !preg_match('/[0-9]/', $password)) {
                $this->errors[$field] = $message ?? "A senha deve conter pelo menos um número";
                return $this;
            }
            
            if (($config['password_require_special'] ?? true) && !preg_match('/[^a-zA-Z0-9]/', $password)) {
                $this->errors[$field] = $message ?? "A senha deve conter pelo menos um caractere especial";
                return $this;
            }
        }
        return $this;
    }
    
    /**
     * Valida uma data
     */
    public function date(string $field, string $format = 'Y-m-d', string $message = null): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$date || $date->format($format) !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "O campo {$field} deve ser uma data válida no formato {$format}";
            }
        }
        return $this;
    }
    
    /**
     * Sanitiza um campo
     */
    public function sanitize(string $field): string {
        if (isset($this->data[$field])) {
            $this->data[$field] = htmlspecialchars(strip_tags(trim($this->data[$field])), ENT_QUOTES, 'UTF-8');
        }
        return $this->data[$field] ?? '';
    }
    
    /**
     * Sanitiza todos os campos
     */
    public function sanitizeAll(): array {
        foreach ($this->data as $key => $value) {
            if (is_string($value)) {
                $this->data[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            }
        }
        return $this->data;
    }
    
    /**
     * Verifica se a validação passou
     */
    public function passes(): bool {
        return empty($this->errors);
    }
    
    /**
     * Verifica se a validação falhou
     */
    public function fails(): bool {
        return !$this->passes();
    }
    
    /**
     * Obtém os erros de validação
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Obtém o primeiro erro de validação
     */
    public function getFirstError(): string {
        return reset($this->errors) ?? '';
    }
    
    /**
     * Obtém os dados validados
     */
    public function getData(): array {
        return $this->data;
    }
    
    /**
     * Define os dados para validação
     */
    public function setData(array $data): self {
        $this->data = $data;
        return $this;
    }
}
