<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');

class ParametersSection extends Section {
    const PARAMETERS_END = "\0";
    public $parameters = array();

    protected function parse() {
        while (true) {
            $name = rtrim($this->file->readLine(), "\n");
            if ($name === self::PARAMETERS_END) break;
            list(, $value) = unpack('d', $this->file->read(8 + 1));
            $this->parameters[$name] = $value;
            Logger::log("Parameter %s: %f", $name, $value);
        }
    }
}

?>
