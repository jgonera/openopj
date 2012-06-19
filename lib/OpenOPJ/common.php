<?php
namespace OpenOPJ;

class OpenOPJException extends \Exception {}
class ParseError extends OpenOPJException {}
class UnexpectedEndError extends ParseError {}
class FragmentSeparatorError extends ParseError {}

function prettyHex($data) {
    return chunk_split(bin2hex($data), 2, ' ');
}

function prettyBin($data, $length=32) {
    return chunk_split(str_pad(decbin($data), $length, '0', STR_PAD_LEFT), 8, ' ');
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

class OPJReader {
    const SIZE_LENGTH = 5;

    public function __construct($reader) {
        $this->reader = $reader;
    }

    protected function readSize() {
        $sizeFragment = new Fragment($this->reader->read(self::SIZE_LENGTH));
        list(, $size) = unpack('V', $sizeFragment->data);
        return $size;
    }

    public function offset() {
        return $this->reader->offset();
    }

    public function readLine() {
        return $this->reader->readLine();
    }

    public function read($count) {
        return $this->reader->read($count);
    }

    public function readBlock($keep=false) {
        $size = $this->readSize();
        Logger::log(
            "Block of size %d at 0x%X",
            $size, $this->reader->offset() - self::SIZE_LENGTH
        );

        if ($size === 0) return NULL;
        return new Fragment($this->reader->read($size + 1));
    }

    public function isNextBlockNull() {
        $size = $this->readSize();
        if ($size === 0) {
            return true;
        } else {
            $this->reader->seek(-self::SIZE_LENGTH);
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
