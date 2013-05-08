<?php
$srcRoot = "src";
$buildRoot = "build";
$app = "citysdk-tourism.phar";

unlink($buildRoot . "/" . $app);

$phar = new Phar($buildRoot . "/" . $app, 
	FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $app);
$phar["TourismClient.php"] = file_get_contents($srcRoot . "/TourismClient.php");
$phar["TourismExceptions.php"] = file_get_contents($srcRoot . "/TourismExceptions.php");
$phar["DataReader.php"] = file_get_contents($srcRoot . "/DataReader.php");
$phar["Request.php"] = file_get_contents($srcRoot . "/Request.php");
$phar["UriTemplate.php"] = file_get_contents($srcRoot . "/UriTemplate.php");
$phar["Operators.php"] = file_get_contents($srcRoot . "/Operators.php");
$phar->setStub($phar->createDefaultStub("index.php"));

print 'Build done'."\n";
?>