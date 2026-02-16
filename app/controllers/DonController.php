<?php

namespace app\controllers;

use app\models\DonModel;
use flight\Engine;
use Flight;

class DonController{
    public static function addDon($idArticle, $quantity){
        $DonModel = new DonModel(Flight::db());
        $DonModel->add($idArticle, $quantity);
    }

    public static function getAllDons(){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->findAllWithDetails();
    }

    public static function getDonsByStatut($statut){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getDonsByStatut($statut);
    }

    public static function updateStatut($id, $statut){
        $DonModel = new DonModel(Flight::db());
        $DonModel->updateStatut($id, $statut);
    }

    public static function getStatistiques(){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getStatistiques();
    }

    public static function getById($id){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getById($id);
    }

    public static function getAll(){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getAll();
    }
}