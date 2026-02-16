<?php

namespace app\controllers;

use app\models\UserModel;
use flight\Engine;
use Flight;

class UserController{
    public static function addUser($userName, $email, $password){
        $UserModel = new UserModel(Flight::db());
        $UserModel->addUser($userName, $email, $password);
    }

    public static function removeUser($id){
        $UserModel = new UserModel(Flight::db());
        $UserModel->removeUser($id);
    }

    public static function getByEmail($email){
        $UserModel = new UserModel(Flight::db());
        $user = $UserModel->getByEmail($email);
        return $user;
    }

    public static function verifyUser($email, $password){
        $UserModel = new UserModel(Flight::db());
        $user = $UserModel->getByEmail($email);
        if(isset($user[0])){
            $user = $user[0];
            if($user != null && $user["password"] != null){
                return password_verify($password, $user["password"]);
            }
        }
        return false;
    }

    public static function getAll(){
        $UserModel = new UserModel(Flight::db());
        $users = $UserModel->getAll();
        return $users;
    }

    public static function getId($email){
        $UserModel = new UserModel(Flight::db());
        $user = $UserModel->getIdByEmail($email);
        return $user;
    }
}