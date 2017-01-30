<?php

require __DIR__ . '/../vendor/autoload.php';

use arroios\plugins\ImportEvent;

$ImportEvent = new ImportEvent([
    'facebook_id' => '1416858661954903',
    'facebook_secret' => 'c072998db2067b2f14b0c87e0a542bd4',
    'Event' => // Your Event Table
    [
        'tableName' => 'Event',
        'columnFacebookEventId' => 'facebookEventId', // New table
        'columnFacebookPageId' => 'facebookPageId', // New table
        'columnFacebookEventUpdateTime' => 'facebookEventUpdateTime', // New table
        'columnStartTime' => 'startTime',
        'columnEndTime' => 'endTime',
        'columnName' => 'name',
        'columnDescription' => 'description',
        'columnCover' => 'cover',
        'columnPlace' => 'place',
        'columnState' => 'state',
        'columnCity' => 'city',
        'columnStreet' => 'street',
        'columnZip' => 'zip',
        'columnLatitude' => 'latitude',
        'columnLongitude' => 'longitude',
    ],
    'Page' => // New Table
    [
        'tableName' => 'Page',
        'columnFacebookPageId' => 'facebookPageId',
        'columnFacebookToken' => 'facebookToken',
        'columnFacebookPageName' => 'facebookPageName',
    ]
]);

$ImportEvent->getHtml();

print '<br><br><br><pre>';

foreach ($ImportEvent->modelPage->getList() as $page)
{
    print 'Page - '.$page[$ImportEvent->modelPage->columnFacebookPageName].'<br>';
    print '----<br>';

    foreach ($ImportEvent->modelEvent->getListOwnPage($page[$ImportEvent->modelPage->columnFacebookPageId]) as $event)
    {
        print 'Event - '.$event[$ImportEvent->modelEvent->columnName].' - '.$event[$ImportEvent->modelEvent->columnFacebookEventUpdateTime].' <br>';
    }

    print '<br><br>==============================<br><br>';
}
print '</pre>';
//print '<pre>';
//print_r($ImportEvent->modelPage->getList());

//print_r($ImportEvent->modelEvent->getListOwnPage(1));

?>

