<?php

namespace app\controllers;

use app\models\VilleModel;
use Flight;

class VilleController{
    public static function getVilles(){
        $VilleModel = new VilleModel(Flight::db());
        $villes = $VilleModel->findAll();
        return $villes;
    }

    public static function getVillesById($id){
        $VilleModel = new VilleModel(Flight::db());
        $ville = $VilleModel->findById($id);
        return $ville;
    }

    public static function addRegion($name){
        $VilleModel = new VilleModel(Flight::db());
        $VilleModel->addVille($name);
    }

    public static function getVilleStats(){
        $VilleModel = new VilleModel(Flight::db());
        $VilleModel->findVilleStatistics();
    }
}