<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Profissional.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Agendamento.php';

class AgendamentoController {
    private $usuarioModel;
    private $profissionalModel;
    private $pacienteModel;
    private $agendamentoModel;

    public function __construct() {
        Session::start();
        if (!Session::has('usuario_id')) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }
        $this->usuarioModel = new Usuario();
        $this->profissionalModel = new Profissional();
        $this->pacienteModel = new Paciente();
        $this->agendamentoModel = new Agendamento();
    }

    public function solicitar_agendamento() {
        $id_paciente = Session::get('usuario_id');

        if (!$id_paciente) {
            header("Location: index.php?controller=login&action=index");
            exit;
        }

        $rotas_painel = [
            Constants::PERFIL_ADMIN => 'painel',
            Constants::PERFIL_MEDICO => 'Medico',
            Constants::PERFIL_ENFERMEIRO => 'Enfermeiro',
            Constants::PERFIL_TECNICO_ENFERMAGEM => 'TecnicoEnfermagem',
            'paciente'            => 'paciente'
        ];

        $perfil = Session::get('usuario_perfil');

        if ($perfil !== Constants::PERFIL_PACIENTE && $perfil !== Constants::PERFIL_ADMIN) {
            $destino = $rotas_painel[$perfil] ?? 'login';
            header("Location: index.php?controller={$destino}&action=index");
            exit;
        }

        $profissionais = $this->profissionalModel->listarAtivos();
        require_once __DIR__ . '/../views/paciente/solicitar_agendamento.php';
    }

    public function enviar_dados() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida CSRF
            if (!CSRF::validateRequest()) {
                Session::flash('error', 'Token de segurança inválido');
                header('Location: index.php?controller=agendamento&action=solicitar_agendamento');
                exit;
            }

            // Valida inputs
            $validator = new Validator($_POST);
            $validator->required('fk_usuario')
                      ->required('fk_profissional')
                      ->required('data')
                      ->required('horario_inicio')
                      ->required('horario_fim')
                      ->integer('fk_usuario')
                      ->integer('fk_profissional')
                      ->date('data');

            if ($validator->fails()) {
                Session::flash('error', $validator->getFirstError());
                header('Location: index.php?controller=agendamento&action=solicitar_agendamento');
                exit;
            }

            $data = $validator->getData();
            $fk_usuario = $data['fk_usuario'];
            $fk_profissional = $data['fk_profissional'];
            $data_agendamento = $data['data'];
            $horario_inicio = $data['horario_inicio'];
            $horario_fim = $data['horario_fim'];

            // Validação lógica do horário
            $inicioParts = explode(':', $horario_inicio);
            $fimParts = explode(':', $horario_fim);

            $inicioMin = (count($inicioParts) === 2) ? ((int)$inicioParts[0] * 60 + (int)$inicioParts[1]) : 0;
            $fimMin = (count($fimParts) === 2) ? ((int)$fimParts[0] * 60 + (int)$fimParts[1]) : 0;

            if ($inicioMin <= 0 || $fimMin <= 0 || $fimMin <= $inicioMin) {
                Session::flash('error', 'O horário do fim precisa ser maior que o horário do início');
                header('Location: index.php?controller=agendamento&action=solicitar_agendamento');
                exit;
            }

            // Busca o ID do paciente vinculado ao ID do usuário
            $id_paciente_real = $this->pacienteModel->getIdByUsuario($fk_usuario);

            if (!$id_paciente_real) {
                Session::flash('error', 'Registro de paciente não localizado para este usuário');
                header('Location: index.php?controller=agendamento&action=solicitar_agendamento');
                exit;
            }

            try {
                // Cria o agendamento
                $resultado = $this->agendamentoModel->criar([
                    'fk_paciente' => $id_paciente_real,
                    'fk_profissional' => $fk_profissional,
                    'data_agendamento' => $data_agendamento,
                    'horario_inicio' => $horario_inicio,
                    'horario_fim' => $horario_fim,
                    'status' => Constants::AGENDAMENTO_PENDENTE
                ]);

                if ($resultado) {
                    Session::flash('success', 'Agendamento solicitado com sucesso! Aguarde a confirmação do profissional.');
                    
                    Logger::audit(Constants::AUDITORIA_ACAO_CRIAR_AGENDAMENTO, [
                        'fk_paciente' => $id_paciente_real,
                        'fk_profissional' => $fk_profissional,
                        'data' => $data_agendamento,
                        'horario_inicio' => $horario_inicio
                    ]);
                    
                    header('Location: index.php?controller=agendamento&action=solicitar_agendamento');
                    exit;
                } else {
                    Session::flash('error', 'Erro ao criar agendamento');
                    header('Location: index.php?controller=agendamento&action=solicitar_agendamento');
                    exit;
                }
            } catch (DatabaseException $e) {
                Logger::error("Erro ao criar agendamento", ['error' => $e->getMessage()]);
                Session::flash('error', 'Erro ao criar agendamento');
                header('Location: index.php?controller=agendamento&action=solicitar_agendamento');
                exit;
            }
        }
    }
}