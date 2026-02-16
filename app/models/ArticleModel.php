<?php 

namespace app\models;
use Flight;
use PDO;

class ArticleModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function add($name, $idType, $prix_unitaire){
        $stmt = $this->db->prepare("INSERT INTO BNGRC_article(name, idType, prix_unitaire) VALUES (?, ?, ?)");
        $stmt->execute([$name, $idType, $prix_unitaire]);
    }

    public function getAll(){
        $stmt = $this->db->query("SELECT * FROM BNGRC_article");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM BNGRC_article WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByType($idType){
        $stmt = $this->db->prepare("SELECT* FROM BNGRC_article WHERE idType = ?");
        $stmt->execute([$idType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}