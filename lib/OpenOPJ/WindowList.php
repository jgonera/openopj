<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');

class WindowList extends Section {
    public $windows = array();

    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            $windowSection = new WindowSection($this->file);
            $this->windows[$windowSection->name] = $windowSection->window;
        }
    }
}

class WindowSection extends Section {
    public $name, $window = array();

    protected function parse() {
        $block = $this->file->readBlock();
        $header = unpack('a25name', $block->slice(0x02, 25));
        $this->name = $header['name'];
        $this->window = $header;

        Logger::log($header);

        new LayerList($this->file);
    }
}

class LayerList extends Section {
    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            new LayerSection($this->file);
        }
    }
}

class LayerSection extends Section {
    protected function parse() {
        $this->file->readBlock();
        new SublayerList($this->file);
        new CurveList($this->file);
        new AxisBreakList($this->file);
        new AxisParameterList($this->file);
        new AxisParameterList($this->file);
        new AxisParameterList($this->file);
    }
}

class SublayerList extends Section {
    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            new SublayerSection($this->file);
        }
    }
}

class SublayerSection extends Section {
    protected function parse() {
        // TODO: accomodate for possible additional nesting?
        $this->file->readBlock();
        $this->file->readBlock();
        $this->file->readBlock();
        $this->file->readBlock();
    }
}

class CurveList extends Section {
    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            new CurveSection($this->file);
        }
    }
}

class CurveSection extends Section {
    protected function parse() {
        $this->file->readBlock();
        $this->file->readBlock();
    }
}

class AxisBreakList extends Section {
    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            new AxisBreakSection($this->file);
        }
    }
}

class AxisBreakSection extends Section {
    protected function parse() {
        $this->file->readBlock();
    }
}

class AxisParameterList extends Section {
    protected function parse() {
        while (!$this->file->isNextBlockNull()) {
            new AxisParameterSection($this->file);
        }
    }
}

class AxisParameterSection extends Section {
    protected function parse() {
        $this->file->readBlock();
    }
}

?>
