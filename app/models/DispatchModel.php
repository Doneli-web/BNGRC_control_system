<?php
namespace app\models;

use Flight;
use PDO;

class DispatchModel {
    private $db;

    public function __construct($db = null){
        $this->db = $db ?: Flight::db();
    }

    // Suppression du clearAll, simulation incrémentale

    public function getBesoinNonComble(){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_besoin WHERE status != 'comble' ORDER BY date_de_saisie ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonDisponible(){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_don WHERE status != 'utilise' ORDER BY date_de_saisie ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($idDon, $idBesoin, $quantite, $dateDispatch = null){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee, date_dispatch) VALUES (?, ?, ?, ?)");
        $dt = $dateDispatch ?: date('Y-m-d H:i:s');
        $stmt->execute([$idDon, $idBesoin, $quantite, $dt]);
        return $this->db->lastInsertId();
    }

    public function simulateDispatch(){
        $besoins = $this->getBesoinNonComble();
        $dons = $this->getDonDisponible();

        $inserted = 0;

        $besoinRemaining = [];
        foreach($besoins as $b){
            $besoinRemaining[$b['id']] = (int)$b['quantite'];
        }

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

                $inserted++;

                // Simuler l'insertion
                // echo "Simulating dispatch: Don {$don['id']} -> Besoin {$needId} | Alloc: {$alloc}\n";

                $donQty -= $alloc;
                $besoinRemaining[$needId] -= $alloc;

                if($donQty <= 0) break;
            }
            foreach($besoinRemaining as $id => $remaining){
                $status = $remaining <= 0 ? 'comble' : 'non_comble';

                $this->db->prepare("UPDATE BNGRC_besoin SET status=? WHERE id=?")
                        ->execute([$status, $id]);
            }
        }

        return $inserted;
    }
}
