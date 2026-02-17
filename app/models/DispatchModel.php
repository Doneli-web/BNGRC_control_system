<?php
namespace app\models;

use Flight;
use PDO;

class DispatchModel {
    private $db;

    public function __construct($db = null){
        $this->db = $db ?: Flight::db();
    }

    public function insert($idDon, $idBesoin, $quantite, $dateDispatch = null){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee, date_dispatch) VALUES (?, ?, ?, ?)");
        $dt = $dateDispatch ?: date('Y-m-d H:i:s');
        $stmt->execute([$idDon, $idBesoin, $quantite, $dt]);
        return $this->db->lastInsertId();
    }

    public function getBesoinNonComble(){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_besoin WHERE status != 'comble' ORDER BY date_de_saisie ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonDisponible(){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_don WHERE quantite > 0 ORDER BY date_de_saisie ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function preview(): array {
        return $this->run(false);
    }

    public function execute(): array {
        return $this->run(true);
    }

    private function run(bool $persist): array {
        $dons = $this->getDonDisponible();
        $besoins = $this->getBesoinNonComble();

        $dispatchs = [];
        $inserted = 0;
        $don_utilises = 0;

        // besoin restant local
        $besoinRemaining = [];
        foreach ($besoins as $b) {
            $besoinRemaining[$b['id']] = (int)$b['quantite'];
        }

        foreach ($dons as $don) {
            $donQty = (int)$don['quantite'];
            if ($donQty <= 0) continue;

            foreach ($besoins as $b) {
                if ($donQty <= 0) break;
                if ($b['idArticle'] != $don['idArticle']) continue;

                $needId = $b['id'];
                $needRem = $besoinRemaining[$needId];
                if ($needRem <= 0) continue;

                $alloc = min($donQty, $needRem);

                $dispatchs[] = [
                    'idDon' => $don['id'],
                    'idBesoin' => $needId,
                    'idArticle' => $don['idArticle'],
                    'quantite' => $alloc
                ];

                if ($persist) {
                    $this->persistDispatch($don['id'], $needId, $alloc);
                }

                $donQty -= $alloc;
                $don_utilises += $alloc;
                $besoinRemaining[$needId] -= $alloc;
                $inserted++;
            }
        }

        // Stats
        $total_besoins = array_sum(array_column($besoins, 'quantite'));
        $total_attribue = array_sum(array_column($dispatchs, 'quantite'));
        $taux = $total_besoins ? round(($total_attribue / $total_besoins) * 100) : 0;

        return [
            'data' => $dispatchs,
            'statistics' => [
                'attributions_creees' => $inserted,
                'dons_utilises' => $don_utilises,
                'total_dons' => count($dons),
                'total_besoins' => count($besoins),
                'taux_couverture_besoins' => $taux
            ]
        ];
    }

    private function persistDispatch($idDon, $idBesoin, $qty): void {
        // insert dispatch
        $stmt = $this->db->prepare("
            INSERT INTO BNGRC_dispatch(idDon, idBesoin, quantite_attribuee, date_dispatch)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$idDon, $idBesoin, $qty]);

        // update don
        $this->db->prepare("
            UPDATE BNGRC_don SET quantite = quantite - ? WHERE id = ?
        ")->execute([$qty, $idDon]);
    }

}
