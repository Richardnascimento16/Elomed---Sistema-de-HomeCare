<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../models/Usuario.php';

class CadastroController {
    private $model;

    public function __construct() {
        Session::start();
        $this->model = new Usuario();
    }

    public function index() {
        if (Session::has('usuario_id')) {
            header('Location: index.php?controller=painel&action=index');
            exit;
        }
        require_once __DIR__ . '/../views/cadastro/index.php';
    }

    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida CSRF
            if (!CSRF::validateRequest()) {
                Session::flash('error', 'Token de segurança inválido');
                require_once __DIR__ . '/../views/cadastro/index.php';
                return;
            }

            // Valida inputs
            $validator = new Validator($_POST);
            $validator->required('nome')
                      ->required('email')
                      ->required('senha')
                      ->required('perfil')
                      ->email('email')
                      ->min('nome', 3)
                      ->min('senha', 8)
                      ->password('senha')
                      ->in('perfil', [
                          Constants::PERFIL_ADMIN,
                          Constants::PERFIL_MEDICO,
                          Constants::PERFIL_ENFERMEIRO,
                          Constants::PERFIL_TECNICO_ENFERMAGEM,
                          Constants::PERFIL_PACIENTE
                      ]);

            if ($validator->fails()) {
                Session::flash('error', $validator->getFirstError());
                require_once __DIR__ . '/../views/cadastro/index.php';
                return;
            }

            $data = $validator->getData();

            try {
                $resultado = $this->model->criar($data);

                if ($resultado) {
                    Session::set('usuario_perfil', $data['perfil']);
                    
                    // Log de auditoria
                    Logger::audit(Constants::AUDITORIA_ACAO_CRIAR_USUARIO, [
                        'nome' => $data['nome'],
                        'email' => $data['email'],
                        'perfil' => $data['perfil']
                    ]);

                    $rotas = [
                        Constants::PERFIL_ADMIN => 'painel',
                        Constants::PERFIL_MEDICO => 'Medico',
                        Constants::PERFIL_ENFERMEIRO => 'Enfermeiro',
                        Constants::PERFIL_TECNICO_ENFERMAGEM => 'TecnicoEnfermagem',
                        Constants::PERFIL_PACIENTE => 'paciente'
                    ];

                    $controller = $rotas[$data['perfil']] ?? 'paciente';
                    header("Location: index.php?controller=$controller&action=index");
                    exit;
                } else {
                    Session::flash('error', 'Erro ao cadastrar usuário');
                    require_once __DIR__ . '/../views/cadastro/index.php';
                }
            } catch (DatabaseException $e) {
                Logger::error("Erro ao cadastrar usuário", ['error' => $e->getMessage()]);
                Session::flash('error', 'Erro ao cadastrar usuário');
                require_once __DIR__ . '/../views/cadastro/index.php';
            }
        }
        require_once __DIR__ . '/../views/cadastro/index.php';
    }
}