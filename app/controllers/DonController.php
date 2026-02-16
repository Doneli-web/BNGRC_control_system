<?php

namespace app\controllers;

use app\models\DonModel;
use Flight;
use PDO;

class DonController{
   
    public static function addDon($idArticle, $quantity){
        $DonModel = new DonModel(Flight::db());
        $DonModel->add($idArticle, $quantity);
    }

    public static function getAllDons(){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->findAllWithDetails(); 
    }

    public static function getDonsByStatut($statut){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getDonsByStatut($statut);
    }

    public static function updateStatut($id, $statut){
        $DonModel = new DonModel(Flight::db());
        $DonModel->updateStatut($id, $statut);
    }

    public static function getStatistiques(){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getStatistiques();
    }

    public static function getById($id){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getById($id);
    }

    public static function getAll(){
        $DonModel = new DonModel(Flight::db());
        return $DonModel->getAll();
    }

    
    public static function showDonsPage($app){
        
        $dons = self::getAllDons();
        
        
        $db = Flight::db();
        $stmt = $db->query("SELECT * FROM BNGRC_article");
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        
        $stats = self::getStatistiques();
        
        // Afficher la vue
        $app->render('dons', [
            "dons" => $dons,
            "articles" => $articles, // Pour le formulaire
            "stats" => $stats,
            "total_dons" => count($dons)
        ]);
    }

    // Méthode pour ajouter depuis formulaire
    public static function addDonFromForm(){
        $idArticle = $_POST["idArticle"] ?? null;
        $quantite = $_POST["quantite"] ?? null;
        
        if(!$idArticle || !$quantite){
            $_SESSION['error'] = "Champs requis manquants";
            Flight::redirect('/dons');
            return;
        }
        
        self::addDon($idArticle, $quantite);
        
        $_SESSION['success'] = "Don ajouté avec succès";
        Flight::redirect('/dons');
    }

    public static function deleteDon($id){
        $DonModel = new DonModel(Flight::db());
        $result = $DonModel->delete($id);
        
        if($result) {
            $_SESSION['success'] = "Don supprimé avec succès";
        } else {
            $_SESSION['error'] = "Don introuvable";
        }
    }
}