<?php

namespace app\models;

use Flight;
use PDO;

class ImageModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function addnewImage($id_objet, $url){
        $stmt = $this->db->prepare("INSERT INTO Image(idObjet, img) VALUES(?, ?)");
        $stmt->execute([$id_objet, $url]);
    }

    public function getById($id_objet){
        $stmt = $this->db->prepare("SELECT idImage, idObjet, img FROM Image WHERE idObjet = :id_objet");
        $stmt->execute([":id_objet" => $id_objet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteById($id_objet){
        $stmt = $this->db->prepare("DELETE FROM Image WHERE idObjet = ?");
        return $stmt->execute([$id_objet]);
    }
}