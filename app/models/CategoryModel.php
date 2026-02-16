<?php

namespace app\models;

use Flight;
use PDO;

class CategoryModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function createCategory($name){
        $stmt = $this->db->prepare("INSERT INTO Category(name) VALUES(?)");
        $stmt->execute([$name]);
    }

    public function getAll(){
        $stmt = $this->db->prepare("SELECT id, name FROM Category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT id, name FROM Category WHERE id=:id");
        $stmt->execute([
            ":id"=>$id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCategory($id, $name){
        $stmt = $this->db->prepare("UPDATE Category SET name=:name WHERE id=:id");
        $stmt->execute([
            ":id"=>$id,
            ":name"=>$name
        ]);
    }

    public function deleteCategory($id){
        $stmt = $this->db->prepare("DELETE FROM Category WHERE id=:id");
        $stmt->execute([
            ":id"=>$id
        ]);
    }
}

