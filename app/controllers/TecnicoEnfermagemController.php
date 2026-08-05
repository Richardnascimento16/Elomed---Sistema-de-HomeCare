<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../models/Profissional.php';

class TecnicoEnfermagemController {
    private $profissionalModel;
    
    public function __construct() {
        Session::start();
        if (!Session::has('usuario_id')) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }
        
        $perfil = Session::get('usuario_perfil');
        if ($perfil !== Constants::PERFIL_ADMIN && $perfil !== Constants::PERFIL_TECNICO_ENFERMAGEM) {
            header('Location: index.php?controller=painel&action=index');
            exit;
        }
        
        $this->profissionalModel = new Profissional();
    }
    
    public function index() {
        $id_usuario = Session::get('usuario_id');
        $id_profissional = $this->profissionalModel->getIdByUsuario($id_usuario);

        $visitas_hoje = [];
        if ($id_profissional) {
            $visitas_hoje = $this->profissionalModel->contarVisitasHoje($id_profissional);
        }

        $visitas_concluidas_hoje = [];
        if ($id_profissional) {
            $visitas_concluidas_hoje = $this->profissionalModel->contarVisitasConcluidasHoje($id_profissional);
        }

        $fila_atendimentos = [];
        if ($id_profissional) {
            $fila_atendimentos = $this->profissionalModel->getFilaAtendimentosHoje($id_profissional);
        }

        require_once __DIR__ . '/../views/tecnico_enfermagem/index.php';
    }

    public function minha_agenda() {
        $id_usuario = Session::get('usuario_id');
        $id_profissional = $this->profissionalModel->getIdByUsuario($id_usuario);

        $agendamentos_futuros = [];
        if ($id_profissional) {
            $agendamentos_futuros = $this->profissionalModel->getAgendamentosFuturos($id_profissional);
        }

        require_once __DIR__ . '/../views/tecnico_enfermagem/minha_agenda.php';
    }

    public function historico() {
        $id_usuario = Session::get('usuario_id');
        $id_profissional = $this->profissionalModel->getIdByUsuario($id_usuario);

        $visitas_concluidas = [];
        if ($id_profissional) {
            $visitas_concluidas = $this->profissionalModel->getVisitasConcluidas($id_profissional);
        }

        require_once __DIR__ . '/../views/tecnico_enfermagem/historico.php';
    }
}