<?php
namespace app\controllers;

use app\models\DispatchModel;
use Flight;
use PDO;

class DispatchController {

    public static function simulate(){
        $db = Flight::db();
        $dispatchModel = new DispatchModel($db);

        try {
            $db->beginTransaction();


            // Simulation incrémentale
            $besoins = $dispatchModel->getBesoinNonComble();
            $dons = $dispatchModel->getDonDisponible();

            $besoinRemaining = [];
            foreach($besoins as $b){
                $besoinRemaining[$b['id']] = (int)$b['quantite'];
            }

            $inserted = 0;

            foreach($dons as $don){
                $donQty = (int)$don['quantite'];
                $donUsed = 0;
                if($donQty <= 0) continue;

                foreach($besoins as $b){
                    if((int)$b['idArticle'] !== (int)$don['idArticle']) continue;
                    $needId = $b['id'];
                    $needRem = $besoinRemaining[$needId] ?? 0;
                    if($needRem <= 0) continue;

                    $alloc = min($donQty, $needRem);
                    if($alloc <= 0) continue;

                    $dispatchModel->insert($don['id'], $needId, $alloc, date('Y-m-d H:i:s'));
                    $inserted++;

                    $donQty -= $alloc;
                    $donUsed += $alloc;
                    $besoinRemaining[$needId] -= $alloc;

                    // Mettre à jour le statut du besoin
                    $newStatus = 'en_cours';
                    if($besoinRemaining[$needId] == 0) $newStatus = 'comble';
                    elseif($besoinRemaining[$needId] < $b['quantite']) $newStatus = 'en_cours';
                    else $newStatus = 'non_commence';
                    $db->prepare("UPDATE BNGRC_besoin SET status = ? WHERE id = ?")->execute([$newStatus, $needId]);

                    if($donQty <= 0) break;
                }
                // Mettre à jour le statut du don
                $donStatus = 'non_utilise';
                if($donUsed == (int)$don['quantite']) $donStatus = 'utilise';
                elseif($donUsed > 0 && $donUsed < (int)$don['quantite']) $donStatus = 'en_cours';
                $db->prepare("UPDATE BNGRC_don SET status = ? WHERE id = ?")->execute([$donStatus, $don['id']]);
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
