<?php
namespace arroios\plugins\models;

use arroios\plugins\Database;

Class _base
{
    public function verify($id, $tableName, $columnId)
    {
        $conn = Database::getConnection();
        $query = $conn->prepare("SELECT * FROM {$tableName} WHERE {$columnId} = :id");
        //$query->bindColumn(':columnId', $columnId);
        $query->bindParam(':id', $id);

        $query->execute();

        $query->setFetchMode(\PDO::FETCH_ASSOC);

        return $query->fetch();
    }

    public function save()
    {
        // grava na base de dados
    }
}