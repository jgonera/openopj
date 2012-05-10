<?php
require_once('OpenOPJ.php');

class OpenOPJTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->opj = new OpenOPJ('support/test.opj');
    }

    public function testWorksheetCount() {
        $this->assertCount(4, $this->opj->worksheet);
    }
}
?>
