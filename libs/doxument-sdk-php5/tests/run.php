<?php
require_once dirname(__FILE__).'/bootstrap.php';
require_once 'TestSuite.php';

$testSuite = new TestSuite($client);
$testSuite->runAll();