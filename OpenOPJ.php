<?php
namespace OpenOPJ;

class OpenOPJException extends \Exception {}
class FileReadError extends OpenOPJException {}

class OpenOPJ {
    public $worksheets;
    protected $fileHandle;

    function __construct($fileName) {
        $this->fileHandle = @fopen($fileName, 'rb');
        if (!$this->fileHandle) throw new FileReadError("Can't read file $fileName");
        flock($this->fileHandle, LOCK_SH);

        $this->parse();

        flock($this->fileHandle, LOCK_UN);
        fclose($this->fileHandle);
    }

    protected function parse() {

    }
}
?>
