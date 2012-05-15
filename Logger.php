<?php
namespace OpenOPJ;

class Logger {
    protected static $handlers = array();

    public static function addHandler($handler) {
        self::$handlers[] = $handler;
    }

    public static function log() {
        foreach (self::$handlers as $handler) {
            $args = func_get_args();
            $args[0] .= "\n";
            $msg = call_user_func_array('sprintf', $args);
            $handler($msg);
        }
    }
}
?>
