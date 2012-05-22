<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');

class DataSection extends Section {
    public $name, $data = array();
    protected $header = array();

    protected function parse() {
        Logger::log("Data section at 0x%X", $this->file->offset());
        if ($this->parseDataHeader()) {
            $this->parseDataContent();
        }
        $this->file->findSectionEnd();
    }

    protected function parseDataHeader() {
        $block = $this->file->readBlock();
        if ($block->size() < 113) {
            Logger::log("Unexpected data header block size: $size");
            return false;
        }
        $this->header = unpack(
            'vdataType/CdataType2/VtotalRows/VfirstRow/VlastRow',
            $block->slice(0x16, 15)
        );
        $this->header += unpack('CvalueSize/x/CdataTypeU', $block->slice(0x3D, 3));
        $this->header += unpack('a25name', $block->slice(0x58, 25));
        $this->name = $this->header['name'];
        Logger::log("Data section name: $this->name");
        print_r($this->header);
    }

    protected function parseDataContent() {

    }
            
}

?>
