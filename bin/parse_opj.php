<?php
require_once('lib/OpenOPJ.php');

use OpenOPJ\OPJFile, OpenOPJ\FileReader;

OpenOPJ\Logger::addHandler(function($msg) { echo $msg; });
$opj = new OPJFile(new FileReader($argv[1]));
//var_dump($opj);

?>
