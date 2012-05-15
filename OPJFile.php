<?php
namespace OpenOPJ;

require_once('Logger.php');

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

    public function readBytes($count) {
        $data = fread($this->fileHandle, $count);
        if (!$data) {
            throw new UnexpectedEndError();
        }
        return $data;
    }

    public function skipBytes($count) {
        fseek($this->fileHandle, $count, SEEK_CUR);
    }

    public function finished() {
        return feof($this->fileHandle);
    }
}

class OPJFile {
    protected $debug, $file, $stats = array();
    public $header, $worksheets;

    function __construct($fileName) {
        $this->file = new FileReader($fileName);
        $this->parse();
        unset($this->file);
    }

    protected function parse() {
        $this->parseHeader();
        $this->skipUnknownBlock();
        try {
            $this->parseDataSections();
        } catch (\Exception $e) {
        }
        Logger::log(print_r($this->stats[123], true));
    }

    protected function parseHeader() {
        $tmp = explode(' ', $this->file->readLine());
        $this->header['id'] = $tmp[0];
        $this->header['version'] = $tmp[1];
        $this->header['build'] = (int)$tmp[2];
        Logger::log(
            "OPJ version: %s, build: %d",
            $this->header['version'],
            $this->header['build']
        );
    }

    protected function readSizeBlock() {
        $size = 0;
        while ($size === 0) {
            $tmp = unpack('Vsize', $this->file->readBytes(4));
            $size = $tmp['size'];
            Logger::log("Size block (%d) at 0x%X", $size, $this->file->offset() - 4);
            // line feed
            $this->file->skipBytes(1);
        }
        return $size;
    }

    protected function skipUnknownBlock() {
        $size = $this->readSizeBlock();
        Logger::log("Skipping unknown block of size $size at 0x%X", $this->file->offset());
        $dump = prettyHex($this->file->readBytes(20));
        //Logger::log("Block starts with: %s", $dump);

        if (!isset($this->stats[$size])) {
            $this->stats[$size] = array();
        }
        $this->stats[$size][] = $dump;

        $this->file->skipBytes($size - 20 + 1);
    }

    protected function parseDataSections() {
        while (!$this->file->finished()) {
            $this->skipUnknownBlock();
            //$this->parseDataHeaderBlock();
            //$this->parseDataContentBlock();
        }
    }

    protected function parseDataHeaderBlock() {
        
    }

    protected function parseDataContentBlock() {

    }

}
?>
