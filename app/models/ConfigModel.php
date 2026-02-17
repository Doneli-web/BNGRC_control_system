<?php

namespace app\models;

use Flight;
use PDO;

class ConfigModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getConfig($cle) {
        $stmt = $this->db->prepare("SELECT valeur FROM BNGRC_config WHERE cle = ?");
        $stmt->execute([$cle]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['valeur'] : null;
    }

    public function updateConfig($cle, $valeur) {
        $stmt = $this->db->prepare("UPDATE BNGRC_config SET valeur = ? WHERE cle = ?");
        return $stmt->execute([$valeur, $cle]);
    }

      public function getFraisAchat() {
        $stmt = $this->db->prepare("SELECT valeur FROM BNGRC_config WHERE cle = 'frais_achat'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['valeur'] : 10;
    }

    public function updateFraisAchat($frais) {
        $stmt = $this->db->prepare("UPDATE BNGRC_config SET valeur = ? WHERE cle = 'frais_achat'");
        return $stmt->execute([$frais]);
    }
    

    public function getAllConfig() {
        $stmt = $this->db->query("SELECT * FROM BNGRC_config");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addConfig($cle, $valeur, $description = '') {
        $stmt = $this->db->prepare("INSERT INTO BNGRC_config (cle, valeur, description) VALUES (?, ?, ?)");
        return $stmt->execute([$cle, $valeur, $description]);
    }

    public function deleteConfig($cle) {
        $stmt = $this->db->prepare("DELETE FROM BNGRC_config WHERE cle = ?");
        return $stmt->execute([$cle]);
    }
}