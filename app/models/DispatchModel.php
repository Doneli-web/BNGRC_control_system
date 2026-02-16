<?php
namespace app\models;

use Flight;
use PDO;

class DispatchModel {
    private $db;

    public function __construct($db = null){
        $this->db = $db ?: Flight::db();
    }

    public function clearAll(){
        $this->db->exec("DELETE FROM BNGRC_dispatch");
    }

    public function insert($idDon, $idBesoin, $quantite, $dateDispatch = null){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee, date_dispatch) VALUES (?, ?, ?, ?)");
        $dt = $dateDispatch ?: date('Y-m-d H:i:s');
        $stmt->execute([$idDon, $idBesoin, $quantite, $dt]);
        return $this->db->lastInsertId();
    }
}
