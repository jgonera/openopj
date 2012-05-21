<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');
require_once('DataSection.php');

class DataList extends Section {
    public $data = array();

    protected function parse() {
        Logger::log("Data list at 0x%X", $this->file->offset());
        while ($this->file->peekSizeBlock()) {
            $dataSection = new DataSection($this->file);
            $this->data[$dataSection->name] = $dataSection->data;
        }
        $this->file->findSectionEnd();
    }
            
}

?>
