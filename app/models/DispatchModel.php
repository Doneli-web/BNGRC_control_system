<?php
namespace app\models;

use Flight;
use PDO;

class DispatchModel {
    private $db;

    public function __construct($db = null){
        $this->db = $db ?: Flight::db();
    }

    // Suppression du clearAll, simulation incrémentale

    public function getBesoinNonComble(){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_besoin WHERE status != 'comble' ORDER BY date_de_saisie ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonDisponible(){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_don WHERE status != 'utilise' ORDER BY date_de_saisie ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($idDon, $idBesoin, $quantite, $dateDispatch = null){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee, date_dispatch) VALUES (?, ?, ?, ?)");
        $dt = $dateDispatch ?: date('Y-m-d H:i:s');
        $stmt->execute([$idDon, $idBesoin, $quantite, $dt]);
        return $this->db->lastInsertId();
    }
}
