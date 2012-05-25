<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');

class NoteSection extends Section {
    public $name, $contents;

    protected function parse() {
        $this->file->readBlock();
        $nameBlock = $this->file->readBlock();
        $this->name = rtrim($nameBlock->data, "\0");
        $contentsBlock = $this->file->readBlock();
        $this->contents = rtrim($contentsBlock->data, "\0");
        Logger::log("Note %s:\n%s", $this->name, $this->contents);
    }
}

?>
