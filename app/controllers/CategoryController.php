<?php

namespace app\controllers;

use app\models\CategoryModel;
use flight\Engine;
use Flight;

class CategoryController{
    public static function getAllCategory(){
        $CategoryModel = new CategoryModel(Flight::db());
        $AllCategory = $CategoryModel->getAll();
        return $AllCategory;
    }

   public static function addCategory($name){
        $CategoryModel = new CategoryModel(Flight::db());
        $CategoryModel->createCategory($name);
   }
   
   public static function removeCategory($id){
        $CategoryModel = new CategoryModel(Flight::db());
        $CategoryModel->deleteCategory($id);
   }
}