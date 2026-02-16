<?php

namespace app\models;

use Flight;
use PDO;

class VilleModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function addVille($name){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_ville(name) VALUES(?)");
        $stmt->execute([$name]);
    }

    public function findAll(){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_ville");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id){
        $stmt = $this->db->prepare("SELECT id, name FROM BNGRC_ville WHERE id=:id");
        $stmt->execute([
            ":id"=>$id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findVilleStatistics(){
        $stmt = $this->db->prepare("SELECT BNGRC_ville.name AS ville_name, COUNT(BNGRC_besoin.id) AS total_besoins FROM BNGRC_ville LEFT JOIN BNGRC_besoin ON BNGRC_ville.id = BNGRC_besoin.idVille GROUP BY BNGRC_ville.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDashboardData(){
        $sql = "
        SELECT 
            v.id AS ville_id,
            v.name AS ville,
            a.name AS article,
            a.prix_unitaire,
            SUM(b.quantite) AS quantite,
            COALESCE(SUM(disp.quantite_attribuee), 0) AS attribue
        FROM BNGRC_ville v
        LEFT JOIN BNGRC_besoin b ON b.idVille = v.id
        LEFT JOIN BNGRC_article a ON a.id = b.idArticle
        LEFT JOIN BNGRC_dispatch disp ON disp.idBesoin = b.id
        GROUP BY v.id, a.id
        ORDER BY v.name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  

    public function getBesoinsByVille($idVille) {
        $stmt = $this->db->prepare("
            SELECT b.*, a.name as article_nom, a.prix_unitaire
            FROM BNGRC_besoin b
            JOIN BNGRC_article a ON b.idArticle = a.id
            WHERE b.idVille = ?
            ORDER BY b.date_de_saisie DESC
        ");
        $stmt->execute([$idVille]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonsByVille($idVille) {
        $stmt = $this->db->prepare("
            SELECT d.*, a.name as article_nom, disp.quantite_attribuee as quantite, disp.date_dispatch
            FROM BNGRC_dispatch disp
            JOIN BNGRC_don d ON disp.idDon = d.id
            JOIN BNGRC_article a ON d.idArticle = a.id
            JOIN BNGRC_besoin b ON disp.idBesoin = b.id
            WHERE b.idVille = ?
            ORDER BY disp.date_dispatch DESC
        ");
        $stmt->execute([$idVille]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalVIlle(){
        $stmt = $this->db->query("SELECT COUNT(*) FROM BNGRC_ville");
        return $stmt->fetchColumn();
    }

    public function getDetailVille($idVille) {
       
        $ville = $this->findById($idVille);
        if(!$ville) return null;
        
        
        $besoins = $this->getBesoinsByVille($idVille);
        
        
        $dons = $this->getDonsByVille($idVille);
        
        return [
            'ville' => $ville,
            'besoins' => $besoins,
            'dons' => $dons
        ];
    }

}

