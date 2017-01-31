<?php

namespace arroios\plugins\models;

use arroios\plugins\Database;

/**
 * Class Event
 * @package arroios\plugins\models
 */
Class Event extends _base
{
    public $facebookEventId;
    public $facebookPageId;
    public $facebookEventUpdateTime;
    public $facebookPlaceId;
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
    public $columnFacebookPlaceId = 'facebookPlaceId';
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

    public $userId;

    /**
     * Event constructor.
     * @param $config
     * @param $userId
     */
    public function __construct($config, $userId)
    {
        if(isset($config['tableName'])) $this->tableName = $config['tableName'];
        if(isset($config['columnFacebookEventId'])) $this->columnFacebookEventId = $config['columnFacebookEventId'];
        if(isset($config['columnFacebookPageId'])) $this->columnFacebookPageId = $config['columnFacebookPageId'];
        if(isset($config['columnFacebookEventUpdateTime'])) $this->columnFacebookEventUpdateTime = $config['columnFacebookEventUpdateTime'];
        if(isset($config['columnFacebookPlaceId'])) $this->columnFacebookPlaceId = $config['columnFacebookEventPlaceId'];
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

        $this->userId = $userId;

    }

    /**
     * @param $data
     * @param $pageId
     */
    public function load($data, $pageId)
    {
        $this->facebookEventId = @$data['id'];
        $this->facebookPageId = $pageId;
        $this->facebookEventUpdateTime = @$data['updated_time'];
        $this->facebookPlaceId = @$data['place']['id'];
        $this->startTime = @$data['start_time'];
        $this->endTime = @$data['end_time'];
        $this->name = @$data['name'];
        $this->description = @$data['description'];
        $this->cover = @$data['cover']['source'];
        $this->place = @$data['place']['name'];
        $this->state = @$data['place']['location']['state'];
        $this->city = @$data['place']['location']['city'];
        $this->street = @$data['place']['location']['street'];
        $this->zip = @$data['place']['location']['zip'];
        $this->latitude = @$data['place']['location']['latitude'];
        $this->longitude = @$data['place']['location']['longitude'];
    }

    /**
     * @param $pageId
     * @return array
     */
    public function getListOwnPage($pageId)
    {
        $conn = $this->conn();
        $query = $conn->prepare("SELECT * FROM {$this->tableName} WHERE {$this->columnFacebookPageId} = :pageId");
        //$query->bindColumn(':columnId', $columnId);
        $query->bindParam(':pageId', $pageId);

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
            'facebookEventId' => $this->facebookEventId,
            'columnFacebookPageId' => $this->columnFacebookPageId,
            'facebookEventUpdateTime' => $this->facebookEventUpdateTime,
            'facebookPlaceId' => $this->facebookPlaceId,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'name' => $this->name,
            'description' => $this->description,
            'cover' => $this->cover,
            'place' => $this->place,
            'state' => $this->state,
            'city' => $this->city,
            'street' => $this->street,
            'zip' => $this->zip,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * @return mixed
     */
    public function save()
    {
        $createOrUpdate = '';
        $existEvent = $this->verify($this->facebookEventId, 'event', $this->columnFacebookEventId);
        if($existEvent == false)
        {
            $createOrUpdate = 'create';
        }
        else if ($existEvent[$this->columnFacebookEventUpdateTime] != $this->facebookEventUpdateTime)
        {
            $createOrUpdate = 'update';
        }


        $conn = $this->conn();

        // Cria um novo espaço
        $event = $this->eventSave($conn, $createOrUpdate);

        // Vincula informações deste evento
        //$eventData = [];
        //$eventData[] = $this->insertIntoData($conn, $createOrUpdate, 'event_meta', 'facebookEventId', $event['id'], $this->facebookEventId);
        //$eventData[] = $this->insertIntoData($conn, $createOrUpdate, 'event_meta', 'facebookPageId', $event['id'], $this->facebookPageId);
        //$eventData[] = $this->insertIntoData($conn, $createOrUpdate, 'event_meta', 'facebookEventUpdateTime', $event['id'], $this->facebookEventUpdateTime);

        // Cria um novo espaço
        $space = $this->spaceSave($conn, $createOrUpdate);

        // Cria um novo espaço
        $eventOccurrence = $this->eventOccurrenceSave($conn, $createOrUpdate, $space['id'], $event['id'], [
            'spaceId' => $space['id'],
            'startsAt' => date_format(date_create($this->startTime),"H:i"),
            'duration' => 0,
            'endsAt' => date_format(date_create($this->endTime),"H:i"),
            'frequency' => 'once',
            'startsOn' => date_format(date_create($this->startTime),"Y-m-d"),
            'until' => "",
            "description" => date_format(date_create($this->startTime),"d \\d\\e F \\d\\e Y \\a\\s H:i"),
            "price" => ""

        ]);

        return [
            'event' => $event,
            //'eventData' => $eventData,
            'eventOccurrence' => $eventOccurrence,
            'space' => $space,
        ];
    }


    /**
     * @param $conn
     * @param $createOrUpdate
     * @return mixed
     */
    protected function eventSave($conn, $createOrUpdate)
    {
        // Cria um novo evento
        $sqlEventInsert = "INSERT INTO event (name, short_description, create_timestamp, status, agent_id, type, {$this->columnFacebookEventId}, {$this->columnFacebookPageId}, {$this->columnFacebookEventUpdateTime}) VALUES ({$this->name}, {$this->description}, NOW(), 1, {$this->userId}, 1, {$this->facebookEventId}, {$this->facebookPageId}, {$this->facebookEventUpdateTime})";
        // Atualiza um existente
        $sqlEventUpdate = "UPDATE event SET  name = :name, short_description = :short_description,  {$this->columnFacebookEventUpdateTime} = :facebookEventUpdateTime  WHERE {$this->columnFacebookEventId} =  :facebookEventId";


        $event = $conn->prepare($createOrUpdate == 'create' ? $sqlEventInsert : $sqlEventUpdate);
        //$event->bindParam(':name', $this->name);
        //$event->bindParam(':short_description', $this->description);
        //$event->bindParam(':facebookEventUpdateTime', $this->facebookEventUpdateTime);


        //if($createOrUpdate == 'create') $event->bindParam(':agent_id', $this->userId);
        //if($createOrUpdate == 'create') $event->bindParam(':facebookEventId', $this->facebookEventId);
        //if($createOrUpdate == 'create') $event->bindParam(':facebookPageId', $this->facebookPageId);

        $event->execute();
        $event->setFetchMode(\PDO::FETCH_ASSOC);

        return  $event->fetch();
    }

    /**
     * @param $conn
     * @param $createOrUpdate
     * @return mixed
     */
    protected function spaceSave($conn, $createOrUpdate)
    {

        $existPlace = $this->verify($this->facebookPlaceId, 'space', $this->columnFacebookPlaceId);

        if($existPlace == false)
        {
            $existPlacePerName = $this->verify($this->place, 'space', 'name');

            if($existPlacePerName == false)
            {
                $latLng = "'(".$this->longitude.",".$this->latitude.")'::point";

                // Cria um novo espaço
                $sqlSpace = "INSERT INTO public.space( location, name,create_timestamp, status, type, agent_id, is_verified, public, {$this->columnFacebookPlaceId}) VALUES ({$latLng}, {$this->place}, NOW(), 1, {$this->userId}, 1, true,true, {$this->facebookPlaceId});";
                $space = $conn->prepare($sqlSpace);
                //$space->bindParam(':location', $latLng);
                //$space->bindParam(':place', $this->place);
                //$space->bindParam(':agent_id', $this->userId);
                //$space->bindParam(':facebook_place_id', $this->facebookPlaceId);
                $space->execute();
                $space->setFetchMode(\PDO::FETCH_ASSOC);

                return $space->fetch();
            }
            else
            {
                $sqlSpaceUpdate = "UPDATE public.space SET {$this->columnFacebookPlaceId} = {$this->facebookPlaceId} WHERE id = {$existPlacePerName['id']}";
                $spaceUpdate = $conn->prepare($sqlSpaceUpdate);
                //$spaceUpdate->bindParam(':facebook_place_id', $this->facebookPlaceId);
                //$spaceUpdate->bindParam(':id', $existPlacePerName['id']);
                $spaceUpdate->execute();
                $spaceUpdate->setFetchMode(\PDO::FETCH_ASSOC);

                return $spaceUpdate->fetch();
            }

        }
        else return $existPlace;

    }

    /**
     * @param $conn
     * @param $createOrUpdate
     * @param $spaceId
     * @param $eventId
     * @param $eventOccurrenceRule
     * @return mixed
     */
    protected function eventOccurrenceSave($conn, $createOrUpdate, $spaceId, $eventId, $eventOccurrenceRule)
    {
        // Cria um novo espaço
        $sqlEventOccurrenceInsert = "INSERT INTO public.event_occurrence( space_id, event_id,  rule, starts_on, ends_on, starts_at, ends_at, frequency) VALUES ({$spaceId}, {$eventId}, {json_encode($eventOccurrenceRule)}, {$this->startTime}, {$this->endTime}, {$this->startTime}, {$this->endTime}, 'once');";
        // Atualiza um existente
        $sqlEventOccurrenceUpdate = "UPDATE public.event_occurrence SET rule = {json_encode($eventOccurrenceRule)}, starts_on = {$this->startTime}, ends_on = {$this->endTime}, starts_at = {$this->startTime},ends_at = {$this->endTime} WHERE space_id = {$spaceId} AND event_id = {$eventId}";

        $eventOccurrence = $conn->prepare($createOrUpdate == 'create' ? $sqlEventOccurrenceInsert : $sqlEventOccurrenceUpdate);
        /*$eventOccurrence->bindParam(':space_id', $spaceId);
        $eventOccurrence->bindParam(':event_id', $eventId);
        $eventOccurrence->bindParam(':rule', json_encode($eventOccurrenceRule));
        $eventOccurrence->bindParam(':starts_on', $this->startTime);
        $eventOccurrence->bindParam(':ends_on', $this->endTime);
        $eventOccurrence->bindParam(':starts_at', $this->startTime);
        $eventOccurrence->bindParam(':ends_at', $this->endTime);
        */
        $eventOccurrence->execute();
        $eventOccurrence->setFetchMode(\PDO::FETCH_ASSOC);

        return $eventOccurrence->fetch();
    }

    /**
     * @param $conn
     * @param $table
     * @param $key
     * @param $object_id
     * @param $value
     * @return mixed
     */
    /*protected function insertIntoData($conn, $table, $key, $object_id, $value)
    {
        $sqlEventMeta = "INSERT INTO {$table} (key, object_id, value) VALUES (:key, :object_id, :value)";
        $eventMeta = $conn->prepare($sqlEventMeta);
        $eventMeta->bindParam(':key', $key);
        $eventMeta->bindParam(':object_id', $object_id);
        $eventMeta->bindParam(':value', $value);
        $eventMeta->execute();

        $eventMeta->setFetchMode(\PDO::FETCH_ASSOC);

        return $eventMeta->fetch();
    }*/
}