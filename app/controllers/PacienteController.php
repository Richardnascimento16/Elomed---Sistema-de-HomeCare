<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Paciente.php';

class PacienteController {
    private $usuarioModel;
    private $pacienteModel;
    
    public function __construct() {
        Session::start();
        if (!Session::has('usuario_id')) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }
        
        $perfil = Session::get('usuario_perfil');
        if ($perfil !== Constants::PERFIL_ADMIN && $perfil !== Constants::PERFIL_PACIENTE) {
            header('Location: index.php?controller=painel&action=index');
            exit;
        }
        
        $this->usuarioModel = new Usuario();
        $this->pacienteModel = new Paciente();
    }
    
    public function index() {
        $id_usuario = Session::get('usuario_id');
        $id_paciente = $this->pacienteModel->getIdByUsuario($id_usuario);

        $ultima_triagem = null;
        if ($id_paciente) {
            $ultima_triagem = $this->pacienteModel->getUltimaTriagem($id_paciente);
        }

        $proximo_agendamento = null;
        if ($id_paciente) {
            $proximo_agendamento = $this->pacienteModel->getProximoAgendamento($id_paciente);
        }

        $agendamentos_futuros = [];
        if ($id_paciente) {
            $agendamentos_futuros = $this->pacienteModel->getAgendamentosFuturos($id_paciente);
        }

        require_once __DIR__ . '/../views/paciente/index.php';
    }

    public function minhas_triagens() {
        $id_usuario = Session::get('usuario_id');
        $id_paciente = $this->pacienteModel->getIdByUsuario($id_usuario);

        $triagens = [];
        if ($id_paciente) {
            $triagens = $this->pacienteModel->getTriagens($id_paciente);
        }

        require_once __DIR__ . '/../views/paciente/minhas_triagens.php';
    }

    public function processar_triagem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=paciente&action=minhas_triagens');
            exit;
        }

        // Valida CSRF
        if (!CSRF::validateRequest()) {
            Session::flash('error', 'Token de segurança inválido');
            header('Location: index.php?controller=paciente&action=minhas_triagens');
            exit;
        }

        $id_usuario = Session::get('usuario_id');
        $id_paciente = $this->pacienteModel->getIdByUsuario($id_usuario);

        if (!$id_paciente) {
            Session::flash('error', 'Paciente não encontrado');
            header('Location: index.php?controller=paciente&action=minhas_triagens');
            exit;
        }

        $nome_solicitante = $_POST['nome_solicitante'] ?? '';

        if (empty($nome_solicitante)) {
            Session::flash('error', 'Nome do solicitante é obrigatório');
            header('Location: index.php?controller=paciente&action=minhas_triagens');
            exit;
        }

        $id_triagem = $this->pacienteModel->criarTriagem($id_paciente, $nome_solicitante);

        if ($id_triagem) {
            Logger::audit(Constants::AUDITORIA_ACAO_CRIAR_TRIAGEM, [
                'id_paciente' => $id_paciente,
                'nome_solicitante' => $nome_solicitante
            ]);
            Session::flash('success', 'Triagem solicitada com sucesso');
        } else {
            Session::flash('error', 'Erro ao solicitar triagem');
        }

        header('Location: index.php?controller=paciente&action=minhas_triagens');
        exit;
    }

    public function agendamento() {
        $id_usuario = Session::get('usuario_id');
        $id_paciente = $this->pacienteModel->getIdByUsuario($id_usuario);

        $agendamentos = [];
        if ($id_paciente) {
            $agendamentos = $this->pacienteModel->getAgendamentos($id_paciente);
        }

        require_once __DIR__ . '/../views/paciente/agendamento.php';
    }

    public function historico() {
        $id_usuario = Session::get('usuario_id');
        $id_paciente = $this->pacienteModel->getIdByUsuario($id_usuario);

        $visitas = [];
        if ($id_paciente) {
            $visitas = $this->pacienteModel->getVisitasConcluidas($id_paciente);
        }

        require_once __DIR__ . '/../views/paciente/historico.php';
    }
}
