<?php
require_once __DIR__ . '/Model.php';

class Protocolo extends Model {
    protected $table = 'tbl_protocolos';
    
    /**
     * Busca todos os protocolos ativos
     */
    public function getAllAtivos() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE ativo = TRUE ORDER BY nome ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca um protocolo por ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca perguntas de um protocolo
     */
    public function getPerguntas($protocoloId) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_perguntas_protocolo WHERE fk_protocolo = ? ORDER BY ordem ASC");
        $stmt->execute([$protocoloId]);
        $perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decodificar JSON das opções e condicionais
        foreach ($perguntas as &$pergunta) {
            if (!empty($pergunta['opcoes'])) {
                $pergunta['opcoes'] = json_decode($pergunta['opcoes'], true);
            }
            if (!empty($pergunta['condicional'])) {
                $pergunta['condicional'] = json_decode($pergunta['condicional'], true);
            }
        }
        
        return $perguntas;
    }
    
    /**
     * Busca protocolo por nome (case insensitive)
     */
    public function getByNome($nome) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE LOWER(nome) = LOWER(?)");
        $stmt->execute([$nome]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cria um novo protocolo
     */
    public function create($dados) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nome, descricao, categoria, ativo) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'] ?? null,
            $dados['categoria'] ?? 'agudo',
            $dados['ativo'] ?? true
        ]);
    }
    
    /**
     * Adiciona pergunta a um protocolo
     */
    public function addPergunta($dados) {
        $stmt = $this->db->prepare("INSERT INTO tbl_perguntas_protocolo (fk_protocolo, ordem, tipo, pergunta, placeholder, obrigatorio, opcoes, condicional) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $dados['fk_protocolo'],
            $dados['ordem'] ?? 0,
            $dados['tipo'],
            $dados['pergunta'],
            $dados['placeholder'] ?? null,
            $dados['obrigatorio'] ?? false,
            !empty($dados['opcoes']) ? json_encode($dados['opcoes']) : null,
            !empty($dados['condicional']) ? json_encode($dados['condicional']) : null
        ]);
    }
}
