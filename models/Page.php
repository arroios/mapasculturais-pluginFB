<?php

namespace arroios\plugins\models;

use arroios\plugins\Database;

Class Page extends _base
{
    public $facebookPageId;
    public $facebookToken;
    public $facebookPageName;

    public $tableName = 'Page';
    public $columnFacebookPageId = 'facebookPageId';
    public $columnFacebookToken = 'facebookToken';
    public $columnFacebookPageName = 'facebookPageName';

    public function __construct($config)
    {
        if(isset($config['tableName'])) $this->tableName = $config['tableName'];
        if(isset($config['columnFacebookPageId'])) $this->columnFacebookPageId = $config['columnFacebookPageId'];
        if(isset($config['columnFacebookToken'])) $this->columnFacebookToken = $config['columnFacebookToken'];
        if(isset($config['columnFacebookPageName'])) $this->columnFacebookPageName = $config['columnFacebookPageName'];
    }

    public function load($data)
    {
        $this->facebookPageId = @$data['id'];
        $this->facebookToken = @$data['access_token'];
        $this->facebookPageName = @$data['name'];
    }

    public function getList()
    {
        $conn = Database::getConnection();
        $query = $conn->prepare("SELECT * FROM Page");

        $query->execute();

        $query->setFetchMode(\PDO::FETCH_ASSOC);

        return $query->fetchAll();
    }

    public function getInfo()
    {
        return (Object)[
            'id' => $this->facebookPageId,
            'access_token' => $this->facebookToken,
            'name' => $this->facebookPageName
        ];
    }

    public function create()
    {
        if($this->verify($this->facebookPageId, $this->tableName, $this->columnFacebookPageId) == false)
        {
            $conn = Database::getConnection();
            $sql = "INSERT INTO {$this->tableName} (
              {$this->columnFacebookPageId}, 
              {$this->columnFacebookToken}, 
              {$this->columnFacebookPageName}
              ) VALUES (
              :facebookPageId, 
              :facebookToken, 
              :facebookPageName
              )";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':facebookPageId', $this->facebookPageId);
            $stmt->bindParam(':facebookToken', $this->facebookToken);
            $stmt->bindParam(':facebookPageName', $this->facebookPageName);

            $stmt->execute();
        }
    }


}