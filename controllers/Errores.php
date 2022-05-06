<?php
class Errores extends Controller {
    public function __construct() {
        parent:: __construct();
        error_log('Errores:: construct -> inicio de Errores');
    }

    public function render() {
        error_log('Errores:: render -> Carga el index de Errores');
        $this -> view -> render('errores/index');
    }
}