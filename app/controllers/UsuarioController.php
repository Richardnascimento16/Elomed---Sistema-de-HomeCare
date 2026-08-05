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

class UsuarioController {
    private $usuarioModel;
    private $profissionalModel;
    private $pacienteModel;

    public function __construct() {
        Session::start();
        if (!Session::has('usuario_id')) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }

        $this->usuarioModel = new Usuario();
        $this->profissionalModel = new Profissional();
        $this->pacienteModel = new Paciente();
    }
        
    public function index() {
        $usuarios = $this->usuarioModel->all();
        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida CSRF
            if (!CSRF::validateRequest()) {
                Session::flash('error', 'Token de segurança inválido');
                require_once __DIR__ . '/../views/usuarios/cadastrar.php';
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
                require_once __DIR__ . '/../views/usuarios/cadastrar.php';
                return;
            }

            $data = $validator->getData();

            try {
                $resultado = $this->usuarioModel->criar($data);

                if ($resultado) {
                    Logger::audit(Constants::AUDITORIA_ACAO_CRIAR_USUARIO, [
                        'nome' => $data['nome'],
                        'email' => $data['email'],
                        'perfil' => $data['perfil']
                    ]);
                    
                    Session::flash('success', 'Usuário cadastrado com sucesso');
                    header('Location: index.php?controller=usuario&action=index');
                    exit;
                } else {
                    Session::flash('error', 'Erro ao cadastrar usuário');
                    require_once __DIR__ . '/../views/usuarios/cadastrar.php';
                }
            } catch (DatabaseException $e) {
                Logger::error("Erro ao cadastrar usuário", ['error' => $e->getMessage()]);
                Session::flash('error', 'Erro ao cadastrar usuário');
                require_once __DIR__ . '/../views/usuarios/cadastrar.php';
            }
        }
        require_once __DIR__ . '/../views/usuarios/cadastrar.php';
    }

    public function cadastrar_profissional() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida CSRF
            if (!CSRF::validateRequest()) {
                Session::flash('error', 'Token de segurança inválido');
                require_once __DIR__ . '/../views/usuarios/cadastrar_profissional.php';
                return;
            }

            // Valida inputs
            $validator = new Validator($_POST);
            $validator->required('id')
                      ->required('especialidade')
                      ->required('numero_registro')
                      ->integer('id')
                      ->min('especialidade', 3);

            if ($validator->fails()) {
                Session::flash('error', $validator->getFirstError());
                require_once __DIR__ . '/../views/usuarios/cadastrar_profissional.php';
                return;
            }

            $data = $validator->getData();

            try {
                $resultado = $this->profissionalModel->criar([
                    'fk_usuario' => $data['id'],
                    'especialidade' => $data['especialidade'],
                    'numero_registro' => $data['numero_registro']
                ]);

                if ($resultado) {
                    Logger::audit(Constants::AUDITORIA_ACAO_CRIAR_USUARIO, [
                        'tipo' => 'profissional',
                        'fk_usuario' => $data['id'],
                        'especialidade' => $data['especialidade']
                    ]);
                    
                    Session::flash('success', 'Profissional cadastrado com sucesso');
                    header('Location: index.php?controller=usuario&action=index');
                    exit;
                } else {
                    Session::flash('error', 'Erro ao cadastrar profissional');
                    require_once __DIR__ . '/../views/usuarios/cadastrar_profissional.php';
                }
            } catch (DatabaseException $e) {
                Logger::error("Erro ao cadastrar profissional", ['error' => $e->getMessage()]);
                Session::flash('error', 'Erro ao cadastrar profissional');
                require_once __DIR__ . '/../views/usuarios/cadastrar_profissional.php';
            }
        }
        require_once __DIR__ . '/../views/usuarios/cadastrar_profissional.php';
    }

    public function cadastrar_paciente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida CSRF
            if (!CSRF::validateRequest()) {
                Session::flash('error', 'Token de segurança inválido');
                require_once __DIR__ . '/../views/usuarios/cadastrar_paciente.php';
                return;
            }

            // Valida inputs
            $validator = new Validator($_POST);
            $validator->required('id')
                      ->required('tel_emergencia')
                      ->required('data_nascimento')
                      ->integer('id')
                      ->date('data_nascimento');

            if ($validator->fails()) {
                Session::flash('error', $validator->getFirstError());
                require_once __DIR__ . '/../views/usuarios/cadastrar_paciente.php';
                return;
            }

            $data = $validator->getData();

            try {
                $resultado = $this->pacienteModel->criar([
                    'fk_usuario' => $data['id'],
                    'contato_emergencia' => $data['tel_emergencia'],
                    'data_nascimento' => $data['data_nascimento']
                ]);

                if ($resultado) {
                    Logger::audit(Constants::AUDITORIA_ACAO_CRIAR_USUARIO, [
                        'tipo' => 'paciente',
                        'fk_usuario' => $data['id']
                    ]);
                    
                    Session::flash('success', 'Paciente cadastrado com sucesso');
                    header('Location: index.php?controller=usuario&action=index');
                    exit;
                } else {
                    Session::flash('error', 'Erro ao cadastrar paciente');
                    require_once __DIR__ . '/../views/usuarios/cadastrar_paciente.php';
                }
            } catch (DatabaseException $e) {
                Logger::error("Erro ao cadastrar paciente", ['error' => $e->getMessage()]);
                Session::flash('error', 'Erro ao cadastrar paciente');
                require_once __DIR__ . '/../views/usuarios/cadastrar_paciente.php';
            }
        }
        require_once __DIR__ . '/../views/usuarios/cadastrar_paciente.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("ID não fornecido.");

        $usuario = $this->usuarioModel->find($id);
        if (!$usuario) die("Usuário não encontrado.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida CSRF
            if (!CSRF::validateRequest()) {
                Session::flash('error', 'Token de segurança inválido');
                require_once __DIR__ . '/../views/usuarios/editar.php';
                return;
            }

            // Valida inputs
            $validator = new Validator($_POST);
            $validator->required('nome')
                      ->required('email')
                      ->required('perfil')
                      ->email('email')
                      ->min('nome', 3)
                      ->in('perfil', [
                          Constants::PERFIL_ADMIN,
                          Constants::PERFIL_MEDICO,
                          Constants::PERFIL_ENFERMEIRO,
                          Constants::PERFIL_TECNICO_ENFERMAGEM,
                          Constants::PERFIL_PACIENTE
                      ]);

            if ($validator->fails()) {
                Session::flash('error', $validator->getFirstError());
                require_once __DIR__ . '/../views/usuarios/editar.php';
                return;
            }

            $dados = $validator->getData();
            $dados['senha'] = $_POST['senha'] ?? null;

            try {
                $this->usuarioModel->atualizar($id, $dados);
                
                Logger::audit(Constants::AUDITORIA_ACAO_ATUALIZAR_USUARIO, [
                    'id' => $id,
                    'nome' => $dados['nome']
                ]);
                
                Session::flash('success', 'Usuário atualizado com sucesso');
                header('Location: index.php?controller=usuario&action=index');
                exit;
            } catch (DatabaseException $e) {
                Logger::error("Erro ao atualizar usuário", ['error' => $e->getMessage()]);
                Session::flash('error', 'Erro ao atualizar usuário');
                require_once __DIR__ . '/../views/usuarios/editar.php';
            }
        }

        require_once __DIR__ . '/../views/usuarios/editar.php';
    }

    public function excluir(){
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->usuarioModel->desativar($id);
                
                Logger::audit(Constants::AUDITORIA_ACAO_DESATIVAR_USUARIO, [
                    'id' => $id
                ]);
                
                Session::flash('success', 'Usuário desativado com sucesso');
            } catch (DatabaseException $e) {
                Logger::error("Erro ao desativar usuário", ['error' => $e->getMessage()]);
                Session::flash('error', 'Erro ao desativar usuário');
            }
        }

        header('Location: index.php?controller=usuario&action=index');
        exit;
    }
    public function procurar() {
        $termo = $_GET['nome'] ?? '';
        if(empty($termo)){
            require dirname(__DIR__, 2) . '/app/views/usuarios/cadastrar_profissional.php';
        } else {
            $usuarios = $this->usuarioModel->buscarPorNomeProfissional($termo);
            require dirname(__DIR__, 2) . '/app/views/usuarios/cadastrar_profissional.php';
        }
    }

    public function procurar_paciente() {
        $termo = $_GET['nome'] ?? '';
        if(empty($termo)){
            require dirname(__DIR__, 2) . '/app/views/usuarios/cadastrar_paciente.php';
        } else {
            $usuarios = $this->usuarioModel->buscarPorNomePaciente($termo);
            require dirname(__DIR__, 2) . '/app/views/usuarios/cadastrar_paciente.php';
        }
    }

    public function listar() {
        $termo = $_GET['nome'] ?? '';
        $usuarios = $this->usuarioModel->buscarPorNome($termo);
        require dirname(__DIR__, 2) . '/app/views/usuarios/index.php';
    }

    public function listarProfissionaisAtendendo() {
        $id_paciente = Session::get('usuario_id');
        $profissionais = $this->profissionalModel->listarAtivos();
        require dirname(__DIR__, 2) . '/app/views/paciente/solicitar_agendamento.php';
    }
}