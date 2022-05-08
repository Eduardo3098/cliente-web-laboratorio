<?php
class LoginModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function login($username, $password) {
        // insertar datos en la BD
        error_log("login: inicio");
        try{
            $query = $this -> prepare('SELECT * FROM users WHERE username = :username');
            $query -> execute(['username' => $username]);

            if($query -> rowCount() == 1) {
                $item = $query -> fetch(PDO::FETCH_ASSOC);

                $user = new UserModel();
                $user -> from($item);
                error_log('login: user id '.$user->getId());

                if(password_verify($password, $user -> getPassword())) {
                    error_log('LoginModel:: login -> success');
                    return $user;
                }else{
                    return NULL;
                }
            }
        }catch(PDOException $e){
            error_log("LoginModel:: login ->exception " .$e);
            return NULL;
        }
    }
}