<?php
require_once('OPJFile.php');

if (getenv('LOG')) {
    OpenOPJ\Logger::addHandler(function($msg) {
        file_put_contents('php://stderr', $msg);
    });
}
?>
