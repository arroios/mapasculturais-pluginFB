<?php

require_once @$argv[1]."/mapasculturais/src/protected/vendor/autoload.php";

require __DIR__ . '/vendor/autoload.php';

define('BASE_PATH', realpath(__DIR__.'/../src') . '/');
define('PROTECTED_PATH', BASE_PATH . 'protected/');
define('APPLICATION_PATH', PROTECTED_PATH . 'application/');
define('THEMES_PATH', APPLICATION_PATH . 'themes/');
define('ACTIVE_THEME_PATH',  THEMES_PATH . 'active/');
define('PLUGINS_PATH', APPLICATION_PATH.'/plugins/');
define('LANGUAGES_PATH', APPLICATION_PATH . 'translations/');

$config = include @$argv[1].'/mapasculturais/src/protected/application/conf/config.php';

$conn = \Doctrine\DBAL\DriverManager::getConnection(array_merge($config['doctrine.database'], ['driver' => 'pdo_pgsql']), new \Doctrine\DBAL\Configuration());


(new \arroios\plugins\ImportEvent($config['arroios.plugin']['import.facebook'], NULL, $conn))->cronJob();
