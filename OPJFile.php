<?php
namespace OpenOPJ;

require_once('Logger.php');

class OpenOPJException extends \Exception {}
class FileReadError extends OpenOPJException {}

class FileReader {
    protected $fileHandle;

    function __construct($fileName) {
        $this->fileHandle = @fopen($fileName, 'rb');
        if (!$this->fileHandle) throw new FileReadError("Can't read file $fileName");
        flock($this->fileHandle, LOCK_SH);
    }

    function __destruct() {
        flock($this->fileHandle, LOCK_UN);
        fclose($this->fileHandle);
    }

    public function readLine() {
        return fgets($this->fileHandle);
    }
}

class OPJFile {
    protected $debug, $file;
    public $header, $worksheets;

    function __construct($fileName) {
        $this->file = new FileReader($fileName);
        $this->parse();
        unset($this->file);
    }

    protected function parse() {
        $this->parseHeader();
        Logger::log('test');
    }

    protected function parseHeader() {
        $tmp = explode(' ', $this->file->readLine());
        $this->header['id'] = $tmp[0];
        $this->header['version'] = $tmp[1];
        $this->header['build'] = (int)$tmp[2];
    }
}
?>
