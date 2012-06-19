<?php
use OpenOPJ\OPJFile, OpenOPJ\StringReader;

class StringReaderTest extends PHPUnit_Framework_TestCase {

    public function testStringReader() {
        $string = file_get_contents('support/test.opj');
        $this->opj = new OPJFile(new StringReader($string));
        $this->assertEquals('CPYA', $this->opj->signature['id']);
        $this->assertEquals(345, $this->opj->data['TestW_Long'][0]);
    }

}
?>
