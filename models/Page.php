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

    public function __construct($config, $data)
    {
        $this->facebookPageId = $data['id'];
        $this->facebookToken = $data['access_token'];
        $this->facebookPageName = $data['name'];

        if(isset($config['tableName'])) $this->tableName = $config['tableName'];
        if(isset($config['columnFacebookPageId'])) $this->columnFacebookPageId = $config['columnFacebookPageId'];
        if(isset($config['columnFacebookToken'])) $this->columnFacebookToken = $config['columnFacebookToken'];
        if(isset($config['columnFacebookPageName'])) $this->columnFacebookPageName = $config['columnFacebookPageName'];


    }

    public function getInfo()
    {
        
    }

    public function create()
    {
        if($this->verify($this->facebookPageId, $this->tableName, $this->facebookPageId) == false)
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