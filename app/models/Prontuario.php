<?php
require_once __DIR__ . '/Model.php';

class Prontuario extends Model {
    protected $table = 'tbl_prontuario'; 
    
    /**
     * Busca prontuário por agendamento
     */
    public function getByAgendamento($agendamentoId) {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE fk_agendamento = :id_agendamento");
        $stmt->execute(['id_agendamento' => $agendamentoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cria novo prontuário
     */
    public function criar($dados) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} 
                                      (fk_paciente, fk_agendamento, queixa_principal, historico_medico, analise_ia) 
                                      VALUES (:fk_paciente, :fk_agendamento, :queixa, :historico, :analise_ia)");
        return $stmt->execute([
            'fk_paciente' => $dados['fk_paciente'],
            'fk_agendamento' => $dados['fk_agendamento'],
            'queixa' => $dados['queixa'],
            'historico' => $dados['historico'],
            'analise_ia' => $dados['analise_ia']
        ]);
    }
    
    /**
     * Atualiza prontuário existente
     */
    public function atualizar($id, $dados) {
        $stmt = $this->db->prepare("UPDATE {$this->table} 
                                      SET queixa_principal = :queixa, 
                                          historico_medico = :historico, 
                                          analise_ia = :analise_ia,
                                          updated_at = NOW()
                                      WHERE id = :id");
        return $stmt->execute([
            'queixa' => $dados['queixa'],
            'historico' => $dados['historico'],
            'analise_ia' => $dados['analise_ia'],
            'id' => $id
        ]);
    }
    
    /**
     * Atualiza diagnóstico final e conduta
     */
    public function atualizarDiagnosticoFinal($agendamentoId, $diagnostico, $conduta) {
        $stmt = $this->db->prepare("UPDATE {$this->table} 
                                      SET diagnostico_final = :diagnostico,
                                          conduta_medica = :conduta,
                                          updated_at = NOW()
                                      WHERE fk_agendamento = :fk_agendamento");
        return $stmt->execute([
            'diagnostico' => $diagnostico,
            'conduta' => $conduta,
            'fk_agendamento' => $agendamentoId
        ]);
    }
}