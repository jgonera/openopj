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

    public function testHeader() {
        $this->assertEquals('CPYA', $this->opj->header['id']);
        $this->assertEquals('4.2673', $this->opj->header['version']);
        $this->assertEquals(552, $this->opj->header['build']);
    }

    //public function testWorksheetCount() {
        //$this->assertCount(4, $this->opj->worksheets);
    //}
}
?>
