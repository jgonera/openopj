<?php
namespace OpenOPJ;

require_once('common.php');

class FileReadError extends OpenOPJException {}

class FileReader {
    protected $fileHandle;

    public function __construct($fileName) {
        $this->fileHandle = @fopen($fileName, 'rb');
        if (!$this->fileHandle) throw new FileReadError("Can't read file $fileName");
        flock($this->fileHandle, LOCK_SH);
    }

    public function __destruct() {
        flock($this->fileHandle, LOCK_UN);
        fclose($this->fileHandle);
    }

    public function offset() {
        return ftell($this->fileHandle);
    }

    public function readLine() {
        return fgets($this->fileHandle);
    }

    public function read($count) {
        $data = fread($this->fileHandle, $count);
        if (!$data) {
            throw new UnexpectedEndError();
        }
        return $data;
    }

    public function seek($count) {
        fseek($this->fileHandle, $count, SEEK_CUR);
    }
}
?>
