<?php

namespace app\models;

use Flight;
use PDO;

class DonModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function add($idArticle, $quantity){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_don(idArticle, quantite) VALUES (?,?)");
        $stmt->execute([$idArticle, $quantity]);
    }

    public function getAll(){
        $stmt = $this->db->query("SELECT * FROM BNGRC_don");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_don WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllWithDetails(){
        $stmt = $this->db->query("SELECT d.*, d.idArticle, a.name as article_nom, a.prix_unitaire FROM BNGRC_don d JOIN BNGRC_article a ON d.idArticle = a.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonsByStatut($statut){
        $stmt = $this->db->prepare("SELECT d.id, a.nom AS article, d.quantite FROM BNGRC_don d JOIN BNGRC_article a ON d.idArticle = a.id WHERE d.statut = ?");
        $stmt->execute([$statut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatut($id, $statut){
        $stmt = $this->db->prepare("UPDATE BNGRC_don SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }

    public function getStatistiques(){
        $stmt = $this->db->query("SELECT a.name AS article, SUM(d.quantite) AS total_quantite FROM BNGRC_don d JOIN BNGRC_article a ON d.idArticle = a.id GROUP BY a.name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id){
        
        $check = $this->db->prepare("SELECT id FROM BNGRC_don WHERE id = ?");
        $check->execute([$id]);
        
        if($check->fetch()) {
            $stmt = $this->db->prepare("DELETE FROM BNGRC_don WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        }
        return false;
    }

    
}