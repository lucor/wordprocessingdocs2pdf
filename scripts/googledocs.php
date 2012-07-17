<?php

if (empty($argv[3])) {
    echo 'Usage: php convert.php username password source_path_file.doc > dest_path_file.pdf';
    exit;
}

$username = $argv[1];
$password = $argv[2];
$fileToUpload = $argv[3];

$paths = array(
	get_include_path(),
	dirname(__FILE__).'/../libs/',
);

set_include_path(implode(PATH_SEPARATOR, $paths));

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_Docs');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

$service = Zend_Gdata_Docs::AUTH_SERVICE_NAME;
$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
$docs = new Zend_Gdata_Docs($client);

$newDocumentEntry = $docs->uploadFile($fileToUpload, null, null, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI);

$sessionToken = $newDocumentEntry->service->getHttpClient()->getClientLoginToken();
$filename = $newDocumentEntry->getContent()->getSrc() . '&exportFormat=pdf&format=pdf';

$opts = array(
  'http' => array(
  'method' => 'GET',
  'header' => "GData-Version: 3.0\r\n".
              "User-Agent: MyCompany-MyApp-1.0 Zend_Framework_Gdata/1.11.11\r\n".
              "Authorization: GoogleLogin auth=$sessionToken\r\n".
              "Accept-encoding: identity\r\n"
              )
);
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="' . $fileToUpload . '.pdf"');
$data = file_get_contents($filename, false, stream_context_create($opts));
echo $data;
