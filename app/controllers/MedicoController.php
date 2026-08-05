<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Protocolo.php';
require_once __DIR__ . '/../models/Profissional.php';
require_once __DIR__ . '/../models/Agendamento.php';
require_once __DIR__ . '/../models/Prontuario.php';
require_once __DIR__ . '/../controllers/IAController.php';

class MedicoController {
    private $usuarioModel;
    private $protocoloModel;
    private $profissionalModel;
    private $agendamentoModel;
    private $prontuarioModel;
    private $iaController;
    
    public function __construct() {
        Session::start();
        if (!Session::has('usuario_id')) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }
        
        $perfil = Session::get('usuario_perfil');
        if ($perfil !== Constants::PERFIL_ADMIN && $perfil !== Constants::PERFIL_MEDICO) {
            header('Location: index.php?controller=painel&action=index');
            exit;
        }
        
        $this->usuarioModel = new Usuario();
        $this->protocoloModel = new Protocolo();
        $this->profissionalModel = new Profissional();
        $this->agendamentoModel = new Agendamento();
        $this->prontuarioModel = new Prontuario();
        $this->iaController = new IAController();
    }

    public function index() {
        $id_usuario = Session::get('usuario_id');
        $id_profissional = $this->profissionalModel->getIdByUsuario($id_usuario);

        $nome_profissional = [];
        if ($id_profissional) {
            $nome_profissional = $this->profissionalModel->getDadosComNome($id_profissional);
        }

        $visitas_hoje = [];
        if ($id_profissional) {
            $visitas_hoje = $this->profissionalModel->contarVisitasHoje($id_profissional);
        }

        $visitas_concluidas_hoje = [];
        if ($id_profissional) {
            $visitas_concluidas_hoje = $this->profissionalModel->contarVisitasConcluidasHoje($id_profissional);
        }

        $consulta_hoje = [];
        if ($id_profissional) {
            $consulta_hoje = $this->profissionalModel->getFilaAtendimentosHoje($id_profissional);
        }

        require_once __DIR__ . '/../views/medico/index.php';
    }

    public function pendencias() {
        $id_usuario = Session::get('usuario_id');
        $id_profissional = $this->profissionalModel->getIdByUsuario($id_usuario);

        $aprovar_triagem = null; 
        if ($id_profissional) {
            $aprovar_triagem = $this->profissionalModel->getTriagensPendentes($id_profissional);
        }

        $aprovar_agendamento = [];
        if ($id_profissional) {
            $aprovar_agendamento = $this->profissionalModel->getAgendamentosPendentes($id_profissional);
        }

        $agendamentos_futuros = [];
        if ($id_profissional) {
            $agendamentos_futuros = $this->profissionalModel->getAgendamentosFuturos($id_profissional);
        }

        require_once __DIR__ . '/../views/medico/pendencias.php';
    }
    
    public function minha_agenda() {
        $id_usuario = Session::get('usuario_id');
        $id_profissional = $this->profissionalModel->getIdByUsuario($id_usuario);

        $agendamentos_futuros = [];
        if ($id_profissional) {
            $agendamentos_futuros = $this->profissionalModel->getAgendamentosFuturos($id_profissional);
        }

        $agendamentos_passados = [];
        if ($id_profissional) {
            $agendamentos_passados = $this->profissionalModel->getAgendamentosPassados($id_profissional);
        }

        require_once __DIR__ . '/../views/medico/minha_agenda.php';
    }
    
    public function historico_visitas() {
        $id_usuario = Session::get('usuario_id');
        $id_profissional = $this->profissionalModel->getIdByUsuario($id_usuario);

        $visitas_concluidas = [];
        if ($id_profissional) {
            $visitas_concluidas = $this->profissionalModel->getVisitasConcluidas($id_profissional);
        }

        require_once __DIR__ . '/../views/medico/historico_visitas.php';
    }

    public function aceitar() {
        $id_agendamento = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if ($id_agendamento) {
            $this->agendamentoModel->atualizarStatusComCondicao($id_agendamento, Constants::AGENDAMENTO_AGENDADO, Constants::AGENDAMENTO_PENDENTE);
            
            Logger::audit(Constants::AUDITORIA_ACAO_ATUALIZAR_AGENDAMENTO, [
                'id_agendamento' => $id_agendamento,
                'novo_status' => Constants::AGENDAMENTO_AGENDADO
            ]);
        }

        header('Location: index.php?controller=Medico&action=index');
        exit;
    }

    public function rejeitar() {
        $id_agendamento = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if ($id_agendamento) {
            $this->agendamentoModel->atualizarStatusComCondicao($id_agendamento, Constants::AGENDAMENTO_CANCELADO, Constants::AGENDAMENTO_PENDENTE);
            
            Logger::audit(Constants::AUDITORIA_ACAO_ATUALIZAR_AGENDAMENTO, [
                'id_agendamento' => $id_agendamento,
                'novo_status' => Constants::AGENDAMENTO_CANCELADO
            ]);
        }

        header('Location: index.php?controller=Medico&action=index');
        exit;
    }

    public function iniciar_atendimento() {
        $id_agendamento = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $protocolos = $this->protocoloModel->getAllAtivos();
        
        require_once __DIR__ . '/../views/medico/iniciar_atendimento.php';
    }

    /**
     * Retorna perguntas de um protocolo via AJAX
     */
    public function get_perguntas_protocolo() {
        header('Content-Type: application/json');
        
        $protocoloId = isset($_GET['protocolo_id']) ? (int)$_GET['protocolo_id'] : null;
        
        if (!$protocoloId) {
            echo json_encode(['success' => false, 'error' => 'Protocolo ID não fornecido']);
            exit;
        }
        
        $perguntas = $this->protocoloModel->getPerguntas($protocoloId);
        
        echo json_encode([
            'success' => true,
            'perguntas' => $perguntas
        ]);
        exit;
    }

    /**
     * Processa a anamnese usando o controller de IA
     */
    public function dados_atendimento() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=Medico&action=index');
            exit;
        }

        // Valida CSRF
        if (!CSRF::validateRequest()) {
            Session::flash('error', 'Token de segurança inválido');
            header('Location: index.php?controller=Medico&action=index');
            exit;
        }

        // Usa o IAController para gerar o diagnóstico
        $dados_view = $this->iaController->gerar_diagnostico();

        // Salva os dados da consulta no prontuário
        $id_agendamento = $dados_view['id_agendamento'];
        $queixa = $dados_view['queixa'];
        $historico = $dados_view['historico'];
        $insights = $dados_view['insights'];

        if ($id_agendamento) {
            $this->salvarProntuario($id_agendamento, $queixa, $historico, $insights);
        }

        // Carrega a view de diagnóstico passando os dados dinâmicos
        require_once __DIR__ . '/../views/medico/diagnostico_ia.php';
    }

    /**
     * Salva os dados da consulta no prontuário do paciente
     */
    private function salvarProntuario($id_agendamento, $queixa, $historico, $insights) {
        $id_paciente = $this->agendamentoModel->getPacienteId($id_agendamento);
        
        if ($id_paciente) {
            $prontuario = $this->prontuarioModel->getByAgendamento($id_agendamento);
            $insights_json = json_encode($insights);
            
            if ($prontuario) {
                $this->prontuarioModel->atualizar($prontuario['id'], [
                    'queixa' => $queixa,
                    'historico' => $historico,
                    'analise_ia' => $insights_json
                ]);
                
                Logger::audit(Constants::AUDITORIA_ACAO_ATUALIZAR_PRONTUARIO, [
                    'id_prontuario' => $prontuario['id'],
                    'id_agendamento' => $id_agendamento
                ]);
            } else {
                $this->prontuarioModel->criar([
                    'fk_paciente' => $id_paciente,
                    'fk_agendamento' => $id_agendamento,
                    'queixa' => $queixa,
                    'historico' => $historico,
                    'analise_ia' => $insights_json
                ]);
                
                Logger::audit(Constants::AUDITORIA_ACAO_CRIAR_PRONTUARIO, [
                    'fk_paciente' => $id_paciente,
                    'fk_agendamento' => $id_agendamento
                ]);
            }
        }
    }

    /**
     * Salva o diagnóstico final e conduta do médico no prontuário
     */
    public function salvar_diagnostico_final() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=Medico&action=index');
            exit;
        }

        // Valida CSRF
        if (!CSRF::validateRequest()) {
            Session::flash('error', 'Token de segurança inválido');
            header('Location: index.php?controller=Medico&action=index');
            exit;
        }

        $id_agendamento = $_POST['id_agendamento'] ?? null;
        $diagnostico_final = $_POST['diagnostico_final'] ?? '';
        $conduta_medica = $_POST['ajuste_conduta'] ?? '';

        if ($id_agendamento) {
            $this->prontuarioModel->atualizarDiagnosticoFinal($id_agendamento, $diagnostico_final, $conduta_medica);
            $this->agendamentoModel->atualizarStatus($id_agendamento, Constants::AGENDAMENTO_CONCLUIDO);
            
            Logger::audit(Constants::AUDITORIA_ACAO_ATUALIZAR_PRONTUARIO, [
                'id_agendamento' => $id_agendamento,
                'diagnostico_final' => substr($diagnostico_final, 0, 100)
            ]);
        }

        header('Location: index.php?controller=Medico&action=index');
        exit;
    }
}