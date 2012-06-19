<?php
namespace OpenOPJ;

require_once('common.php');

class StringReader {
    protected $data, $offset, $length;

    public function __construct($string) {
        $this->data = $string;
        $this->offset = 0;
        $this->length = mb_strlen($this->data, '8bit');
    }

    public function offset() {
        return $this->offset;
    }

    public function readLine() {
        $nlPos = mb_strpos($this->data, "\n", $this->offset, '8bit');
        if ($nlPos === false) {
            throw new UnexpectedEndError();
        }
        $count = $nlPos - $this->offset + 1;
        $data = mb_substr($this->data, $this->offset, $count, '8bit');
        $this->offset += $count;
        return $data;
    }

    public function read($count) {
        $data = mb_substr($this->data, $this->offset, $count, '8bit');
        $this->offset += $count;
        if ($this->offset > $this->length) {
            throw new UnexpectedEndError();
        }
        return $data;
    }

    public function seek($count) {
        $this->offset += $count;
    }
}
?>
