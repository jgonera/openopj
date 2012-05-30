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
            $msg = '';
            if (is_string($args[0])) {
                $args[0] .= "\n";
                $msg = call_user_func_array('sprintf', $args);
            } else if (is_array($args[0])) {
                foreach ($args[0] as $key => $value) {
                    $msg .= "$key: $value\n";
                }
            }
            $handler($msg);
        }
    }
}
?>
