<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');
require_once('NoteSection.php');

class NoteList extends Section {
    public $notes = array();

    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            $noteSection = new NoteSection($this->file);
            $this->notes[$noteSection->name] = $noteSection->contents;
        }
    }
}

?>
