<?php
require_once __DIR__ . '/../../config/Database.php';

abstract class Model {
    protected PDO $db;
    protected $table; 
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all() {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'ativo' ORDER BY {$this->primaryKey} DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $valor_banco = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$valor_banco){
            return false;
        }

        if($valor_banco['status'] === 'ativo'){
                return $valor_banco;
            }
            return false;
        }

    public function insert($sql, array $dados_inserir = []) {
        $stmt = $this->db->prepare($sql); 
        $stmt->execute($dados_inserir); 
        return $stmt;
    }

    public function update($sql, array $dados_atualizar = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($dados_atualizar);
    }

    public function delete(string $sql, array $dados_remover = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($dados_remover);
    }

    public function buscarPorNome($nome) {
        $stmt = $this->db->prepare("SELECT id, nome, email, perfil FROM tbl_usuario WHERE nome LIKE :nome");
        $stmt->execute(['nome' => "%$nome%"]);
        return $stmt->fetchAll();
    }

    public function buscarPorNomeProfissional($nome) {
        $stmt = $this->db->prepare("SELECT id, nome, email, perfil FROM tbl_usuario WHERE nome LIKE :nome AND perfil in ('medico', 'enfermeiro', 'tecnico_enfermagem')");
        $stmt->execute(['nome' => "%$nome%"]);
        return $stmt->fetchAll();
    }

    public function buscarPorNomePaciente($nome) {
        $stmt = $this->db->prepare("SELECT id, nome, email, perfil FROM tbl_usuario WHERE nome LIKE :nome AND perfil = 'paciente'");
        $stmt->execute(['nome' => "%$nome%"]);
        return $stmt->fetchAll();
    }

    public function listarProfissionaisAtendendo($sql) {
    $stmt = $this->db->prepare($sql);
    $stmt->execute(); // ✓ Corrigido: Sem passar a string $sql aqui dentro
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function buscarIdPaciente($sql1, $dados_inserir1){
        $stmt = $this->db->prepare($sql1);
        $stmt->execute($dados_inserir1);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}