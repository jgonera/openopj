<?php
namespace OpenOPJ;

class OpenOPJException extends \Exception {}
class FileReadError extends OpenOPJException {}
class UnexpectedEndError extends OpenOPJException {}

function prettyHex($data) {
    return chunk_split(bin2hex($data), 2, ' ');
}

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

    public function isEnd() {
        return feof($this->fileHandle);
    }
}

class OPJReader extends FileReader {
    public function readSizeBlock() {
        $tmp = unpack('Vsize', $this->read(4));
        $size = $tmp['size'];
        Logger::log("Size block (%d) at 0x%X", $size, $this->offset() - 4);
        // line feed
        $this->seek(1);
        return $size;
    }

    public function peekSizeBlock() {
        $tmp = unpack('Vsize', $this->read(4));
        $this->seek(-4);
        return $tmp['size'];
    }

    public function findSectionEnd() {
        while ($size = $this->readSizeBlock()) {
            Logger::log("Skipping unknown block of size $size at 0x%X", $this->offset());
            $this->seek($size + 1);
        }
    }
}

abstract class Section {
    protected $file;

    function __construct($file) {
        $this->file = $file;
        $this->parse();
    }
}

?>
