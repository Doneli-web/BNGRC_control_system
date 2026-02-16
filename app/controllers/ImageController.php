<?php

namespace app\controllers;

use app\models\ImageModel;
use flight\Engine;
use Flight;

class ImageController{
    public static function addnewImage($id_objet, $url){
        $ImageModel = new ImageModel(Flight::db());
        $ImageModel->addnewImage($id_objet, $url);
    }

    public static function getById($id_objet){
        $ImageModel = new ImageModel(Flight::db());
        return $ImageModel->getById($id_objet);
    }
}