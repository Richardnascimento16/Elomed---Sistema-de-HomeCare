<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../models/Usuario.php';

class LoginController {
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
        require_once __DIR__ . '/../views/login/index.php';
    }

    public function logar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        // Valida CSRF
        if (!CSRF::validateRequest()) {
            Session::flash('error', 'Token de segurança inválido');
            require_once __DIR__ . '/../views/login/index.php';
            return;
        }

        // Valida inputs
        $validator = new Validator($_POST);
        $validator->required('email')
                  ->required('senha')
                  ->required('perfil')
                  ->email('email');

        if ($validator->fails()) {
            Session::flash('error', $validator->getFirstError());
            require_once __DIR__ . '/../views/login/index.php';
            return;
        }

        $data = $validator->getData();
        $email = $data['email'];
        $senha = $data['senha'];
        $perfilForm = $data['perfil'];

        $usuario = $this->model->verificarLogin($email, $senha, $perfilForm);

        if ($usuario) {
            Session::set('usuario_id', $usuario['id']);
            Session::set('usuario_nome', $usuario['nome']);
            Session::set('usuario_perfil', $usuario['perfil']);

            // Log de auditoria
            Logger::audit(Constants::AUDITORIA_ACAO_LOGIN, [
                'email' => $email,
                'perfil' => $perfilForm
            ]);

            $rotas = [
                Constants::PERFIL_ADMIN => 'painel',
                Constants::PERFIL_MEDICO => 'Medico',
                Constants::PERFIL_ENFERMEIRO => 'Enfermeiro',
                Constants::PERFIL_TECNICO_ENFERMAGEM => 'TecnicoEnfermagem',
                Constants::PERFIL_PACIENTE => 'paciente'
            ];

            $controller = $rotas[$usuario['perfil']] ?? 'paciente';
            header("Location: index.php?controller=$controller&action=index");
            exit;
        
        } else {
            Session::flash('error', 'Dados de acesso inválidos');
            require_once __DIR__ . '/../views/login/index.php';
        }
    }

    public function sair() {
        // Log de auditoria
        Logger::audit(Constants::AUDITORIA_ACAO_LOGOUT, [
            'user_id' => Session::get('usuario_id'),
            'user_nome' => Session::get('usuario_nome')
        ]);

        Session::destroy();
        header('Location: index.php?controller=login&action=index');
        exit;
    }
}