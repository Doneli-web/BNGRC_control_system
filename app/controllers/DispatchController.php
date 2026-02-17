<?php

namespace app\controllers;

use app\models\DispatchModel;
use app\models\BesoinModel;
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

            // Calculer le montant déjà satisfait (prix_unitaire * quantite_attribuee)
            $besoinModel = new BesoinModel($db);
            $montantSatisfait = $besoinModel->getMontantSatisfait();

            // Retourner un résumé
            Flight::json([
                'status' => 'ok',
<<<<<<< HEAD
                'data' => [],
                'inserted' => $inserted,
                'statistics' => [
                    'attributions_creees' => $inserted
                ]
=======
                'inserted' => $inserted,
                'montant_satisfait' => $montantSatisfait
>>>>>>> 9d72703d032c4ef8d3a2e524cf4c7dc913246c56
            ]);

        } catch(\Exception $e){
            $db->rollBack();
            Flight::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public static function simulatePreview(){
        $db = Flight::db();
        $dispatchModel = new DispatchModel($db);

        // Simulation incrémentale
        $besoins = $dispatchModel->getBesoinNonComble();
        $dons = $dispatchModel->getDonDisponible();

        $besoinRemaining = [];
        foreach($besoins as $b){
            $besoinRemaining[$b['id']] = (int)$b['quantite'];
        }

        $previewDispatches = [];

        foreach($dons as $don){
            $donQty = (int)$don['quantite'];
            if($donQty <= 0) continue;

            foreach($besoins as $b){
                if((int)$b['idArticle'] !== (int)$don['idArticle']) continue;
                $needId = $b['id'];
                $needRem = $besoinRemaining[$needId] ?? 0;
                if($needRem <= 0) continue;

                $alloc = min($donQty, $needRem);
                if($alloc <= 0) continue;

                // Simuler l'insertion
                $previewDispatches[] = [
                    'idDon' => $don['id'],
                    'idBesoin' => $needId,
                    'quantite_attribuee' => $alloc,
                    'date_dispatch' => date('Y-m-d H:i:s')
                ];

                $donQty -= $alloc;
                $besoinRemaining[$needId] -= $alloc;

                if($donQty <= 0) break;
            }
        }

        Flight::json([
            'status' => 'ok',
            'data' => $previewDispatches,
            'statistics' => [
                'attributions_creees' => count($previewDispatches)
            ]
        ]);
    }
}
