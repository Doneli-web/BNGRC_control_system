<?php

namespace app\models;

use Flight;
use PDO;

class ResetModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function resetAll() {
        try {
            $this->db->beginTransaction();
          
            $this->db->exec("UPDATE BNGRC_don SET status = 'non_commence'");
            $this->db->exec("UPDATE BNGRC_besoin SET status = 'non_utilise'");
            $this->db->exec("DELETE FROM BNGRC_dispatch");
            $this->db->exec("DELETE FROM BNGRC_achat");
            
            
            $this->db->exec("
                UPDATE BNGRC_don d
                JOIN BNGRC_etat_initial_don e ON d.id = e.id
                SET d.quantite = e.quantite,
                    d.status = e.status
            ");
            
            
            $this->db->exec("
                UPDATE BNGRC_besoin b
                JOIN BNGRC_etat_initial_besoin e ON b.id = e.id
                SET b.quantite = e.quantite,
                    b.status = e.status
            ");
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Réinitialisation effectuée avec succès'];
            
        } catch(\Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

  
    
}