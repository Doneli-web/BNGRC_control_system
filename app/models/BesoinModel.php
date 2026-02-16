<?php

namespace app\models;

use Flight;
use PDO;

class BesoinModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function addBesoin($idVille, $idArticle, $quantite, $date_de_saisie){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_besoin(idVille, idArticle, quantite, date_de_saisie) VALUES(?, ?, ?, ?)");
        $stmt->execute([$idVille, $idArticle, $quantite, $date_de_saisie]);
    }

    public function findAll(){
        $stmt = $this->db->prepare("SELECT * name FROM BNGRC_besoin");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id){
        $stmt = $this->db->prepare("SELECT id, name FROM BNGRC_besoin WHERE id=:id");
        $stmt->execute([
            ":id"=>$id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBesoin($id){
        $stmt = $this->db->prepare("DELETE FROM BNGRC_besoin WHERE id=:id");
        $stmt->execute([
            ":id"=>$id
        ]);
    }
}

