<?php

namespace app\controllers;

use app\models\ConfigModel;
use Flight;

class ConfigController {

    public static function getFraisAchat() {
        $ConfigModel = new ConfigModel(Flight::db());
        $frais = $ConfigModel->getFraisAchat();
        return $frais;
    }

    public static function updateFraisAchat($frais) {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->updateFraisAchat($frais);
    }

    public static function getConfig($cle) {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->getConfig($cle);
    }

    public static function getAllConfig() {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->getAllConfig();
    }
}