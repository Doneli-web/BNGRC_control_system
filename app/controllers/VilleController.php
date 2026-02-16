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
        return $VilleModel->findVilleStatistics();
    }

    public static function getDashboard(){
        $model = new VilleModel(Flight::db());
        return $model->getDashboardData();
    }

    

    public static function getBesoinsByVille($idVille) {
        $VilleModel = new VilleModel(Flight::db());
        return $VilleModel->getBesoinsByVille($idVille);
    }

    public static function getDonsByVille($idVille) {
        $VilleModel = new VilleModel(Flight::db());
        return $VilleModel->getDonsByVille($idVille);
    }

    public static function getDetailVille($idVille) {
        $VilleModel = new VilleModel(Flight::db());
        return $VilleModel->getDetailVille($idVille);
    }

    public static function getTotalVilles() {
        $VilleModel = new VilleModel(Flight::db());
        return $VilleModel->getTotalVIlle();
    }


}