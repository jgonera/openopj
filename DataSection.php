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
        $size = $this->file->readSizeBlock();
        if ($size < 113) {
            Logger::log("Unexpected data header block size: $size");
            $this->file->seek($size + 1);
            return false;
        }
        $this->file->seek(22);
        $this->header = unpack(
            'vdataType/CdataType2/VtotalRows/VfirstRow/VlastRow',
            $this->file->read(15)
        );
        $this->file->seek(24);
        $this->header += unpack(
            'CvalueSize/x/CdataTypeU',
            $this->file->read(3)
        );
        $this->file->seek(24);
        $this->header += unpack('a25name', $this->file->read(25));
        $this->name = $this->header['name'];
        $this->file->seek(10);
        $this->file->seek(1);
        Logger::log("Data section name: $this->name");
        print_r($this->header);
    }

    protected function parseDataContent() {

    }
            
}

?>
