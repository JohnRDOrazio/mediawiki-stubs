<?php

require 'vendor/autoload.php';

use Johnrdorazio\MediaWikiStubs\StubGenerator;

$generator = new StubGenerator();
$generator->generateStubs('./mediawiki-core');
$generator->saveStubs('./stubs');
