<?php

namespace app\models;

use Flight;
use PDO;

class BesoinModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function addBesoin($idVille, $idArticle, $quantite){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_besoin(idVille, idArticle, quantite) VALUES(?, ?, ?)");
        $stmt->execute([$idVille, $idArticle, $quantite]);
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

    public function findAllBesoinInfo(){
        $stmt = $this->db->prepare("SELECT BNGRC_besoin.id, BNGRC_ville.name AS ville_name, BNGRC_typeDon.name AS type_name, BNGRC_article.name AS article_name, BNGRC_article.prix_unitaire, BNGRC_besoin.quantite, BNGRC_besoin.date_de_saisie FROM BNGRC_besoin JOIN BNGRC_ville ON BNGRC_besoin.idVille = BNGRC_ville.id JOIN BNGRC_article ON BNGRC_besoin.idArticle = BNGRC_article.id JOIN BNGRC_typeDon ON BNGRC_article.idType = BNGRC_typeDon.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBesoin($id){
        $stmt = $this->db->prepare("DELETE FROM BNGRC_besoin WHERE id=:id");
        $stmt->execute([
            ":id"=>$id
        ]);
    }

    public function getTotalPrice(){
        $stmt = $this->db->prepare("SELECT SUM(BNGRC_article.prix_unitaire * BNGRC_besoin.quantite) AS total FROM BNGRC_besoin JOIN BNGRC_article ON BNGRC_besoin.idArticle = BNGRC_article.id");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getMontantSatisfait(){
        $sql = "SELECT COALESCE(SUM(disp.quantite_attribuee * a.prix_unitaire), 0) AS total_satisfait
                FROM BNGRC_dispatch disp
                JOIN BNGRC_don don ON disp.idDon = don.id
                JOIN BNGRC_article a ON don.idArticle = a.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($res['total_satisfait']) ? (float)$res['total_satisfait'] : 0.0;
    }
}

