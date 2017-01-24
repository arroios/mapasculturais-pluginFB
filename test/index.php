<?php

require __DIR__ . '/../vendor/autoload.php';

use arroios\plugins\ImportEvent;

$ImportEvent = new ImportEvent([
    'facebook_id' => '1416858661954903',
    'facebook_secret' => 'c072998db2067b2f14b0c87e0a542bd4',
    'debug' => true
]);

$ImportEvent->getHtml();


?>


<style>
    .arroios-bt {padding: 5px 10px; background: #3b5998; color: #fff; cursor: pointer; font-family: Arial }
</style>
