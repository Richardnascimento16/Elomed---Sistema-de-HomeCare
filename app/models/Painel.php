<?php
require_once __DIR__ . '/Model.php';

class Painel extends Model {
    public function getEstatisticaPainel(): array {
        $totalPacientesAtendidos = $this->db->query("SELECT COUNT(*) FROM tbl_visita WHERE status = 'concluido'")->fetchColumn();
        $equipeEmCampo = $this->db->query("SELECT COUNT(DISTINCT fk_profissional) FROM tbl_visita WHERE status = 'em_andamento'")->fetchColumn();
        $visitasHoje = $this->db->query("SELECT COUNT(*) FROM tbl_visita WHERE DATE(horario_inicio) = CURDATE()")->fetchColumn();
        $triagensPendentes = $this->db->query("SELECT COUNT(*) FROM tbl_triagem WHERE status = 'pendente'")->fetchColumn();

        return [
            'pacientes_atendidos' => (int)$totalPacientesAtendidos,
            'equipe_campo'        => (int)$equipeEmCampo,
            'visitas_hoje'        => (int)$visitasHoje,
            'triagens_pendentes'  => (int)$triagensPendentes
        ];
    }

    public function getEscalaDoDia(): array {
        $sql = "SELECT 
            a.id AS agendamento_id,
            a.data_agendamento,
            a.horario_inicio,
            a.horario_fim,
            a.status,
            u_prof.nome AS profissional_nome,
            prof.especialidade,
            u_pac.nome AS paciente_nome
            FROM tbl_agendamento AS a
            JOIN tbl_profissional AS prof ON a.fk_profissional = prof.id
            JOIN tbl_usuario AS u_prof ON prof.fk_usuario = u_prof.id
            JOIN tbl_paciente AS pac ON a.fk_paciente = pac.id
            JOIN tbl_usuario AS u_pac ON pac.fk_usuario = u_pac.id";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}