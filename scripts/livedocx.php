<?php

if (empty($argv[4])) {
    echo 'Usage: php livedocx.php apiKey apiToken source_path_file.doc dest_path_file.pdf';
    exit;
}

$username = $argv[1];
$password = $argv[2];
$file_doc = $argv[3];
$file_pdf = $argv[4];

$paths = array(
	get_include_path(),
	dirname(__FILE__).'/../libs/',
);

set_include_path(implode(PATH_SEPARATOR, $paths));

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Service_LiveDocx_MailMerge');

$mailMerge = new Zend_Service_LiveDocx_MailMerge();
 
$mailMerge->setUsername($username)
          ->setPassword($password);
 
$mailMerge->setLocalTemplate($file_doc);
 
// necessary as of LiveDocx 1.2
$mailMerge->assign('dummyFieldName', 'dummyFieldValue');
 
$mailMerge->createDocument();
 
$document = $mailMerge->retrieveDocument('pdf');
 
file_put_contents($file_pdf, $document);
 
unset($mailMerge);