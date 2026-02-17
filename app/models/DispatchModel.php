<?php
namespace app\models;

use Flight;
use PDO;

class DispatchModel {
    private $db;

    public function __construct($db = null){
        $this->db = $db ?: Flight::db();
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

    public function insert($idDon, $idBesoin, $quantite, $dateDispatch = null){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite, date_dispatch) VALUES (?, ?, ?, ?)");
        $dt = $dateDispatch ?: date('Y-m-d H:i:s');
        $stmt->execute([$idDon, $idBesoin, $quantite, $dt]);
        return $this->db->lastInsertId();
    }

    /**
     * Preview sans modifier la DB
     */
    public function preview(): array {
        return $this->run(false);
    }

    /**
     * Exécution réelle avec mise à jour DB
     */
    public function execute(): array {
        return $this->run(true);
    }

    private function run(bool $persist): array {
        $dispatchs = [];
        $dons = $this->getDonDisponible();
        $besoins = $this->getBesoinNonComble();

        $inserted = 0;
        $don_utilises = 0;

        // Cloner quantités pour calcul local
        $besoinRemaining = [];
        foreach($besoins as $b){
            $besoinRemaining[$b['id']] = (int)$b['quantite'];
        }

        foreach($dons as $don){
            $donQty = (int)$don['quantite'];
            if($donQty <= 0) continue;

            foreach($besoins as $b){
                if($donQty <= 0) break;
                if($b['idArticle'] != $don['idArticle']) continue;

                $needId = $b['id'];
                $needRem = $besoinRemaining[$needId] ?? 0;
                if($needRem <= 0) continue;

                $alloc = min($donQty, $needRem);
                if($alloc <= 0) continue;

                $dispatchs[] = [
                    'idDon' => $don['id'],
                    'idBesoin' => $needId,
                    'idArticle' => $don['idArticle'],
                    'quantite' => $alloc
                ];

                if($persist){
                    $this->persistDispatch($don, $b, $alloc);
                }

                $donQty -= $alloc;
                $don_utilises += $alloc;
                $besoinRemaining[$needId] -= $alloc;
                $inserted++;
            }
        }

        // Calcul des stats
        $villes_servies = count(array_unique(array_column($besoins, 'ville')));
        $taux_couverture_besoins = 0;
        $total_besoins = 0;
        $total_attribue = 0;
        foreach($besoins as $b){
            $total_besoins += (int)$b['quantite'];
            $attribue = ((int)$b['quantite'] - ($besoinRemaining[$b['id']] ?? 0));
            $total_attribue += $attribue;
        }
        if($total_besoins > 0) $taux_couverture_besoins = round(($total_attribue / $total_besoins)*100);

        return [
            'data' => $dispatchs,
            'statistics' => [
                'attributions_crees' => $inserted,
                'dons_utilises' => $don_utilises,
                'total_dons' => count($dons),
                'total_besoins' => count($besoins),
                'villes_servies' => $villes_servies,
                'taux_couverture_besoins' => $taux_couverture_besoins
            ]
        ];
    }

    private function persistDispatch(array $don, array $besoin, int $qty): void {
        // INSERT dispatch
        $stmt = $this->db->prepare("
            INSERT INTO BNGRC_dispatch(idDon, idBesoin, quantite, date_dispatch)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$don['id'], $besoin['id'], $qty]);

        // UPDATE don
        $this->db->prepare("UPDATE BNGRC_don SET quantite = quantite - ? WHERE id = ?")
                 ->execute([$qty, $don['id']]);

        // UPDATE besoin restant et statut
        $this->db->prepare("
            UPDATE BNGRC_besoin
            SET quantite = quantite - ?,
                status = CASE
                            WHEN quantite - ? <= 0 THEN 'comble'
                            ELSE 'en_cours'
                         END
            WHERE id = ?
        ")->execute([$qty, $qty, $besoin['id']]);
    }
}
