<?php

namespace app\controllers;

use app\models\AdminModel;
use flight\Engine;
use Flight;

class AdminController{
    public static function verificationAdmin(String $email, $password){
        $AdminModel = new AdminModel(Flight::db());
        $admin = $AdminModel->isAdmin($email);
        if(isset($admin[0])){
            $admin = $admin[0];
            if($admin != null && $admin["password"] != null){
                return password_verify($password, $admin["password"]);
            }
        }
        return false;
    }

    public static function getByEmail($email){
        $AdminModel = new AdminModel(Flight::db());
        $admin = $AdminModel->getByEmail($email);
        return $admin;
    }

    public static function getId($email){
        $AdminModel = new AdminModel(Flight::db());
        $admin = $AdminModel->getIdByEmail($email);
        return $admin;
    }
}