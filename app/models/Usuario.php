<?php
require_once __DIR__ . '/Model.php';

class Usuario extends Model {
    protected $table = 'tbl_usuario';
    
    /**
     * Busca usuário por email
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verifica login do usuário
     */
    public function verificarLogin($email, $senha, $perfil) {
        $usuario = $this->getByEmail($email);
        
        if ($usuario && password_verify($senha, $usuario['senha']) && $usuario['perfil'] === $perfil) {
            return $usuario;
        }
        
        return false;
    }
    
    /**
     * Cria novo usuário
     */
    public function criar($dados) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)");
        return $stmt->execute([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
            'perfil' => $dados['perfil']
        ]);
    }
    
    /**
     * Atualiza usuário
     */
    public function atualizar($id, $dados) {
        if (!empty($dados['senha'])) {
            $sql = "UPDATE {$this->table} SET nome = :nome, email = :email, senha = :senha, perfil = :perfil WHERE id = :id";
            $params = [
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
                'perfil' => $dados['perfil'],
                'id' => $id
            ];
        } else {
            $sql = "UPDATE {$this->table} SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id";
            $params = [
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'perfil' => $dados['perfil'],
                'id' => $id
            ];
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Desativa usuário
     */
    public function desativar($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'desativado' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}