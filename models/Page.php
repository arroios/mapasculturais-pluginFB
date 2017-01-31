<?php

namespace arroios\plugins\models;

use arroios\plugins\Database;

/**
 * Class Page
 * @package arroios\plugins\models
 */
Class Page extends _base
{
    public $facebookPageId;
    public $facebookToken;
    public $facebookPageName;

    public $tableName = 'Page';
    public $columnFacebookPageId = 'facebookPageId';
    public $columnFacebookToken = 'facebookToken';
    public $columnFacebookPageName = 'facebookPageName';

    /**
     * Page constructor.
     * @param $config
     */
    public function __construct($config)
    {
        if(isset($config['tableName'])) $this->tableName = $config['tableName'];
        if(isset($config['columnFacebookPageId'])) $this->columnFacebookPageId = $config['columnFacebookPageId'];
        if(isset($config['columnFacebookToken'])) $this->columnFacebookToken = $config['columnFacebookToken'];
        if(isset($config['columnFacebookPageName'])) $this->columnFacebookPageName = $config['columnFacebookPageName'];
    }

    /**
     * @param $data
     * @param $target
     */
    public function load($data, $target)
    {
        $this->facebookPageId = $target == 'facebook' ? $data['id'] : $data[$this->columnFacebookPageId];
        $this->facebookToken = $target == 'facebook' ? $data['access_token'] : $data[$this->columnFacebookToken];
        $this->facebookPageName = $target == 'facebook' ? $data['name'] : $data[$this->columnFacebookPageName];
    }

    /**
     * @return array
     */
    public function getList()
    {
        $conn = $this->conn();
        $query = $conn->prepare("SELECT * FROM Page");

        $query->execute();

        $query->setFetchMode(\PDO::FETCH_ASSOC);

        return $query->fetchAll();
    }

    /**
     * @return object
     */
    public function getInfo()
    {
        return (Object)[
            $this->columnFacebookPageId => $this->facebookPageId,
            $this->columnFacebookToken => $this->facebookToken,
            $this->columnFacebookPageName => $this->facebookPageName
        ];
    }

    /**
     * @return mixed
     */
    public function save()
    {
        if($this->verify($this->facebookPageId, $this->tableName, $this->columnFacebookPageId) == false)
        {
            $conn = $this->conn();
            $sql = "INSERT INTO {$this->tableName} (
              {$this->columnFacebookPageId}, 
              {$this->columnFacebookToken}, 
              {$this->columnFacebookPageName}
              ) VALUES (
              {$this->facebookPageId}, 
              {$this->facebookToken}, 
              {$this->facebookPageName}
              )";
            $stmt = $conn->prepare($sql);

            //$stmt->bindParam(':facebookPageId', $this->facebookPageId);
            //$stmt->bindParam(':facebookToken', $this->facebookToken);
            //$stmt->bindParam(':facebookPageName', $this->facebookPageName);

            $stmt->execute();

            $stmt->setFetchMode(\PDO::FETCH_ASSOC);

            return $stmt->fetch();
        }
    }


}