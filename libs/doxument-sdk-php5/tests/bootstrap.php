<?php 
$paths = array(
	get_include_path(),
	dirname(__FILE__).'/../src/',
	dirname(__FILE__).'/',
);

set_include_path(implode(PATH_SEPARATOR, $paths));

require_once 'Doxument/Loader.php';
spl_autoload_register(array('Doxument_Loader', 'autoload'));

//$apiKey = '9fb4e95127dff53b7d5a82ccca09fb95';
//$apiToken = '9c43482642a094cb8657fbee4d9b7660737b32c2';
$apiKey = '3c887bd87180e4def0bf65698cd40afb';
$apiToken = '4577c1a4b555e89adf980d7e65198d909e006730';

$client = new Doxument_Api_Client($apiKey, $apiToken);
$client->setProxy('localhost:8888'); // comment out this line if you don't use proxy
$client->setEndpoint('http://api.doxument.localhost:8080/v1');
