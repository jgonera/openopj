<?php
require_once('test/helper.php');

use OpenOPJ\OPJFile;

class OPJFileTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->opj = new OPJFile('support/test.opj');
    }

    public function testFileReadError() {
        $this->setExpectedException('OpenOPJ\FileReadError');
        $opj = new OpenOPJ('support/nonexistent.opj');
    }

    /** @group wip */
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
