<?php

class UserModel extends Model implements IModel {
    private $id;
    private $username;
    private $password;
    private $role;
    private $budget;
    private $photo;
    private $name;

    public function __construct() {
        parent::__construct();
        $this -> username = '';
        $this -> password = '';
        $this -> role = '';
        $this -> budget = 0.0;
        $this -> photo = '';
        $this -> name = '';
    }

    public function save(){
        try {
            $query = $this -> prepare('INSERT INTO users (username, password, role, budget, photo, name) 
                                             VALUES (:username, :password, :role, :budget, :photo, :name )');
            $query -> execute([
                'username'  => $this -> username,
                'password'  => $this -> password,
                'role'      => $this -> role,
                'budget'    => $this -> budget,
                'photo'     => $this -> photo,
                'name'      => $this -> name
            ]);
            return true;
        } catch(PDOException $e) {
            error_log('USERMODEL::save -> PDOException '. $e);
            return false;
        }
    }

    public function getAll() {
        $items = [];
        try {
            $query = $this-> query('SELECT * FROM users');

            while ($p = $query -> fetch(PDO:: FETCH_ASSOC)) {
                $item = new UserModel();
                $item -> setId($p['id']);
                $item -> setUsername($p['username']);
                $item -> setPassword($p['password'], false);
                $item -> setRole($p['role']);
                $item -> setBudget($p['budget']);
                $item -> setPhoto($p['photo']);
                $item -> setName($p['name']);
            }
        } catch(PDOException $e) {
            error_log('USERMODEL::getAll -> PDOException '. $e);
            return false;
        }
    }

    public function get($id) {
        // TODO: Implement get() method.
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }

    public function update() {
        // TODO: Implement update() method.
    }

    public function from($array) {
        // TODO: Implement from() method.
    }

    public function setId($id)         { $this -> id = $id; }
    public function setRole($role)     { $this -> role = $role; }
    public function setBudget($budget) { $this -> budget = $budget; }
    public function setPhoto($photo)   { $this -> photo = $photo; }
    public function setName($name)     { $this -> name = $name; }

    public function getId()            { return $this -> id; }
    public function getPassword()      { return $this -> password; }
    public function getRole()          { return $this -> role; }
    public function getBudget()        { return $this -> budget; }
    public function getPhoto()         { return $this -> photo; }
    public function getName()          { return $this -> name;}
}