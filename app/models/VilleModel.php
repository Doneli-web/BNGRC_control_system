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

   public function findAll() {
        $stmt = $this->db->query("
            SELECT v.*, r.name as region_name 
            FROM BNGRC_ville v
            LEFT JOIN BNGRC_region r ON v.idRegion = r.id
            ORDER BY v.name
        ");
        $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter le nombre de besoins pour chaque ville
        foreach($villes as $key => $ville) {
            $stmt2 = $this->db->prepare("SELECT COUNT(*) as total FROM BNGRC_besoin WHERE idVille = ?");
            $stmt2->execute([$ville['id']]);
            $total = $stmt2->fetch(PDO::FETCH_ASSOC);
            $villes[$key]['total_besoins'] = $total['total'];
            
            // Calculer le montant total des besoins
            $stmt3 = $this->db->prepare("
                SELECT COALESCE(SUM(b.quantite * a.prix_unitaire), 0) as montant
                FROM BNGRC_besoin b
                JOIN BNGRC_article a ON b.idArticle = a.id
                WHERE b.idVille = ?
            ");
            $stmt3->execute([$ville['id']]);
            $montant = $stmt3->fetch(PDO::FETCH_ASSOC);
            $villes[$key]['montant_total'] = $montant['montant'];
        }
        
        return $villes;
    }

    public function findById($id){
        $stmt = $this->db->prepare(" SELECT v.*, r.name as region_name FROM BNGRC_ville v LEFT JOIN BNGRC_region r ON v.idRegion = r.id WHERE v.id = :id");
        $stmt->execute([
            ":id"=>$id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findVilleStatistics(){
        $stmt = $this->db->prepare("
            SELECT 
                v.id AS ville_id,
                v.name AS ville_nom,
                COUNT(b.id) AS total_besoins,
                COALESCE(SUM(b.quantite * a.prix_unitaire), 0) AS montant_total,
                COALESCE((
                    SELECT SUM(disp.quantite_attribuee * a2.prix_unitaire)
                    FROM BNGRC_dispatch disp
                    JOIN BNGRC_besoin b2 ON disp.idBesoin = b2.id
                    JOIN BNGRC_article a2 ON b2.idArticle = a2.id
                    WHERE b2.idVille = v.id
                ), 0) AS montant_recu
            FROM BNGRC_ville v
            LEFT JOIN BNGRC_besoin b ON v.id = b.idVille
            LEFT JOIN BNGRC_article a ON b.idArticle = a.id
            GROUP BY v.id, v.name
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        
        $stats = [];
        foreach($results as $row) {
            $stats[$row['ville_id']] = $row;
        }
        
        return $stats; 
    }

    public function getDashboardData(){
        $sql = "
        SELECT 
            v.id AS ville_id,
            v.name AS ville,
            a.name AS article,
            a.prix_unitaire,
            b.quantite,
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

