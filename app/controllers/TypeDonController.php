<?php

namespace app\controllers;

use app\models\TypeDonModel;

use Flight;

class TypeDonController{
    public static function findAll(){
        $typeDonModel = new TypeDonModel(Flight::db());
        return $typeDonModel->getAll();
    }

    public static function findById($id){
        $typeDonModel = new TypeDonModel(Flight::db());
        return $typeDonModel->getById($id);
    }
}