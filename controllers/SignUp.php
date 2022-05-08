<?php

require_once 'models/UserModel.php';
class SignUp extends SessionController {
    function __construct() {
        parent::__construct();
        error_log('SignUp:: construct -> inicio de SignUp');
    }

    function render() {
        error_log('SignUp:: render -> Carga el SignUp');
        $this -> view -> errorMessage = '';
        $this -> view -> render('login/signup');
    }

    function newUser() {
        if($this -> existPOST(['username', 'password'])) {

            $username = $this -> getPost('username');
            $password = $this -> getPost('password');

            //validate data
            if($username == '' || empty($username) || $password == '' || empty($password)){
                // error al validar datos
                $this -> redirect('signup', ['error' => ErrorMessages::ERROR_SIGNUP_NEWUSER_EMPTY]);
                return;
            }

            $user = new UserModel();
            $user -> setUsername($username);
            $user -> setPassword($password);
            $user -> setRole("user");

            if($user -> exists($username)) {
                $this -> redirect('signup', ['error' => ErrorMessages::ERROR_SIGNUP_NEWUSER_EXISTS]);
                //return;
            }else if($user -> save()) {
                $this -> redirect('', ['success' => SuccessMessages::SUCCESS_SIGNUP_NEWUSER]);
            }else {
                $this -> redirect('signup', ['error' => ErrorMessages::ERROR_SIGNUP_NEWUSER]);
            }
        }else {
            $this -> redirect('signup', ['error' => ErrorMessages::ERROR_SIGNUP_NEWUSER_EXISTS]);
        }
    }
}