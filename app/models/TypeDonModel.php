<?php

namespace app\models;
use Flight;
use PDO;

class TypeDonModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function add($type){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_typeDon(type) VALUES (?)");
        $stmt->execute([$type]);
    }

    public function getAll(){
        $stmt = $this->db->query("SELECT * FROM BNGRC_typeDon");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_typeDon WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
}