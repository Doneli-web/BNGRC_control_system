<?php

namespace app\models;

use Flight;
use PDO;

class DonModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function add($idArticle, $quantity){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_don(idArticle, quantite) VALUES (?, ?, ?)");
        $stmt->execute([$idArticle, $quantity]);
    }

    public function getAll(){
        $stmt = $this->db->query("SELECT * FROM BNGRC_don");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_don WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
}