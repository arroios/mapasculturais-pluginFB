<?php

namespace arroios\plugins\models;

use arroios\plugins\Database;

Class Event extends _base
{
    public $facebookEventId;
    public $facebookPageId;
    public $facebookEventUpdateTime;
    public $startTime;
    public $endTime;
    public $name;
    public $description;
    public $cover;
    public $place;
    public $state;
    public $city;
    public $street;
    public $zip;
    public $latitude;
    public $longitude;

    public $tableName = 'Event';
    public $columnFacebookEventId = 'facebookEventId';
    public $columnFacebookPageId = 'facebookPageId';
    public $columnFacebookEventUpdateTime = 'facebookEventUpdateTime';
    public $columnStartTime = 'startTime';
    public $columnEndTime = 'endTime';
    public $columnName = 'name';
    public $columnDescription = 'description';
    public $columnCover = 'cover';
    public $columnPlace = 'place';
    public $columnState = 'state';
    public $columnCity = 'city';
    public $columnStreet = 'street';
    public $columnZip = 'zip';
    public $columnLatitude = 'latitude';
    public $columnLongitude = 'longitude';

    public function __construct($config, $data, $pageId)
    {
        $this->facebookEventId = $data['id'];
        $this->columnFacebookPageId = $pageId;
        $this->facebookEventUpdateTime = $data['updated_time'];
        $this->startTime = $data['start_time'];
        $this->endTime = $data['end_time'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->cover = $data['cover']['source'];
        $this->place = $data['place']['name'];
        $this->state = $data['place']['location']['state'];
        $this->city = $data['place']['location']['city'];
        $this->street = $data['place']['location']['street'];
        $this->zip = $data['place']['location']['zip'];
        $this->latitude = $data['place']['location']['latitude'];
        $this->longitude = $data['place']['location']['longitude'];


        if(isset($config['tableName'])) $this->tableName = $config['tableName'];
        if(isset($config['columnFacebookEventId'])) $this->columnFacebookEventId = $config['columnFacebookEventId'];
        if(isset($config['columnFacebookPageId'])) $this->columnFacebookPageId = $config['columnFacebookPageId'];
        if(isset($config['columnFacebookEventUpdateTime'])) $this->columnFacebookEventUpdateTime = $config['columnFacebookEventUpdateTime'];
        if(isset($config['columnStartTime'])) $this->columnStartTime = $config['columnStartTime'];
        if(isset($config['columnEndTime'])) $this->columnEndTime = $config['columnEndTime'];
        if(isset($config['columnName'])) $this->columnDescription = $config['columnName'];
        if(isset($config['columnDescription'])) $this->columnDescription = $config['columnDescription'];
        if(isset($config['columnCover'])) $this->columnCover = $config['columnCover'];
        if(isset($config['columnPlace'])) $this->columnPlace = $config['columnPlace'];
        if(isset($config['columnState'])) $this->columnState = $config['columnState'];
        if(isset($config['columnCity'])) $this->columnCity = $config['columnCity'];
        if(isset($config['columnStreet'])) $this->columnStreet = $config['columnStreet'];
        if(isset($config['columnZip'])) $this->columnZip = $config['columnZip'];
        if(isset($config['columnLatitude'])) $this->columnLatitude = $config['columnLatitude'];
        if(isset($config['columnLongitude'])) $this->columnLongitude = $config['columnLongitude'];

    }

    public function create()
    {
        if($this->verify($this->facebookEventId, $this->tableName, $this->columnFacebookEventId) == false)
        {
            $conn = Database::getConnection();
            $sql = "INSERT INTO {$this->tableName} (
              {$this->columnFacebookEventId}, 
              {$this->columnFacebookPageId}, 
              {$this->columnFacebookEventUpdateTime}, 
              {$this->columnStartTime}, 
              {$this->columnEndTime}, 
              {$this->columnName},
              {$this->columnDescription},
              {$this->columnCover},
              {$this->columnPlace},
              {$this->columnState},
              {$this->columnCity},
              {$this->columnStreet},
              {$this->columnZip},
              {$this->columnLatitude},
              {$this->columnLongitude}
              ) VALUES (
              :facebookEventId, 
              :facebookPageId, 
              :facebookEventUpdateTime, 
              :start_time, 
              :end_time, 
              :name, 
              :description,
              :cover,
              :place,
              :state,
              :city,
              :street,
              :zip,
              :latitude,
              :longitude
              )";
            $stmt = $conn->prepare($sql);

            $stmt->bindColumn(':columnFacebookEventId', $this->columnFacebookEventId);
            $stmt->bindColumn(':columnFacebookPageId', $this->columnFacebookPageId);
            $stmt->bindColumn(':columnFacebookEventUpdateTime', $this->columnFacebookEventUpdateTime);
            $stmt->bindColumn(':columnStartTime', $this->columnStartTime);
            $stmt->bindColumn(':columnEndTime', $this->columnEndTime);
            $stmt->bindColumn(':columnName', $this->columnName);
            $stmt->bindColumn(':columnDescription', $this->columnDescription);
            $stmt->bindColumn(':columnCover', $this->columnCover);
            $stmt->bindColumn(':columnPlace', $this->columnPlace);
            $stmt->bindColumn(':columnState', $this->columnState);
            $stmt->bindColumn(':columnCity', $this->columnCity);
            $stmt->bindColumn(':columnStreet', $this->columnStreet);
            $stmt->bindColumn(':columnZip', $this->columnZip);
            $stmt->bindColumn(':columnLatitude', $this->columnLatitude);
            $stmt->bindColumn(':columnLongitude', $this->columnLongitude);

            $stmt->bindParam(':facebookEventId', $this->facebookEventId);
            $stmt->bindParam(':facebookPageId', $this->facebookPageId);
            $stmt->bindParam(':facebookEventUpdateTime', $this->facebookEventUpdateTime);
            $stmt->bindParam(':start_time', $this->startTime);
            $stmt->bindParam(':end_time', $this->endTime);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':cover', $this->cover);
            $stmt->bindParam(':place', $this->place);
            $stmt->bindParam(':state', $this->state);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':street', $this->street);
            $stmt->bindParam(':zip', $this->zip);
            $stmt->bindParam(':latitude', $this->latitude);
            $stmt->bindParam(':longitude', $this->longitude);

            $stmt->execute();
        }
    }
}