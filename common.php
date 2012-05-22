<?php
namespace OpenOPJ;

class OpenOPJException extends \Exception {}
class FileReadError extends OpenOPJException {}
class UnexpectedEndError extends OpenOPJException {}
class BlockSeparatorError extends OpenOPJException {}

function prettyHex($data) {
    return chunk_split(bin2hex($data), 2, ' ');
}

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

    public function isEnd() {
        return feof($this->fileHandle);
    }
}

class Block {
    const BLOCK_SEPARATOR = 0x0A;
    public $data;

    public function __construct($data) {
        $this->data = $data;
        if (ord($this->slice(-1, 1)) !== self::BLOCK_SEPARATOR) {
            throw new BlockSeparatorError("Wrong block separator");
        }
    }

    public function size() {
        return mb_strlen($this->data, '8bit');
    }

    public function slice($offset, $length) {
        return mb_substr($this->data, $offset, $length, '8bit');
    }
}

class OPJReader extends FileReader {
    protected $lastSize = NULL;

    protected function readSizeBlock() {
        // if we already read the size in isSectionEnd(), do not read again
        if ($this->lastSize !== NULL) return $this->lastSize;

        $block = new Block($this->read(5));
        list(, $size) = unpack('V', $block->slice(0x0, 4));
        $this->lastSize = $size;
        Logger::log("Size block (%d) at 0x%X", $size, $this->offset() - 5);
        return $size;
    }

    public function readBlock() {
        $size = $this->readSizeBlock();
        $this->lastSize = NULL;

        if ($size === 0) return NULL;
        return new Block($this->read($size + 1));
    }

    public function isSectionEnd() {
        return $this->readSizeBlock() === 0;
    }

    public function findSectionEnd() {
        while ($block = $this->readBlock()) {
            Logger::log("Skipping unknown block of size %d at 0x%X", $block->size(), $this->offset());
        }
    }
}

abstract class Section {
    protected $file;

    public function __construct($file) {
        $this->file = $file;
        $this->parse();
    }
}

?>
