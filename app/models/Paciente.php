<?php
require_once __DIR__ . '/Model.php';

class Paciente extends Model {
    protected $table = 'tbl_paciente';
    
    /**
     * Busca ID do paciente pelo ID do usuário
     */
    public function getIdByUsuario($usuarioId) {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE fk_usuario = :fk");
        $stmt->execute(['fk' => $usuarioId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'] ?? null;
    }
    
    /**
     * Busca última triagem do paciente
     */
    public function getUltimaTriagem($pacienteId) {
        $stmt = $this->db->prepare("SELECT nome_solicitante, status, criado_em 
                                     FROM tbl_triagem 
                                     WHERE fk_paciente = :fk 
                                     ORDER BY criado_em DESC LIMIT 1");
        $stmt->execute(['fk' => $pacienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todas as triagens do paciente
     */
    public function getTriagens($pacienteId) {
        $stmt = $this->db->prepare("SELECT id, nome_solicitante, status, criado_em AS data_criacao
                                     FROM tbl_triagem 
                                     WHERE fk_paciente = :fk 
                                     ORDER BY criado_em DESC");
        $stmt->execute(['fk' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todos os agendamentos do paciente
     */
    public function getAgendamentos($pacienteId) {
        $stmt = $this->db->prepare("SELECT a.id, a.data_agendamento, a.horario_inicio, a.horario_fim, a.status,
                                             u.nome AS nome_profissional, p.especialidade
                                      FROM tbl_agendamento a
                                      JOIN tbl_profissional p ON a.fk_profissional = p.id
                                      JOIN tbl_usuario u ON p.fk_usuario = u.id
                                      WHERE a.fk_paciente = :fk 
                                      ORDER BY a.data_agendamento DESC, a.horario_inicio DESC");
        $stmt->execute(['fk' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca visitas concluídas do paciente
     */
    public function getVisitasConcluidas($pacienteId) {
        $stmt = $this->db->prepare("SELECT a.id, a.data_agendamento, a.horario_inicio, a.horario_fim,
                                             u.nome AS nome_profissional, p.especialidade,
                                             pr.diagnostico_final, pr.conduta_medica
                                      FROM tbl_agendamento a
                                      JOIN tbl_profissional p ON a.fk_profissional = p.id
                                      JOIN tbl_usuario u ON p.fk_usuario = u.id
                                      LEFT JOIN tbl_prontuario pr ON a.id = pr.fk_agendamento
                                      WHERE a.fk_paciente = :fk 
                                        AND a.status = 'concluido'
                                      ORDER BY a.data_agendamento DESC, a.horario_inicio DESC");
        $stmt->execute(['fk' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cria nova triagem para o paciente
     */
    public function criarTriagem($pacienteId, $nomeSolicitante) {
        $stmt = $this->db->prepare("INSERT INTO tbl_triagem (fk_paciente, nome_solicitante, status, criado_em) 
                                     VALUES (:fk_paciente, :nome_solicitante, 'pendente', NOW())");
        $stmt->execute([
            'fk_paciente' => $pacienteId,
            'nome_solicitante' => $nomeSolicitante
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Busca próximo agendamento futuro do paciente
     */
    public function getProximoAgendamento($pacienteId) {
        $stmt = $this->db->prepare("SELECT a.data_agendamento, a.horario_inicio, a.horario_fim, a.status,
                                             u.nome AS nome_profissional, p.especialidade
                                      FROM tbl_agendamento a
                                      JOIN tbl_profissional p ON a.fk_profissional = p.id
                                      JOIN tbl_usuario u ON p.fk_usuario = u.id
                                      WHERE a.fk_paciente = :fk 
                                        AND a.data_agendamento >= CURDATE()
                                        AND a.status IN ('agendado', 'pendente')
                                      ORDER BY a.data_agendamento ASC, a.horario_inicio ASC 
                                      LIMIT 1");
        $stmt->execute(['fk' => $pacienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca agendamentos futuros do paciente
     */
    public function getAgendamentosFuturos($pacienteId) {
        $stmt = $this->db->prepare("SELECT a.data_agendamento, a.horario_inicio, a.horario_fim, a.status,
                                             u.nome AS nome_profissional, p.especialidade
                                      FROM tbl_agendamento a
                                      JOIN tbl_profissional p ON a.fk_profissional = p.id
                                      JOIN tbl_usuario u ON p.fk_usuario = u.id
                                      WHERE a.fk_paciente = :fk 
                                        AND a.data_agendamento >= CURDATE()
                                        AND a.status IN ('agendado', 'pendente')
                                      ORDER BY a.data_agendamento ASC, a.horario_inicio ASC 
                                      LIMIT 10");
        $stmt->execute(['fk' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cria novo paciente
     */
    public function criar($dados) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (fk_usuario, contato_emergencia, data_nascimento) VALUES (:fk_usuario, :contato_emergencia, :data_nascimento)");
        return $stmt->execute([
            'fk_usuario' => $dados['fk_usuario'],
            'contato_emergencia' => $dados['contato_emergencia'],
            'data_nascimento' => $dados['data_nascimento']
        ]);
    }
}