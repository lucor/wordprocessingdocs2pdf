<?php
require_once dirname(__FILE__).'/bootstrap.php';

try {
	$data = $client->findAll();
	print_r($data);
} catch(Doxument_Api_Exception $ex) {
	die($ex);
}
