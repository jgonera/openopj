<?php
require_once('lib/OpenOPJ/OPJFile.php');

use OpenOPJ\OPJFile;

OpenOPJ\Logger::addHandler(function($msg) { echo $msg; });
$opj = new OPJFile($argv[1]);
//var_dump($opj);

?>
