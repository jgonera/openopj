<?php
namespace OpenOPJ;

class OpenOPJException extends \Exception {}
class FileReadError extends OpenOPJException {}
class ParseError extends OpenOPJException {}
class UnexpectedEndError extends ParseError {}
class FragmentSeparatorError extends ParseError {}

function prettyHex($data) {
    return chunk_split(bin2hex($data), 2, ' ');
}

function prettyBin($data, $length=32) {
    return chunk_split(str_pad(decbin($data), $length, '0', STR_PAD_LEFT), 8, ' ');
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

// binary data wrapper, uses mb_* functions in case mbstring.func_overload is
// set
class Fragment {
    const FRAGMENT_SEPARATOR = 0x0A;
    public $data;

    public function __construct($data) {
        $this->data = $data;
        if (ord($this->slice(-1, 1)) !== self::FRAGMENT_SEPARATOR) {
            throw new FragmentSeparatorError("Wrong fragment separator");
        }
        $this->data = $this->slice(0, -1);
    }

    public function size() {
        return mb_strlen($this->data, '8bit');
    }

    public function slice($offset, $length) {
        return mb_substr($this->data, $offset, $length, '8bit');
    }
}

class OPJReader extends FileReader {
    const SIZE_LENGTH = 5;

    protected function readSize() {
        $sizeFragment = new Fragment($this->read(self::SIZE_LENGTH));
        list(, $size) = unpack('V', $sizeFragment->data);
        return $size;
    }

    public function readBlock($keep=false) {
        $size = $this->readSize();
        Logger::log(
            "Block of size %d at 0x%X",
            $size, $this->offset() - self::SIZE_LENGTH
        );

        if ($size === 0) return NULL;
        return new Fragment($this->read($size + 1));
    }

    public function isNextBlockNull() {
        $size = $this->readSize();
        if ($size === 0) {
            return true;
        } else {
            $this->seek(-self::SIZE_LENGTH);
            return false;
        }
    }
}

abstract class Section {
    protected $file;
    public $offset;

    public function __construct($file) {
        $this->file = $file;
        $this->offset = $this->file->offset();
        Logger::log("%s at 0x%X", get_class($this), $this->offset);
        $this->parse();
        Logger::log("End of %s at 0x%X", get_class($this), $this->file->offset());
    }
}

?>
