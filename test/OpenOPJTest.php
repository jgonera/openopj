<?php
require_once('OpenOPJ.php');

use OpenOPJ\OpenOPJ;

class OpenOPJTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->opj = new OpenOPJ('support/test.opj');
    }

    public function testFileReadError() {
        $this->setExpectedException('OpenOPJ\FileReadError');
        $opj = new OpenOPJ('support/nonexistent.opj');
    }

    public function testWorksheetCount() {
        $this->assertCount(4, $this->opj->worksheets);
    }
}
?>
