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
    public $columnFacebookEventId = 'facebook_event_id';
    public $columnFacebookPageId = 'facebook_page_id';
    public $columnFacebookEventUpdateTime = 'facebook_event_update_time';
    public $columnFacebookPlaceId = 'facebook_place_id';
    public $columnStartTime = 'start_time';
    public $columnEndTime = 'end_time';
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
        if(isset($config['columnFacebookPlaceId'])) $this->columnFacebookPlaceId = $config['columnFacebookPlaceId'];
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
        $this->name = pg_escape_string(@$data['name']);
        $this->description = addslashes(@$data['description']);
        $this->cover = @$data['cover']['source'];
        $this->place = pg_escape_string(@$data['place']['name']);
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

        $conn = $this->conn();

        if($this->latitude != '' || $this->longitude != '')
        {
            // Cria um novo espaço
            $space = $this->spaceSave($conn);

            // Cria um novo espaço
            $event = $this->eventSave($conn);

            $eventOccurrence = false;

            if(count($space) > 0)
            {
                // Cria um novo espaço
                $eventOccurrence = $this->eventOccurrenceSave($conn, $space['id'], $event['id'], [
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
            }
            return [
                'space' => $space,
                'event' => $event,
                'eventOccurrence' => $eventOccurrence
            ];
        }
        else return false;
    }


    /**
     * @param $conn
     * @return mixed
     */
    protected function eventSave($conn)
    {

        $existEvent = $this->verify($this->facebookEventId, 'event', $this->columnFacebookEventId);
        if($existEvent == false)
        {
            $sqlEvent = "INSERT INTO event (name, short_description, create_timestamp, status, agent_id, type, {$this->columnFacebookEventId}, {$this->columnFacebookPageId}, {$this->columnFacebookEventUpdateTime}) VALUES (:name, :short_description, NOW(), 1, :agent_id, 1, :facebookEventId, :facebookPageId, :facebookEventUpdateTime)";
        }
        else if ($existEvent[$this->columnFacebookEventUpdateTime] != $this->facebookEventUpdateTime)
        {
            $sqlEvent = "UPDATE event SET  name = :name, short_description = :short_description,  {$this->columnFacebookEventUpdateTime} = :facebookEventUpdateTime  WHERE {$this->columnFacebookEventId} =  :facebookEventId";
        }
        else return $existEvent;


        $event = $conn->prepare($sqlEvent);

        $event->bindParam(':name', $this->name);
        $event->bindParam(':short_description', $this->description);
        $event->bindParam(':facebookEventUpdateTime', $this->facebookEventUpdateTime);
        $event->bindParam(':facebookEventId', $this->facebookEventId);
        if($existEvent == false) $event->bindParam(':agent_id', $this->userId);
        if($existEvent == false) $event->bindParam(':facebookPageId', $this->facebookPageId);

        $event->execute();

        $eventId =  ($existEvent == false ? $conn->lastInsertId('event_id_seq') : $event->fetch(\PDO::FETCH_ASSOC)['id']) ;

        if($existEvent == false)
        {
            $sqlFile = 'INSERT INTO file (md5, mime_type, name, object_type, object_id, create_timestamp, grp) VALUES (:md5, \'image/jpeg\', :name, \'MapasCulturais\Entities\Event\', :object_id, \'NOW()\', \'header\')';
            $fileName = $this->publishImageOriginal($eventId, $this->cover).'.jpg';
            $md5File = md5(microtime());


            $file = $conn->prepare($sqlFile);
            $file->bindParam(':md5', $md5File);
            $file->bindParam(':name', $fileName);
            $file->bindParam(':object_id', $eventId);
            $file->execute();

            $fileId = $conn->lastInsertId('file_id_seq');


            $sqlFile = 'INSERT INTO file (md5, mime_type, name, object_type, object_id, create_timestamp, grp, parent_id) VALUES (:md5, \'image/jpeg\', :name, \'MapasCulturais\Entities\Event\', :object_id, \'NOW()\', \'img:header\', :parent_id)';
            $fileNameCrop = $this->publishImageCrop($eventId, $fileId, $fileName).'.jpg';
            $md5FileCrop = md5(microtime());

            $fileCrop = $conn->prepare($sqlFile);
            $fileCrop->bindParam(':md5', $md5FileCrop);
            $fileCrop->bindParam(':name', $fileNameCrop);
            $fileCrop->bindParam(':object_id', $eventId);
            $fileCrop->bindParam(':parent_id', $fileId);
            $fileCrop->execute();

        }
    }

    /**
     * @param $conn
     * @return mixed
     */
    protected function spaceSave($conn)
    {

        $existPlace = $this->verify($this->facebookPlaceId, 'space', $this->columnFacebookPlaceId);

        if($existPlace == false)
        {
            $existPlacePerName = $this->verify($this->place, 'space', 'name');

            if($existPlacePerName == false)
            {
                $latLng = "(".$this->longitude.",".$this->latitude.")";

                // Cria um novo espaço
                $sqlSpace = "INSERT INTO public.space( location, name,create_timestamp, status, type, agent_id, is_verified, public, {$this->columnFacebookPlaceId}) VALUES (:location::point, :place, NOW(), 1,  1, :agent_id, true,true, :facebook_place_id);";

                $space = $conn->prepare($sqlSpace);
                $space->bindParam(':location', $latLng);
                $space->bindParam(':place', $this->place);
                $space->bindParam(':agent_id', $this->userId);
                $space->bindParam(':facebook_place_id', $this->facebookPlaceId);

                $space->execute();

                $spaceId = $conn->lastInsertId('space_id_seq');

                $this->insertIntoData($conn, 'space_meta', 'En_CEP', $spaceId, $this->zip);
                $this->insertIntoData($conn, 'space_meta', 'En_Nome_Logradouro', $spaceId, $this->street);
                $this->insertIntoData($conn, 'space_meta', 'En_Municipio', $spaceId, $this->city);
                $this->insertIntoData($conn, 'space_meta', 'En_Estado', $spaceId, $this->place);

                return ['id' => $spaceId];
            }
            else
            {
                $sqlSpaceUpdate = "UPDATE public.space SET {$this->columnFacebookPlaceId} = :facebook_place_id WHERE id = :id";
                $spaceUpdate = $conn->prepare($sqlSpaceUpdate);
                $spaceUpdate->bindParam(':facebook_place_id', $this->facebookPlaceId);
                $spaceUpdate->bindParam(':id', $existPlacePerName['id']);
                $spaceUpdate->execute();
                $spaceUpdate->setFetchMode(\PDO::FETCH_ASSOC);

                return $spaceUpdate->fetch();
            }

        }
        else return $existPlace;

    }

    /**
     * @param $conn
     * @param $spaceId
     * @param $eventId
     * @param $eventOccurrenceRule
     * @return mixed
     */
    protected function eventOccurrenceSave($conn, $spaceId, $eventId, $eventOccurrenceRule)
    {
        $exist = $this->verify($eventId, 'event_occurrence', 'event_id');

        if($exist == false)
        {
            $sqlEventOccurrence = "INSERT INTO public.event_occurrence( space_id, event_id,  rule, starts_on, ends_on, starts_at, ends_at, frequency) VALUES (:space_id, :event_id, :rule, :starts_on, :ends_on, :starts_at, :ends_at, 'once');";
        }
        else
        {
            $sqlEventOccurrence = "UPDATE public.event_occurrence SET rule = :rule, starts_on = :starts_on, ends_on = :ends_on, starts_at = :starts_at,ends_at = :ends_at WHERE space_id = :space_id AND event_id = :event_id";
        }


        $eventOccurrence = $conn->prepare($sqlEventOccurrence);

        $eventOccurrence->bindParam(':space_id', $spaceId);
        $eventOccurrence->bindParam(':event_id', $eventId);
        $eventOccurrence->bindParam(':rule', json_encode($eventOccurrenceRule));
        $eventOccurrence->bindParam(':starts_on', $this->startTime);
        $eventOccurrence->bindParam(':ends_on', $this->endTime);
        $eventOccurrence->bindParam(':starts_at', $this->startTime);
        $eventOccurrence->bindParam(':ends_at', $this->endTime);


        $eventOccurrence->execute();
        $eventOccurrence->setFetchMode(\PDO::FETCH_ASSOC);

        return $exist == false ? ['id' => $conn->lastInsertId('event_occurrence_id_seq')] : $eventOccurrence->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $conn
     * @param $table
     * @param $key
     * @param $object_id
     * @param $value
     * @return mixed
     */
    protected function insertIntoData($conn, $table, $key, $object_id, $value)
    {
        $sqlEventMeta = "INSERT INTO {$table} (key, object_id, value) VALUES (:key, :object_id, :value)";
        $eventMeta = $conn->prepare($sqlEventMeta);
        $eventMeta->bindParam(':key', $key);
        $eventMeta->bindParam(':object_id', $object_id);
        $eventMeta->bindParam(':value', $value);
        $eventMeta->execute();

        $eventMeta->setFetchMode(\PDO::FETCH_ASSOC);

        return true;
    }

    protected function publishImageOriginal($eventId, $link)
    {
        $filenameHash = md5(microtime().$link);
        @mkdir("/mapasculturais/src/files/event/".$eventId);
        @copy($link, '/mapasculturais/src/files/event/'.$eventId.'/'.$filenameHash.'.jpg');

        return $filenameHash;
    }

    protected function publishImageCrop($eventId, $fileId, $fileName )
    {
        $filenameHash = md5(microtime().$fileName);
        @mkdir("/mapasculturais/src/files/event/".$eventId.'/file');
        @mkdir("/mapasculturais/src/files/event/".$eventId.'/file/'.$fileId);
        $image = imagecreatefromjpeg('/mapasculturais/src/files/event/'.$eventId.'/'.$fileName);
        $filename = '/mapasculturais/src/files/event/'.$eventId.'/file/'.$fileId.'/'.$filenameHash.'.jpg';
        $thumb_width = 1188;
        $thumb_height = 192;
        $width = imagesx($image);
        $height = imagesy($image);

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;
        if ( $original_aspect >= $thumb_aspect )
        {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        }
        else
        {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

        // Resize and crop
        imagecopyresampled($thumb,
            $image,
            0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_height) / 2, // Center the image vertically
            0, 0,
            $new_width, $new_height,
            $width, $height);

        @copy(imagejpeg($thumb, $filename, 100), $filename);

        return $filenameHash;
    }
}