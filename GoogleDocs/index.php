<?php

require_once 'Zend/Loader.php'; Zend_Loader::loadClass('Zend_Gdata_Docs');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

$service = Zend_Gdata_Docs::AUTH_SERVICE_NAME; $client =
Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service); $docs = new
Zend_Gdata_Docs($client); $feed =
$docs->getDocumentListFeed('https://docs.google.com/feeds/documents/private/full/-/document');

foreach($feed->entries as $entry) {

  $sessionToken = $entry->service->getHttpClient()->getClientLoginToken();
  $key = explode('document%3A', $entry->getId()->getText());
  $filename = 'https://docs.google.com/feeds/download/documents/Export?docID=' . $key[1] . '&exportFormat=pdf&format=pdf';
  $opts = array('http' => array('method' => 'GET',
				'header' => "GData-Version: 3.0\r\n".
				"User-Agent:MyCompany-MyApp-1.0 Zend_Framework_Gdata/1.11.11\r\n".
				"Authorization:GoogleLogin auth=$sessionToken\r\n".
				"Accept-encoding:identity\r\n"
				)
		);
  header('Content-type: application/pdf');
  header('Content-Disposition: attachment; filename="downloaded.pdf"');
  $data = file_get_contents($filename, false, stream_context_create($opts));
  echo $data;
}
