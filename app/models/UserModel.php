<?php

namespace app\models;

use Flight;
use PDO;

class UserModel{
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function addUser($userName, $email, $password){
        $stmt = $this->db->prepare("INSERT INTO users(username, email, password) VALUES(?, ?, ?)");
        $stmt->execute([$userName, $email, $this->hash_password($password)]);
    }

    public function removeUser($id){
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$id]);
    }

    public function getByEmail($email){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAll(){
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIdByEmail(String $email){
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function hash_password($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }
}