<?php

namespace app\models;

use Flight;
use PDO;

class ObjetModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

  

    public function getById($id){
        $stmt = $this->db->prepare("SELECT idObjet, idCategorie, idUser, nomObjet, description, prix_estimatif FROM Objet WHERE idObjet = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByName($name){
        $stmt = $this->db->prepare("SELECT idObjet, idCategorie, idUser, nomObjet, description, prix_estimatif FROM Objet WHERE nomObjet LIKE :name");
        $stmt->execute([":name" => "%$name%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllObject(){
        $stmt = $this->db->prepare("SELECT idObjet, idCategorie, idUser, nomObjet, description, prix_estimatif FROM Objet ORDER BY idObjet DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($id_category){
        $stmt = $this->db->prepare("SELECT idObjet, idCategorie, idUser, nomObjet, description, prix_estimatif FROM Objet WHERE idCategorie = :id_category ORDER BY idObjet DESC");
        $stmt->execute([":id_category" => $id_category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateObject($id, $data){
        $sql = "UPDATE Objet SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['nomObjet']) || isset($data['name'])) {
            $updates[] = "nomObjet = ?";
            $params[] = $data['nomObjet'] ?? $data['name'];
        }
        if (isset($data['description'])) {
            $updates[] = "description = ?";
            $params[] = $data['description'];
        }
        if (isset($data['prix_estimatif'])) {
            $updates[] = "prix_estimatif = ?";
            $params[] = $data['prix_estimatif'];
        }
        if (isset($data['idCategorie']) || isset($data['id_category'])) {
            $updates[] = "idCategorie = ?";
            $params[] = $data['idCategorie'] ?? $data['id_category'];
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql .= implode(", ", $updates);
        $sql .= " WHERE idObjet = ?";
        $params[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function removeObject($id){
        // D'abord supprimer les images associées
        $imageModel = new ImageModel($this->db);
        $imageModel->deleteById($id);
        
        // Ensuite supprimer l'objet
        $stmt = $this->db->prepare("DELETE FROM Objet WHERE idObjet = ?");
        return $stmt->execute([$id]);
    }

    public function addnewObject($id_category, $name, $description, $prix_estimatif, $id_user){
        $stmt = $this->db->prepare("INSERT INTO Objet(idCategorie, nomObjet, description, prix_estimatif, idUser)
            VALUES(?, ?, ?, ?, ?)");
        $stmt->execute([$id_category, $name, $description, $prix_estimatif, $id_user]);
    }

    public function countObjectByCategorie($id){
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Objet WHERE idCategorie = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function findUserObjects($id_user){
        $stmt = $this->db->prepare("SELECT idObjet, idCategorie, nomObjet, description, prix_estimatif FROM Objet WHERE idUser = ? ORDER BY idObjet DESC");
        $stmt->execute([$id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}