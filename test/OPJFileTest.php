<?php
require_once('test/helper.php');

use OpenOPJ\OPJFile;

class OPJFileTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->opj = new OPJFile('support/test.opj');
    }

    public function testFileReadError() {
        $this->setExpectedException('OpenOPJ\FileReadError');
        $opj = new OPJFile('support/nonexistent.opj');
    }

    public function testSignature() {
        $this->assertEquals('CPYA', $this->opj->signature['id']);
        $this->assertEquals('4.2673', $this->opj->signature['version']);
        $this->assertEquals(552, $this->opj->signature['build']);
    }

    public function testHeader() {
        $this->assertEquals(7.0552, $this->opj->header['originVersion']);
    }

    public function testDataNames() {
        $names = array(
            'Data1_DH', 'Data1_INJV', 'Data1_Xt', 'Data1_Mt', 'Data1_XMt',
            'Data1_NDH', 'Data1RAW_time', 'Data1RAW_cp', 'Data1BEGIN',
            'Data1RANGE', 'Data1Coeff_Time', 'Data1Coeff_Base',
            'Data1Coeff_Spline', 'Data1Coeff_Coeff', 'Data1Coeff_Net',
            'Data1spline_Time', 'Data1spline_Base', 'DATA1BASE', 'Data1_Fit',
            'Data1_DY', 'Test_Text', 'Test_TextNumeric'
        );
        foreach ($names as $name) {
            $this->assertArrayHasKey($name, $this->opj->data);
        }
    }

    public function testDataNumericContents() {
        $this->assertEquals(0.4, $this->opj->data['Data1_INJV'][0]);
        $this->assertEquals(2.0, $this->opj->data['Data1_INJV'][19]);
        $this->assertNull($this->opj->data['Data1_INJV'][20]);
    }

    public function testDataTextNumericContents() {
        $this->assertNull($this->opj->data['Data1_DY'][0]);
        $this->assertEquals(-33.23707, $this->opj->data['Data1_DY'][1], '', 0.00001);
        $this->assertEquals(-60.56222, $this->opj->data['Data1_DY'][19], '', 0.00001);

        $this->assertEquals('text', $this->opj->data['Test_TextNumeric'][0]);
        $this->assertEquals(3.14, $this->opj->data['Test_TextNumeric'][1]);
    }

    public function testDataTextContents() {
        $this->assertEquals('test string 123', $this->opj->data['Test_Text'][0]);
        $this->assertEquals('only text', $this->opj->data['Test_Text'][1]);
    }

    public function testFirstRowNotZero() {
        $this->assertNull($this->opj->data['Test_firstRow'][0]);
        $this->assertEquals(5.23, $this->opj->data['Test_firstRow'][1]);
        $this->assertEquals(7, $this->opj->data['Test_firstRow'][2]);
    }

}
?>
