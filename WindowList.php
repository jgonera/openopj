<?php
namespace OpenOPJ;

require_once('Logger.php');
require_once('common.php');

class WindowList extends Section {
    protected function parse() {
        while (!$this->file->isSectionEnd()) {
            new WindowSection($this->file);
        }
    }
}

class WindowSection extends Section {
    protected function parse() {
        $this->file->readBlock();
        new LayerList($this->file);
    }
}

class LayerList extends Section {
    protected function parse() {
        while (!$this->file->isSectionEnd()) {
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
        while (!$this->file->isSectionEnd()) {
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
        while (!$this->file->isSectionEnd()) {
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
        while (!$this->file->isSectionEnd()) {
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
        while (!$this->file->isSectionEnd()) {
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