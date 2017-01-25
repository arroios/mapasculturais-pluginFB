<?php
namespace arroios\plugins\models;

use arroios\plugins\Database;

Class _base
{
    public function verify($id, $table)
    {
        $conn = Database::getConnection();
        $query = $conn->prepare("SELECT id FROM {$table} WHERE id = :id");
        $query->bindParam(':id', $id);

        $query->execute();

        $query->setFetchMode(\PDO::FETCH_ASSOC);

        if($query->fetch() == false)
        {
            $conn = Database::getConnection();
            $sql = "INSERT INTO tags (id, name) VALUES (:name, :color)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':color', $color);
            $stmt->execute();
        }

        return $query->fetch();
    }

    public function save()
    {
        // grava na base de dados
    }


}