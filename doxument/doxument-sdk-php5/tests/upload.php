<?php
require_once dirname(__FILE__).'/bootstrap.php';
 
try {
	$data = $client->upload('./data/test.txt');
	if($data['success'] == 'true') {
		echo "upload OK\n";
	}
	print_r($data);
	
} catch(Doxument_Api_Exception $ex) {
	die($ex);
}
