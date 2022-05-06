<?php
class Login extends Controller {
    public function __construct()
    {
        parent:: __construct();
    	error_log('Login:: construct -> inicio de Login');
    }

    public function render() {
        error_log('Login:: render -> Carga el index de Login');
        $this -> view -> render('login/index');
    }
}