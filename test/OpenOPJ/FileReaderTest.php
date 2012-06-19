<?php
use OpenOPJ\OPJFile, OpenOPJ\FileReader;

class FileReaderTest extends PHPUnit_Framework_TestCase {

    public function testFileReader() {
        // just in case of change of the default reader in OPJFileTest
        $this->opj = new OPJFile(new FileReader('support/test.opj'));
        $this->assertEquals('CPYA', $this->opj->signature['id']);
        $this->assertEquals(345, $this->opj->data['TestW_Long'][0]);
    }

    public function testFileReadError() {
        $this->setExpectedException('OpenOPJ\FileReadError');
        new FileReader('support/nonexistent.opj');
    }

}
?>
