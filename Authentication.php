<?php

namespace Authentication;

use Database;

class Authentication {

    protected $database = null;

    public function __construct()
    {
        $this->database = new Database;
    }

    public function login($username, $password)
    {
        if($this->validation(['username' => $username, 'password' => $password])){
            
            session_regenerate_id();
            $_SESSION['user_logged'] = true;
            $_SESSION['user_username'] = $username;

        }else{
            echo "As credenciais introduzidas estão incorretas, verifique novamente pff";
        }
    }

    protected function validation($data)
    {
        $validated = false;

        if(isset($data['username'])){
            if(filter_var($data['username'], FILTER_VALIDATE_EMAIL)) {
                $field = "email";
            }else{
                $field = "username";
            }

            $user = $this->database->table('tbl_users')->select()->where('username', 'LIKE', 'eduardo.bessa');

            if($user->count() > 0) {
                $user = $user->first();

                if(password_verify($data['password'], $user->password)) {
                    $validated = true;
                }
            }
        }

        return $validated;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
    }
}

$auth = new Authentication;
$auth->login('eduardo.bessa', 'Vush@w123');