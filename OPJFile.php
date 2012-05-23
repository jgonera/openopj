<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');
require_once('DataList.php');

class OPJFile {
    public $signature = array(), $header = array(), $data = array();

    public function __construct($fileName) {
        $this->file = new OPJReader($fileName);
        $this->parse();
        unset($this->file);
    }

    protected function parse() {
        $this->parseSignature();
        $this->parseHeader();
        $this->parseDataList();
        $this->parseWindowList();
    }

    protected function parseSignature() {
        $tmp = explode(' ', $this->file->readLine());
        $this->signature['id'] = $tmp[0];
        $this->signature['version'] = $tmp[1];
        $this->signature['build'] = (int)$tmp[2];
        Logger::log(
            "OPJ version: %s, build: %d",
            $this->signature['version'],
            $this->signature['build']
        );
    }

    protected function parseHeader() {
        $block = $this->file->readBlock();
        if ($block->size() === 39) {
            $this->header = unpack('doriginVersion', $block->slice(0x1B, 8));
            Logger::log("Origin version: %s", $this->header['originVersion']);
        } else {
            Logger::log("Unexpected header size: %d, skipping", $block->size());
        }
        $this->file->findSectionEnd();
    }

    protected function parseDataList() {
        $dataList = new DataList($this->file);
        $this->data = $dataList->data;
    }

    protected function parseWindowList() {

    }
}
?>
