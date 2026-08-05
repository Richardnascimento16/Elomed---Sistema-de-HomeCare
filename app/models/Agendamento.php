<?php
require_once __DIR__ . '/Model.php';

class Agendamento extends Model {
    protected $table = 'tbl_agendamento'; 
    
    /**
     * Atualiza status do agendamento
     */
    public function atualizarStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }
    
    /**
     * Atualiza status do agendamento com condição
     */
    public function atualizarStatusComCondicao($id, $status, $statusAtual) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id AND status = :status_atual");
        return $stmt->execute(['status' => $status, 'id' => $id, 'status_atual' => $statusAtual]);
    }
    
    /**
     * Busca paciente vinculado ao agendamento
     */
    public function getPacienteId($agendamentoId) {
        $stmt = $this->db->prepare("SELECT fk_paciente FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $agendamentoId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['fk_paciente'] ?? null;
    }
    
    /**
     * Cria novo agendamento
     */
    public function criar($dados) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (fk_paciente, fk_profissional, data_agendamento, horario_inicio, horario_fim) 
                                      VALUES (:fk_paciente, :fk_profissional, :data_agendamento, :horario_inicio, :horario_fim)");
        return $stmt->execute([
            'fk_paciente' => $dados['fk_paciente'],
            'fk_profissional' => $dados['fk_profissional'],
            'data_agendamento' => $dados['data_agendamento'],
            'horario_inicio' => $dados['horario_inicio'],
            'horario_fim' => $dados['horario_fim']
        ]);
    }
}