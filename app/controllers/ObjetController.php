<?php

namespace app\controllers;

use app\models\ObjetModel;
use app\models\ImageModel;
use flight\Engine;
use Flight;

class ObjetController{
    public static function addnewObject($id_category, $name, $description, $prix_estimatif, $id_user){
        $ObjetModel = new ObjetModel(Flight::db());
        return $ObjetModel->addnewObject($id_category, $name, $description, $prix_estimatif, $id_user);
    }

    public static function getById($id){
        $ObjetModel = new ObjetModel(Flight::db());
        return $ObjetModel->getById($id);
    }
    
    public static function getByName($name){
        $ObjetModel = new ObjetModel(Flight::db());
        return $ObjetModel->getByName($name);
    }

    public static function getAllObject(){
        $ObjetModel = new ObjetModel(Flight::db());
        return $ObjetModel->getAllObject();
    }

    public static function getByCategory($id_category){
        $ObjetModel = new ObjetModel(Flight::db());
        return $ObjetModel->getByCategory($id_category);
    }
    
    public static function updateObject($id, $data){
        $ObjetModel = new ObjetModel(Flight::db());
        return $ObjetModel->updateObject($id, $data);
    }

    public static function getUserObjects($id_user){
        $objetModel = new ObjetModel(Flight::db());
        return $objetModel->findUserObjects($id_user);
    }
    
    public static function removeObject($id){
        $ObjetModel = new ObjetModel(Flight::db());
        return $ObjetModel->removeObject($id);
    }

    public static function countObjectPerCategory($id){
        $ObjetModel = new ObjetModel(Flight::db());
        $count = $ObjetModel->countObjectByCategorie($id);
        return $count;
    }
}