<?php

namespace app\controllers;

use app\models\ResetModel;
use Flight;

class ResetController {
    
   
    public static function reset() {
        $ResetModel = new ResetModel(Flight::db());
        $result = $ResetModel->resetAll();
        
        Flight::json($result);
    }
    
  
   
}