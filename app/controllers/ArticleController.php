<?php

namespace app\controllers;
use app\models\ArticleModel;

use Flight;

class ArticleController{
    public static function findAll(){
        $articleModel = new ArticleModel(Flight::db());
        return $articleModel->getAll();
    }

    public static function findById($id){
        $articleModel = new ArticleModel(Flight::db());
        return $articleModel->getById($id);
    }

    public static function findByType($idType){
        $articleModel = new ArticleModel(Flight::db());
        return $articleModel->getByType($idType);
    }
}