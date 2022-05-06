<?php

require_once 'controllers/Errores.php';

class App {
    public function __construct() {
        $url = isset($_GET['url']) ? $_GET['url']: null;
        $url = rtrim($url. '/');
        $url = explode('/', $url);

        if(empty($url[0])) {
            error_log('APP::construct -> No hay controlador especificado');
            $archivoController = 'controllers/login.php';
            require_once $archivoController;

            $controller = new Login();
            $controller -> loadModel('login');
            $controller -> render();
            return;
        }
        $archivoController = 'controllers/'.$url[0]. '.php';

        if(file_exists($archivoController)) {
            require_once $archivoController;

            $controller = new $url[0];
            $controller -> loadModel($url[0]);

            if(isset($url[1])) {
                if(method_exists($controller, $url[1])) {
                    if(isset($url[2])) {
                        $nparam = count($url) - 2; // parametros del controlador
                        $params = []; // parametros

                        for ($i = 0; $i < $nparam; $i++) {
                            array_push($params, $url[$i] + 2);
                        }
                        $controller -> {$url[1]}($params);
                    } else {
                        // no tiene parametros, se manda a llamar el metodo
                        $controller -> {$url[1]}();
                    }
                } else {
                    // error, no existe el metodo
                    $controller = new Errores();
                    $controller -> render();
                }
            } else {
                // no hay metodo a cargar, metodo por default
                $controller -> render();
            }
        } else {
            // error, no existe el archivo
            $controller = new Errores();
            $controller -> render();
        }
    }
}