<?php

if (!is_file('configure_me.php')) {
    echo 'Cannot find configure_me.php. Please create it starting from configure_me.dist.php';
    exit;
}

$fileToUpload = $argv[1];
echo $fileToUpload;
echo $user;
echo $pass;

require_once 'configure_me.php';
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_Docs');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

$service = Zend_Gdata_Docs::AUTH_SERVICE_NAME;
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
$docs = new Zend_Gdata_Docs($client);

$fileToUpload = 'sample.doc';
$originalFileName = 'sample.doc';
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



