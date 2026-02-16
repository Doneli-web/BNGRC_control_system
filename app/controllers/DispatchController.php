<?php
namespace app\controllers;

use app\models\DispatchModel;
use Flight;
use PDO;

class DispatchController {

    /**
     * Simule la répartition des dons et insère les enregistrements dans BNGRC_dispatch.
     * Règle: pour chaque don (ordre asc par date_de_saisie), on parcourt les besoins
     * pour le même article (ordre asc par date_de_saisie) et on attribue jusqu'à
     * épuisement du don ou satisfaction du besoin.
     */
    public static function simulate(){
        $db = Flight::db();
        $dispatchModel = new DispatchModel($db);

        try {
            $db->beginTransaction();

            // Nettoyer les anciennes dispatchs pour une simulation fraîche
            $dispatchModel->clearAll();

            // Récupérer tous les dons triés par date_de_saisie asc
            $stmtD = $db->prepare("SELECT id, idArticle, quantite, date_de_saisie FROM BNGRC_don ORDER BY date_de_saisie ASC, id ASC");
            $stmtD->execute();
            $dons = $stmtD->fetchAll(PDO::FETCH_ASSOC);

            // Préparer un tableau des besoins restants (quantité)
            $stmtB = $db->prepare("SELECT id, idArticle, idVille, quantite, date_de_saisie FROM BNGRC_besoin ORDER BY date_de_saisie ASC, id ASC");
            $stmtB->execute();
            $besoins = $stmtB->fetchAll(PDO::FETCH_ASSOC);

            $besoinRemaining = [];
            foreach($besoins as $b){
                $besoinRemaining[$b['id']] = (int)$b['quantite'];
            }

            $inserted = 0;

            // Pour chaque don, distribuer aux besoins correspondants (même idArticle)
            foreach($dons as $don){
                $donQty = (int)$don['quantite'];
                if($donQty <= 0) continue;

                // parcourir les besoins pour cet article dans l'ordre de saisie
                foreach($besoins as $b){
                    if((int)$b['idArticle'] !== (int)$don['idArticle']) continue;
                    $needId = $b['id'];
                    $needRem = $besoinRemaining[$needId] ?? 0;
                    if($needRem <= 0) continue;

                    $alloc = min($donQty, $needRem);
                    if($alloc <= 0) continue;

                    // Insérer en dispatch
                    $dispatchModel->insert($don['id'], $needId, $alloc, date('Y-m-d H:i:s'));
                    $inserted++;

                    // Décrémenter
                    $donQty -= $alloc;
                    $besoinRemaining[$needId] -= $alloc;

                    // Si don épuisé -> passer au don suivant
                    if($donQty <= 0) break;
                }
            }

            $db->commit();

            // Retourner un résumé
            Flight::json([
                'status' => 'ok',
                'inserted' => $inserted
            ]);
        } catch(\Exception $e){
            $db->rollBack();
            Flight::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}