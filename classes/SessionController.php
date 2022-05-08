<?php

require_once 'classes/Session.php';
require_once 'models/UserModel.php';
class SessionController extends Controller {
    private $userSession;
    private $username;
    private $userid;
    private $session;
    private $sites;
    private $user;

    function __construct() {
        parent::__construct();
        $this->init();
    }

    private function init() {
        //se crea nueva sesión
        $this->session = new Session();
        //se carga el archivo json con la configuración de acceso
        $json = $this -> getJSONFileConfig();
        // se asignan los sitios
        $this -> sites = $json['sites'];
        // se asignan los sitios por default, los que cualquier rol tiene acceso
        $this -> defaultSites = $json['default-sites'];
        // inicia el flujo de validación para determinar
        // el tipo de rol y permismos
        $this -> validateSession();
    }

    private function getJSONFileConfig() {
        $string = file_get_contents("config/access.json");
        return json_decode($string, true);
    }

    function validateSession() {
        error_log('SessionController::validateSession()');
        //Si existe la sesión
        if($this -> existsSession()) {
            $role = $this -> getUserSessionData() -> getRole();
            error_log("sessionController::validateSession(): username:" . $this -> user -> getUsername() . " - role: " . $this -> user -> getRole());
            if($this -> isPublic()) {
                $this -> redirectDefaultSiteByRole($role);
                error_log("SessionController::validateSession() => sitio público, redirige al main de cada rol");
            }else{
                if($this -> isAuthorized($role)) {
                    error_log("SessionController::validateSession() => autorizado, lo deja pasar");
                    //si el usuario está en una página de acuerdo
                    // a sus permisos termina el flujo
                }else {
                    error_log("SessionController::validateSession() => no autorizado, redirige al main de cada rol");
                    // si el usuario no tiene permiso para estar en
                    // esa página lo redirije a la página de inicio
                    $this -> redirectDefaultSiteByRole($role);
                }
            }
        }else {
            //No existe ninguna sesión
            //se valida si el acceso es público o no
            if($this -> isPublic()) {
                error_log('SessionController::validateSession() public page');
                //la pagina es publica
                //no pasa nada
            }else {
                //la página no es pública
                //redirect al login
                error_log('SessionController::validateSession() redirect al login');
                header('location: '. constant('URL') . '');
            }
        }
    }

    function existsSession() {
        if(!$this -> session -> exists()) return false;
        if($this -> session -> getCurrentUser() == NULL) return false;

        $userid = $this -> session -> getCurrentUser();

        if($userid) return true;
        return false;
    }

    function getUserSessionData() {
        $id = $this -> session -> getCurrentUser();
        $this -> user = new UserModel();
        $this -> user -> get($id);
        error_log("sessionController::getUserSessionData(): " . $this -> user -> getUsername());
        return $this -> user;
    }

    public function initialize($user) {
        error_log("sessionController::initialize(): user: " . $user -> getUsername());
        $this -> session->setCurrentUser($user -> getId());
        $this -> authorizeAccess($user -> getRole());
    }

    private function isPublic() {
        $currentURL = $this -> getCurrentPage();
        $currentURL = preg_replace( "/\?.*/", "", $currentURL); //omitir get info
        error_log("sessionController::isPublic(): currentURL => " . $currentURL);

        foreach ($this -> sites as $site) {
            if($currentURL === $site['site'] && $site['access'] === 'public') {
                return true;
            }
        }
        return false;
    }

    private function redirectDefaultSiteByRole($role) {
        $url = '';
        for($i = 0; $i < sizeof($this -> sites); $i++) {
            if($this -> sites[$i]['role'] === $role) {
                $url = $this -> sites[$i]['site'];
                break;
            }
        }
        header('location: '.$url);
    }

    private function isAuthorized($role) {
        $currentURL = $this -> getCurrentPage();
        $currentURL = preg_replace( "/\?.*/", "", $currentURL); //omitir get info

        foreach ($this -> sites as $site) {
            if($currentURL === $site['site'] && $site['role'] === $role) {
                return true;
            }
        }
        return false;
    }

    private function getCurrentPage() {
        $actual_link = trim("$_SERVER[REQUEST_URI]");
        $url = explode('/', $actual_link);
        if($url[count($url)-1] === "getExpensesJSON" || $url[count($url)-1] === "create" || $url[count($url)-1] === "getHistoryJSON" ||
            $url[count($url)-1] === "newExpense" || $url[count($url)-1] === "createCategory") {
            $current = $url[count($url)-2];
        } else {
            $current = $url[count($url)-1];
        }
        error_log("sessionController::getCurrentPage(): actualLink =>" . $actual_link . ", url => " . $current);
        return $current;
    }

    function authorizeAccess($role) {
        error_log("sessionController::authorizeAccess(): role: $role");
        switch($role) {
            case 'user':
                $this -> redirect($this->defaultSites['user']);
                break;
            case 'admin':
                $this -> redirect($this->defaultSites['admin']);
                break;
        }
    }

    function logout() {
        $this->session->closeSession();
    }
}