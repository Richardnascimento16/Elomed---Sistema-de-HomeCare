<?php
require_once __DIR__ . '/Model.php';

class Profissional extends Model {
    protected $table = 'tbl_profissional'; 
    
    /**
     * Busca ID do profissional pelo ID do usuário
     */
    public function getIdByUsuario($usuarioId) {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE fk_usuario = :fk");
        $stmt->execute(['fk' => $usuarioId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'] ?? null;
    }
    
    /**
     * Busca dados do profissional com nome do usuário
     */
    public function getDadosComNome($profissionalId) {
        $stmt = $this->db->prepare("SELECT u.nome AS nome_profissional, p.numero_registro 
                                     FROM tbl_usuario u
                                     JOIN {$this->table} p ON p.fk_usuario = u.id
                                     WHERE p.id = :id");
        $stmt->execute(['id' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Conta visitas agendadas para hoje
     */
    public function contarVisitasHoje($profissionalId) {
        $stmt = $this->db->prepare("SELECT count(id) AS total_visitas
                                      FROM tbl_agendamento 
                                      WHERE fk_profissional = :fk 
                                        AND data_agendamento = CURDATE()
                                        AND status = 'agendado'");
        $stmt->execute(['fk' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Conta visitas concluídas hoje
     */
    public function contarVisitasConcluidasHoje($profissionalId) {
        $stmt = $this->db->prepare("SELECT count(id) AS visitas_concluidas
                                      FROM tbl_agendamento 
                                      WHERE fk_profissional = :fk 
                                        AND data_agendamento = CURDATE()
                                        AND status = 'concluido'");
        $stmt->execute(['fk' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca fila de atendimentos de hoje
     */
    public function getFilaAtendimentosHoje($profissionalId) {
        $stmt = $this->db->prepare("SELECT a.id, a.data_agendamento, a.horario_inicio, a.horario_fim, a.status,
                                             u.nome AS nome_paciente
                                      FROM tbl_agendamento a
                                      JOIN tbl_paciente p ON a.fk_paciente = p.id
                                      JOIN tbl_usuario u ON p.fk_usuario = u.id
                                      WHERE a.fk_profissional = :fk 
                                        AND a.data_agendamento = CURDATE()
                                        AND a.status IN ('agendado', 'em_andamento')
                                      ORDER BY a.horario_inicio ASC");
        $stmt->execute(['fk' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Busca triagens pendentes para aprovação
     */
    public function getTriagensPendentes($profissionalId) {
        $stmt = $this->db->prepare("SELECT t.nome_solicitante, t.status, t.criado_em 
                                      FROM tbl_triagem t
                                      JOIN tbl_agendamento a ON t.fk_paciente = a.fk_paciente
                                      WHERE a.fk_profissional = :fk_medico
                                      ORDER BY t.criado_em DESC LIMIT 1");
        $stmt->execute(['fk_medico' => $profissionalId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca agendamentos pendentes para aprovação
     */
    public function getAgendamentosPendentes($profissionalId) {
        $stmt = $this->db->prepare("SELECT a.id, a.data_agendamento, a.horario_inicio, a.horario_fim, a.status, 
                                             u.nome AS nome_paciente
                                      FROM tbl_agendamento a
                                      JOIN tbl_paciente pac ON a.fk_paciente = pac.id
                                      JOIN tbl_usuario u ON pac.fk_usuario = u.id
                                      WHERE a.fk_profissional = :fk 
                                        AND a.data_agendamento >= CURDATE()
                                        AND a.status = 'pendente'
                                      ORDER BY a.data_agendamento ASC, a.horario_inicio ASC");
        $stmt->execute(['fk' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca agendamentos futuros
     */
    public function getAgendamentosFuturos($profissionalId) {
        $stmt = $this->db->prepare("SELECT a.data_agendamento, a.horario_inicio, a.horario_fim, a.status,
                                             u.nome AS nome_paciente, p.especialidade
                                      FROM tbl_agendamento a
                                      JOIN {$this->table} p ON a.fk_profissional = p.id
                                      JOIN tbl_paciente pac ON a.fk_paciente = pac.id
                                      JOIN tbl_usuario u ON pac.fk_usuario = u.id
                                      WHERE a.fk_profissional = :fk 
                                        AND a.data_agendamento >= CURDATE()
                                        AND a.status IN ('agendado', 'pendente')
                                      ORDER BY a.data_agendamento ASC, a.horario_inicio ASC 
                                      LIMIT 10");
        $stmt->execute(['fk' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca agendamentos passados
     */
    public function getAgendamentosPassados($profissionalId) {
        $stmt = $this->db->prepare("SELECT a.data_agendamento, a.horario_inicio, a.horario_fim, a.status,
                                             u.nome AS nome_paciente, p.especialidade
                                      FROM tbl_agendamento a
                                      JOIN {$this->table} p ON a.fk_profissional = p.id
                                      JOIN tbl_paciente pac ON a.fk_paciente = pac.id
                                      JOIN tbl_usuario u ON pac.fk_usuario = u.id
                                      WHERE a.fk_profissional = :fk 
                                        AND a.data_agendamento < CURDATE()
                                      ORDER BY a.data_agendamento DESC, a.horario_inicio DESC 
                                      LIMIT 10");
        $stmt->execute(['fk' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca visitas concluídas do profissional
     */
    public function getVisitasConcluidas($profissionalId) {
        $stmt = $this->db->prepare("SELECT a.id, a.data_agendamento, a.horario_inicio, a.horario_fim, a.status,
                                             u.nome AS nome_paciente, p.especialidade,
                                             pr.diagnostico_final, pr.conduta_medica
                                      FROM tbl_agendamento a
                                      JOIN {$this->table} p ON a.fk_profissional = p.id
                                      JOIN tbl_paciente pac ON a.fk_paciente = pac.id
                                      JOIN tbl_usuario u ON pac.fk_usuario = u.id
                                      LEFT JOIN tbl_prontuario pr ON a.id = pr.fk_agendamento
                                      WHERE a.fk_profissional = :fk 
                                        AND a.status = 'concluido'
                                      ORDER BY a.data_agendamento DESC, a.horario_inicio DESC");
        $stmt->execute(['fk' => $profissionalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lista profissionais ativos para seleção
     */
    public function listarAtivos() {
        $stmt = $this->db->prepare("SELECT p.id AS id_profissional, u.nome AS nome_profissional, p.especialidade 
                                     FROM tbl_usuario u
                                     JOIN {$this->table} p ON p.fk_usuario = u.id
                                     WHERE u.status = 'ativo' 
                                     ORDER BY p.especialidade ASC, u.nome ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cria novo profissional
     */
    public function criar($dados) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (fk_usuario, especialidade, numero_registro) VALUES (:fk_usuario, :especialidade, :numero_registro)");
        return $stmt->execute([
            'fk_usuario' => $dados['fk_usuario'],
            'especialidade' => $dados['especialidade'],
            'numero_registro' => $dados['numero_registro']
        ]);
    }
}