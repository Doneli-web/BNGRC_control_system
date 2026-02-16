<?php

namespace app\models;

use Flight;
use PDO;

class AchatModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function add($idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total) {
        $stmt = $this->db->prepare("INSERT INTO BNGRC_achat (idDon, idBesoin, montant_utilise, frais_pourcentage, frais_montant, montant_total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM BNGRC_achat ORDER BY date_achat DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_achat WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAchatsByDon($idDon) {
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_achat WHERE idDon = ?");
        $stmt->execute([$idDon]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAchatsByBesoin($idBesoin) {
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_achat WHERE idBesoin = ?");
        $stmt->execute([$idBesoin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAchatsParVille($idVille) {
        $stmt = $this->db->prepare("
            SELECT SUM(a.montant_total) as total
            FROM BNGRC_achat a
            JOIN BNGRC_besoin b ON a.idBesoin = b.id
            WHERE b.idVille = ?
        ");
        $stmt->execute([$idVille]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getAchatsFiltresParVille($idVille) {
        $stmt = $this->db->prepare("
            SELECT a.*, b.idVille, b.quantite as besoin_quantite
            FROM BNGRC_achat a
            JOIN BNGRC_besoin b ON a.idBesoin = b.id
            WHERE b.idVille = ?
            ORDER BY a.date_achat DESC
        ");
        $stmt->execute([$idVille]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonsArgentRestants() {
        $stmt = $this->db->query("
            SELECT d.*, a.name as article_nom, a.prix_unitaire
            FROM BNGRC_don d
            JOIN BNGRC_article a ON d.idArticle = a.id
            WHERE a.idType = 3 AND d.quantite > 0
            ORDER BY d.date_de_saisie ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinsRestantsNatureMateriaux() {
        $stmt = $this->db->query("
            SELECT b.*, a.name as article_nom, a.prix_unitaire, v.name as ville_nom
            FROM BNGRC_besoin b
            JOIN BNGRC_article a ON b.idArticle = a.id
            JOIN BNGRC_ville v ON b.idVille = v.id
            WHERE a.idType IN (1, 2) AND b.quantite > 0
            ORDER BY b.date_de_saisie ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function delete($id){
        $stmt = $this->db->prepare("DELETE FROM BNGRC_achat WHERE id = ?");
        $stmt->execute([$id]);
    }
}
