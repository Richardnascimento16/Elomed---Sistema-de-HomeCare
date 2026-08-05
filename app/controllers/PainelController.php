<?php
require_once __DIR__ . '/../config/Constants.php';
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../middleware/Session.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once dirname(__DIR__, 2) . '/app/models/Painel.php';

class PainelController {
    private $model;

    public function __construct() {
        Session::start();
        if (!Session::has('usuario_id')) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }

        $this->model = new Painel();
    }
        
    public function index() {
        $estatisticas = $this->model->getEstatisticaPainel();
        $escalaVisitas = $this->model->getEscalaDoDia();

        require_once __DIR__ . '/../views/painel/index.php';
    }
}