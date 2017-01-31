<?php
namespace arroios\plugins\models;

use arroios\plugins\Database;

/**
 * Class _base
 * @package arroios\plugins\models
 */
Class _base
{
    /**
     * @param bool $test
     * @return \PDO
     */
    public function conn($test = false)
    {
        if($test == true)
        {
            return Database::getConnection();
        }
        else
        {
            $app = \MapasCulturais\App::i();
            $em = $app->em;
            return $em->getConnection();
        }

    }
    /**
     * @param $id
     * @param $tableName
     * @param $columnId
     * @return mixed
     */
    public function verify($id, $tableName, $columnId)
    {
        $conn = $this->conn();
        $query = $conn->prepare("SELECT * FROM {$tableName} WHERE {$columnId} = :id");
        //$query->bindColumn(':columnId', $columnId);
        $query->bindParam(':id', $id);

        $query->execute();

        $query->setFetchMode(\PDO::FETCH_ASSOC);

        return $query->fetch();
    }
}