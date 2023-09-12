<?php
class Inicio extends Controller
{

    public $Catalogo;
    public $Remision;

    public function __construct()
    {
       
        
    }

    public function index()
    {

        if (!isset($_SESSION['userdata'])) {
            header("Location: ".base_url."Login");
            exit();
        }

        $data = [
            'titulo'    => 'IE ADMIN | Inicio'
        ];
        
        $this->view('templates/header', $data);
        $this->view('system/inicio/inicioView', $data);
        $this->view('templates/footer', $data);
        
    }
}