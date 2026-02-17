<?php

namespace app\controllers;

use app\models\AchatModel;
use app\models\ConfigModel;
use app\controllers\VilleController;
use app\models\DonModel;
use app\models\BesoinModel;
use app\models\ArticleModel;
use Flight;
use PDO;
class AchatController {

    public static function addAchat($idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total) {
        $AchatModel = new AchatModel(Flight::db());
        $AchatModel->add($idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total);
    }

    public static function getAllAchats() {
        $db = Flight::db();
        $stmt = $db->query("
            SELECT 
                a.*, 
                v.name as ville_nom,
                d.idArticle,
                b.idVille
            FROM BNGRC_achat a
            JOIN BNGRC_besoin b ON a.idBesoin = b.id
            JOIN BNGRC_ville v ON b.idVille = v.id
            LEFT JOIN BNGRC_don d ON a.idDon = d.id
            ORDER BY a.date_achat DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAchatById($id) {
        $AchatModel = new AchatModel(Flight::db());
        return $AchatModel->getById($id);
    }

    public static function getAchatsByDon($idDon) {
        $AchatModel = new AchatModel(Flight::db());
        return $AchatModel->findAchatsByDon($idDon);
    }

    public static function getAchatsByBesoin($idBesoin) {
        $AchatModel = new AchatModel(Flight::db());
        return $AchatModel->findAchatsByBesoin($idBesoin);
    }

    public static function getTotalAchatsParVille($idVille) {
        $AchatModel = new AchatModel(Flight::db());
        return $AchatModel->getTotalAchatsParVille($idVille);
    }

    public static function getAchatsFiltresParVille($idVille) {
        $AchatModel = new AchatModel(Flight::db());
        return $AchatModel->getAchatsFiltresParVille($idVille);
    }

    public static function getDonsArgentRestants() {
        $AchatModel = new AchatModel(Flight::db());
        return $AchatModel->getDonsArgentRestants();
    }

    public static function getBesoinsRestantsNatureMateriaux() {
        $AchatModel = new AchatModel(Flight::db());
        return $AchatModel->getBesoinsRestantsNatureMateriaux();
    }

    public static function calculerAchat($idDon, $idBesoin, $montant) {
        $DonModel = new DonModel(Flight::db());
        $ConfigModel = new ConfigModel(Flight::db());
        $db = Flight::db();

        // Récupérer le don
        $don = $DonModel->getById($idDon);
        if (!$don) {
            error_log("Don non trouvé: " . $idDon);
            return null;
        }

        // Récupérer le besoin
        $stmt = $db->prepare("
            SELECT b.*, a.name as article_nom, a.prix_unitaire, v.name as ville_nom
            FROM BNGRC_besoin b
            JOIN BNGRC_article a ON b.idArticle = a.id
            JOIN BNGRC_ville v ON b.idVille = v.id
            WHERE b.id = ?
        ");
        $stmt->execute([$idBesoin]);
        $besoin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$besoin) {
            error_log("Besoin non trouvé: " . $idBesoin);
            return null;
        }

        // Vérifier que c'est un don en argent (idArticle = 5)
        if ($don['idArticle'] != 5) {
            error_log("Ce n'est pas un don en argent: " . $don['idArticle']);
            return null;
        }

        // Vérifier le montant
        if ($montant > $don['quantite']) {
            error_log("Montant insuffisant: " . $montant . " > " . $don['quantite']);
            return null;
        }

        $prix_unitaire = $besoin['prix_unitaire'];
        $quantite_achetable = floor($montant / $prix_unitaire);

        if ($quantite_achetable <= 0) {
            error_log("Quantité achetable nulle: " . $quantite_achetable);
            return null;
        }

        if ($quantite_achetable > $besoin['quantite']) {
            $quantite_achetable = $besoin['quantite'];
            $montant = $quantite_achetable * $prix_unitaire;
        }

        $frais_pourcentage = $ConfigModel->getFraisAchat();
        $frais_montant = $montant * $frais_pourcentage / 100;
        $montant_total = $montant + $frais_montant;

        return [
            'idDon' => $idDon,
            'idBesoin' => $idBesoin,
            'montant_utilise' => $montant,
            'quantite_achetee' => $quantite_achetable,
            'prix_unitaire' => $prix_unitaire,
            'frais_pourcentage' => $frais_pourcentage,
            'frais_montant' => $frais_montant,
            'montant_total' => $montant_total,
            'article_nom' => $besoin['article_nom'],
            'ville_nom' => $besoin['ville_nom']
        ];
    }

    public static function validerAchat($idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total) {
        $DonModel = new DonModel(Flight::db());
        $AchatModel = new AchatModel(Flight::db());
        $db = Flight::db();

        // Récupérer le don
        $don = $DonModel->getById($idDon);
        if (!$don) {
            error_log("Don non trouvé: " . $idDon);
            return false;
        }

        // Récupérer le besoin
        $stmt = $db->prepare("SELECT * FROM BNGRC_besoin WHERE id = ?");
        $stmt->execute([$idBesoin]);
        $besoin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$besoin) {
            error_log("Besoin non trouvé: " . $idBesoin);
            return false;
        }

        // Vérifier les quantités
        if ($don['quantite'] < $montant_utilise) {
            error_log("Don insuffisant: " . $don['quantite'] . " < " . $montant_utilise);
            return false;
        }

        // Calculer quantité achetée
        $stmt = $db->prepare("SELECT prix_unitaire FROM BNGRC_article WHERE id = ?");
        $stmt->execute([$besoin['idArticle']]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        $quantite_achetee = floor($montant_utilise / $article['prix_unitaire']);

        if ($besoin['quantite'] < $quantite_achetee) {
            error_log("Besoin insuffisant: " . $besoin['quantite'] . " < " . $quantite_achetee);
            return false;
        }

        // Transaction
        $db->beginTransaction();

        try {
            // 1. Ajouter l'achat
            $AchatModel->add($idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total);
            
            // 2. Mettre à jour le don
            $nouvelle_quantite_don = $don['quantite'] - $montant_utilise;
            $stmt = $db->prepare("UPDATE BNGRC_don SET quantite = ? WHERE id = ?");
            $stmt->execute([$nouvelle_quantite_don, $idDon]);
            
            // 3. Mettre à jour le besoin
            $nouvelle_quantite_besoin = $besoin['quantite'] - $quantite_achetee;
            $stmt = $db->prepare("UPDATE BNGRC_besoin SET quantite = ? WHERE id = ?");
            $stmt->execute([$nouvelle_quantite_besoin, $idBesoin]);
            
            $db->commit();
            error_log("Achat réussi pour don " . $idDon . " et besoin " . $idBesoin);
            return true;
            
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Erreur transaction: " . $e->getMessage());
            return false;
        }
    }

    public static function getHistoriqueAchats() {
        $db = Flight::db();
        $stmt = $db->query("
            SELECT 
                a.*, 
                v.name as ville_nom
            FROM BNGRC_achat a
            JOIN BNGRC_besoin b ON a.idBesoin = b.id
            JOIN BNGRC_ville v ON b.idVille = v.id
            ORDER BY a.date_achat DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAchatsByVille($idVille) {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT 
                a.*, 
                v.name as ville_nom,
                v.id as ville_id
            FROM BNGRC_achat a
            JOIN BNGRC_besoin b ON a.idBesoin = b.id
            JOIN BNGRC_ville v ON b.idVille = v.id
            WHERE b.idVille = ?
            ORDER BY a.date_achat DESC
        ");
        $stmt->execute([$idVille]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function showAchatsPage($app){
        $AchatModel = new AchatModel(Flight::db());
        $ConfigModel = new ConfigModel(Flight::db());
        
        
        $achats = self::getAllAchats(); 
        
        $dons_argent = $AchatModel->getDonsArgentRestants();
        $besoins = $AchatModel->getBesoinsRestantsNatureMateriaux();
        $frais = $ConfigModel->getFraisAchat();
        $ville = VilleController::getVilles();
        
        
       
        
        $app->render('achats', [
            "achats" => $achats,
            "dons_argent" => $dons_argent,
            "besoins" => $besoins,
            "frais" => $frais,
            "total_achats" => count($achats),
            "villes" => $ville
        ]);
    }

    public static function updateFraisAchat() {
        $ConfigModel = new ConfigModel(Flight::db());
        $nouveau_frais = $_POST["frais_pourcentage"] ?? null;

        if ($nouveau_frais !== null) {
            $ConfigModel->updateFraisAchat($nouveau_frais);
        }
    }

    public static function deleteAchat($id){
        $AchatModel = new AchatModel(Flight::db());
        $AchatModel->delete($id);
    }
}