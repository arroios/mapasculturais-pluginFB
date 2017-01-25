<?php

namespace arroios\plugins\models;

Class Event extends _base
{
    public $id;
    public $updateTime;
    public $pageId;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->updateTime = $data['updateTime'];
        $this->pageId = $data['pageId'];
    }

}