<?php

namespace app\models;

use Flight;
use PDO;

class RegionModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function findAll(){
        $stmt = $this->db->prepare("SELECT * name FROM BNGRC_region");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id){
        $stmt = $this->db->prepare("SELECT id, name FROM BNGRC_region WHERE id=:id");
        $stmt->execute([
            ":id"=>$id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRegion($name){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_region (name) VALUES (:name)");
        $stmt->execute([
            ":name"=>$name
        ]);
    }
}