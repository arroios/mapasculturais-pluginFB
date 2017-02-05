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
    public $userId;

    public $tableName = 'Page';
    public $columnFacebookPageId = 'facebook_page_id';
    public $columnFacebookToken = 'facebook_token';
    public $columnFacebookPageName = 'facebook_page_name';
    public $columnUserId = 'user_id';

    /**
     * Page constructor.
     * @param $config
     * @param $userId
     */
    public function __construct($config, $userId)
    {
        if(isset($config['tableName'])) $this->tableName = $config['tableName'];
        if(isset($config['columnFacebookPageId'])) $this->columnFacebookPageId = $config['columnFacebookPageId'];
        if(isset($config['columnFacebookToken'])) $this->columnFacebookToken = $config['columnFacebookToken'];
        if(isset($config['columnFacebookPageName'])) $this->columnFacebookPageName = $config['columnFacebookPageName'];
        if(isset($config['columnUserId'])) $this->columnUserId = $config['columnUserId'];

        $this->userId = $userId;
    }

    /**
     * @param $data
     * @param $target
     */
    public function load($data, $target)
    {
        $this->facebookPageId = $target == 'facebook' ? $data['id'] : $data[$this->columnFacebookPageId];
        $this->facebookToken = $target == 'facebook' ? $data['access_token'] : $data[$this->columnFacebookToken];
        $this->facebookPageName = $target == 'facebook' ? pg_escape_string($data['name']) : pg_escape_string($data[$this->columnFacebookPageName]);

        if($this->userId == NULL)
        {
            $this->userId = $this->getUserId();
        }
    }

    public function getUserId()
    {
        $conn = $this->conn();
        $query = $conn->prepare("SELECT * FROM {$this->tableName} WHERE {$this->columnFacebookPageId} = '{$this->facebookPageId}'");

        $query->execute();

        $query->setFetchMode(\PDO::FETCH_ASSOC);

        return $query->fetch()[$this->columnUserId];
    }

    /**
     * @return array
     */
    public function getList()
    {
        $conn = $this->conn();
        $query = $conn->prepare("SELECT * FROM {$this->tableName}");

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
              {$this->columnFacebookPageName},
              {$this->columnUserId}
              ) VALUES (
              '{$this->facebookPageId}', 
              '{$this->facebookToken}', 
              '{$this->facebookPageName}',
              '{$this->userId}'
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