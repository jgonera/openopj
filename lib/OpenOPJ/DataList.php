<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');
require_once('DataSection.php');

class DataList extends Section {
    public $data = array();

    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            $dataSection = new DataSection($this->file);
            $this->data[$dataSection->name] = $dataSection->data;
        }
    }
}

?>
