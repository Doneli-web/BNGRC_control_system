<?php

namespace app\controllers;

use app\models\ConfigModel;
use Flight;

class ConfigController {

    public static function getConfig($cle) {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->getConfig($cle);
    }

    public static function updateConfig($cle, $valeur) {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->updateConfig($cle, $valeur);
    }

    public static function getFraisAchat() {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->getFraisAchat();
    }

    public static function updateFraisAchat($frais) {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->updateFraisAchat($frais);
    }

    public static function getAllConfig() {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->getAllConfig();
    }

    public static function addConfig($cle, $valeur, $description = '') {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->addConfig($cle, $valeur, $description);
    }

    public static function deleteConfig($cle) {
        $ConfigModel = new ConfigModel(Flight::db());
        return $ConfigModel->deleteConfig($cle);
    }
}