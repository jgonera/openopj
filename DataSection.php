<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');

class DataSection extends Section {
    const EMPTY_VALUE = -1.23456789E-300;
    const DATA_TYPE_INTEGER = 0x800;
    const DATA_TYPE_TEXTNUMERIC = 0x100;

    public $name, $data = array();
    protected $header = array();

    protected function parse() {
        $this->parseDataHeader();
        $this->parseDataContent();
        $this->file->readBlock();
    }

    protected function parseDataHeader() {
        $block = $this->file->readBlock();
        if ($block->size() < 113) {
            throw new ParseError("Unexpected data header block size: $size");
        }
        $this->header = unpack(
            'vdataType/CdataType2/VtotalRows/VfirstRow/VlastRow',
            $block->slice(0x16, 15)
        );
        $this->header += unpack('CvalueSize/x/CdataTypeU', $block->slice(0x3D, 3));
        $this->header += unpack('a25name', $block->slice(0x58, 25));
        $this->name = $this->header['name'];

        Logger::log($this->header);
        Logger::log('dataType bin: ' . prettyBin($this->header['dataType'], 16));
    }

    protected function parseDataContent() {
        $block = $this->file->readBlock();
        if ($block === NULL) return;

        $valueSize = $this->header['valueSize'];
        $dataType = $this->header['dataType'];

        if ($valueSize <= 8) {
            // Numeric
            $format = $this->getFormat();
            $this->data = array_values(unpack($format, $block->data));
        } else {
            $offset = 0;
            $end = $this->header['lastRow'] * $valueSize;
            while ($offset < $end) {
                if ($dataType & self::DATA_TYPE_TEXTNUMERIC) {
                    // Text & Numeric
                    $prefix = ord($block->data[$offset]);
                    $format = $prefix === 0 ? 'd' : sprintf('a%d', $valueSize - 2);
                    list(, $this->data[]) = unpack($format, $block->slice($offset + 2, $valueSize - 2));
                } else {
                    // Text
                    $format = sprintf('a%d', $valueSize);
                    list(, $text) = unpack($format, $block->slice($offset, $valueSize));
                    list($this->data[]) = explode("\0", $text);
                }
                $offset += $valueSize;
            }
        }

        foreach ($this->data as $key => $value) {
            if ($value === self::EMPTY_VALUE) $this->data[$key] = NULL;
        }
    }

    protected function getFormat() {
        $isInteger = $this->header['dataType'] & self::DATA_TYPE_INTEGER;
        switch ($this->header['valueSize']) {
            case 8: return 'd*';
            case 4: return $isInteger ? 'l*' : 'f*';
            case 2: return 's*';
            case 1: return 'c*';
            default: throw new ParseError("Unknown value size: " + $this->header['valueSize']);
        }
    }
            
}

?>
