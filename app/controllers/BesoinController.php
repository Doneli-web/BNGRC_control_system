<?php

namespace app\controllers;

use app\models\BesoinModel;

use Flight;

class BesoinController{
    public static function addBesoin($idVille, $idArticle, $quantite){
        $besoinModel = new BesoinModel(Flight::db());
        $besoinModel->addBesoin($idVille, $idArticle, $quantite);
    }

    public static function findAll(){
        $besoinModel = new BesoinModel(Flight::db());
        return $besoinModel->findAll();
    }

    public static function findById($id){
        $besoinModel = new BesoinModel(Flight::db());
        return $besoinModel->findById($id);
    }

    public static function findAllBesoinInfo(){
        $besoinModel = new BesoinModel(Flight::db());
        return $besoinModel->findAllBesoinInfo();
    }

    public static function deleteBesoin($id){
        $besoinModel = new BesoinModel(Flight::db());
        $besoinModel->deleteBesoin($id);
    }

    public static function getTotalBesoinPrice(){
        $besoinModel = new BesoinModel(Flight::db());
        return $besoinModel->getTotalPrice();
    }

    public static function getMontantSatisfait(){
        $besoinModel = new BesoinModel(Flight::db());
        return $besoinModel->getMontantSatisfait();
    }
}