<?php
    require_once __DIR__. '/../model/User.php';
    class AuthController{
        public function register($data){
            $user = new User();
            
            if($data['password'] !== $data['confirm_password']){
                die("Parolele nu coincid");
            }

            $result = $user->createUser($data['name'], $data['surname'], $data['email'], $data['password']);
            if($result){
                header("Location: ../view/succes_register.php");
            }else{
                die("eroare la inregistrare");
            }
        }

        public function login($data){
            $user = new User();
            $authenticated = $user->authenticate($data['name'], $data['surname'], $data['password']);

            if($authenticated){
                header("Location: ../view/succes_login.php");
            }else{
                die("Nume sau parola incorecte");
            }
        }
    }