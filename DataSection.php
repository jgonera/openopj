<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');

class DataSection extends Section {
    const EMPTY_VALUE = -1.23456789E-300;
    public $name, $data = array();
    protected $header = array();

    protected function parse() {
        Logger::log("Data section at 0x%X", $this->file->offset());
        if ($this->parseDataHeader()) {
            $this->parseDataContent();
        }
        $this->file->findSectionEnd();
    }

    protected function parseDataHeader() {
        $block = $this->file->readBlock();
        if ($block->size() < 113) {
            Logger::log("Unexpected data header block size: $size");
            return false;
        }
        $this->header = unpack(
            'vdataType/CdataType2/VtotalRows/VfirstRow/VusedRows',
            $block->slice(0x16, 15)
        );
        $this->header += unpack('CvalueSize/x/CdataTypeU', $block->slice(0x3D, 3));
        $this->header += unpack('a25name', $block->slice(0x58, 25));
        $this->name = $this->header['name'];

        Logger::log($this->header);

        return true;
    }

    protected function parseDataContent() {
        $block = $this->file->readBlock();
        $valueSize = $this->header['valueSize'];
        $offset = $this->header['firstRow'] * $valueSize;
        $length = $this->header['usedRows'] * $valueSize;
        $rawData = $block->slice($offset, $length);

        if ($valueSize <= 8) {
            // Numeric
            switch ($valueSize) {
                case 8: $format = 'd*'; break;
                case 4: $format = 'V*'; break;
                case 2: $format = 'v*'; break;
                case 1: $format = 'C*'; break;
                default: throw new ParseError("Unknown value size: " + $valueSize);
            }
            $this->data = array_values(unpack($format, $rawData));
        } else {
            $end = $offset + $length;
            while ($offset < $end) {
                if ($this->header['dataType'] & 0x100) {
                    // Text & Numeric
                    $prefix = ord($block->data[$offset]);
                    if ($prefix === 0) {
                        $format = 'd';
                    } else {
                        $format = sprintf('a%d', $valueSize - 2);
                    }
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
            
}

?>
