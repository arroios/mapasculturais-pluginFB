<?php

namespace arroios\plugins\models;

Class Page extends _base
{
    public $id;
    public $userId;
    public $token;
    public $name;

    public function __construct($data, $userId)
    {
        $this->id = $data['id'];
        $this->userId = $userId;
        $this->token = $data['access_token'];
        $this->name = $data['name'];
    }


}