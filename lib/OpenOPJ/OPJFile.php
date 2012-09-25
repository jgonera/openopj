<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');
require_once('DataList.php');
require_once('WindowList.php');
require_once('ParametersSection.php');
require_once('NoteList.php');

class OPJFile {
    public $signature = array(), $header = array(), $data, $parameters, $notes;

    public function __construct($reader) {
        $this->file = new OPJReader($reader);
        $this->parse();
        unset($this->file);
    }

    protected function parse() {
        $this->parseSignature();
        $this->parseHeader();
        $this->parseDataList();
        $this->parseWindowList();
        $this->parseParametersSection();
        $this->parseNoteList();
    }

    protected function parseSignature() {
        $tmp = explode(' ', $this->file->readLine());
        if ($tmp[0] !== 'CPYA') {
            throw new WrongSignatureError("File doesn't seem to be an OPJ file");
        }
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
        if ($block->size() >= 39) {
            $this->header = unpack('doriginVersion', $block->slice(0x1B, 8));
            Logger::log("Origin version: %s", $this->header['originVersion']);
        } else {
            Logger::log("Unexpected header size: %d, skipping", $block->size());
        }
        $this->file->readBlock();
    }

    protected function parseDataList() {
        $dataList = new DataList($this->file);
        $this->data = $dataList->data;
    }

    protected function parseWindowList() {
        // not implemented (only window names), skips to the next section
        $windowList = new WindowList($this->file);
        $this->windows = $windowList->windows;
    }

    protected function parseParametersSection() {
        $parametersSection = new ParametersSection($this->file);
        $this->parameters = $parametersSection->parameters;
    }

    protected function parseNoteList() {
        $noteList = new NoteList($this->file);
        $this->notes = $noteList->notes;
    }
}
?>
