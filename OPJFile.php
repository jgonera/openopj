<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');
require_once('DataList.php');

class OPJFile {
    public $signature = array(), $header = array(), $data = array();

    function __construct($fileName) {
        $this->file = new OPJReader($fileName);
        $this->parse();
        unset($this->file);
    }

    protected function parse() {
        $this->parseSignature();
        $this->parseHeader();
        $this->parseDataList();
        //try {
            //$this->parseUnknown();
        //} catch (\Exception $e) {
        //}
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
        $size = $this->file->readSizeBlock();
        if ($size === 39) {
            $this->file->seek(27);
            $this->header = unpack('doriginVersion', $this->file->read(8));
            $this->file->seek(4 + 1);
            Logger::log("Origin version: %s", $this->header['originVersion']);
        } else {
            Logger::log("Unexpected header size: $size, skipping");
            $this->file->seek($size + 1);
        }
        $this->file->findSectionEnd();
    }

    protected function parseDataList() {
        $dataList = new DataList($this->file);
        $this->data = $dataList->data;
    }
}
?>
