<?php

namespace app\controllers;

use app\models\RegionModel;
use flight\Engine;
use Flight;

class RegionController{
    public static function getRegions(){
        $RegionModel = new RegionModel(Flight::db());
        $regions = $RegionModel->findAll();
        return $regions;
    }

    public static function getRegionById($id){
        $RegionModel = new RegionModel(Flight::db());
        $region = $RegionModel->findById($id);
        return $region;
    }

    public static function addRegion($name){
        $RegionModel = new RegionModel(Flight::db());
        $RegionModel->addRegion($name);
    }
}