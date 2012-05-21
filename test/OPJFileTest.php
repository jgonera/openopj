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

    public function testSignature() {
        $this->assertEquals('CPYA', $this->opj->signature['id']);
        $this->assertEquals('4.2673', $this->opj->signature['version']);
        $this->assertEquals(552, $this->opj->signature['build']);
    }

    public function testHeader() {
        $this->assertEquals(7.0552, $this->opj->header['originVersion']);
    }

    /** @group wip */
    public function testData() {
        $this->assertArrayHasKey('Data1_DH', $this->opj->data);
    }
}
?>
