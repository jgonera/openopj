<?php
use OpenOPJ\OPJFile, OpenOPJ\FileReader;

class OPJFileTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->opj = new OPJFile(new FileReader('support/test.opj'));
    }

    public function testSignature() {
        $this->assertEquals('CPYA', $this->opj->signature['id']);
        $this->assertEquals('4.2673', $this->opj->signature['version']);
        $this->assertEquals(552, $this->opj->signature['build']);
    }

    public function testSingatureException() {
        $this->setExpectedException('OpenOPJ\WrongSignatureError');
        new OPJFile(new FileReader('support/test.txt'));
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
            'Data1_DY',
            
            'TestW_Text', 'TestW_TextNumeric', 'TestW_firstRow'
        );
        foreach ($names as $name) {
            $this->assertArrayHasKey($name, $this->opj->data);
        }
    }

    public function testDataDoubleContents() {
        $this->assertEquals(0.4, $this->opj->data['Data1_INJV'][0]);
        $this->assertEquals(2.0, $this->opj->data['Data1_INJV'][19]);
        $this->assertNull($this->opj->data['Data1_INJV'][20]);
    }

    public function testDataFloatContents() {
        $this->assertEquals(345.60001, $this->opj->data['TestW_Float'][0], '', 0.00001);
        $this->assertEquals(-100000.20313, $this->opj->data['TestW_Float'][1], '', 0.00001);
    }

    public function testDataLongContents() {
        $this->assertEquals(345, $this->opj->data['TestW_Long'][0]);
        $this->assertEquals(-100000, $this->opj->data['TestW_Long'][1]);
    }

    public function testDataIntegerContents() {
        $this->assertEquals(34, $this->opj->data['TestW_Integer'][0]);
        $this->assertEquals(-1000, $this->opj->data['TestW_Integer'][1]);
    }

    public function testDataTextNumericContents() {
        $this->assertNull($this->opj->data['Data1_DY'][0]);
        $this->assertEquals(-33.23707, $this->opj->data['Data1_DY'][1], '', 0.00001);
        $this->assertEquals(-60.56222, $this->opj->data['Data1_DY'][19], '', 0.00001);

        $this->assertEquals('text', $this->opj->data['TestW_TextNumeric'][0]);
        $this->assertEquals(3.14, $this->opj->data['TestW_TextNumeric'][1]);
    }

    public function testDataTextContents() {
        $this->assertEquals('test string 123', $this->opj->data['TestW_Text'][0]);
        $this->assertEquals('only text', $this->opj->data['TestW_Text'][1]);
    }

    public function testDataFirstRowNotZero() {
        $this->assertNull($this->opj->data['TestW_firstRow'][0]);
        $this->assertEquals(5.23, $this->opj->data['TestW_firstRow'][1]);
        $this->assertEquals(-7, $this->opj->data['TestW_firstRow'][2]);
    }

    public function testDataRowCount() {
        $this->assertCount(2, $this->opj->data['TestW_Float']);
        $this->assertCount(2, $this->opj->data['TestW_TextNumeric']);
    }

    public function testWindowsNames() {
        $names = array(
            'Data1', 'Data1Coeff', 'Data1RAW', 'Data1spline', 'DeltaH',
            'ITCFINAL', 'mRawITC'
        );
        foreach ($names as $name) {
            $this->assertArrayHasKey($name, $this->opj->windows);
        }
    }
    
    public function testParameters() {
        $this->assertEquals(1, $this->opj->parameters['ERR']);
        $this->assertEquals(1.25, $this->opj->parameters['SYRNG_C_DATA1']);
        $this->assertEquals(0.1246, $this->opj->parameters['CELL_C_DATA1']);
        $this->assertEquals(1.28889201142965, $this->opj->parameters['S']);
    }

    public function testNotes() {
        $this->assertEquals(
            "Data1 Temperature:\t25.10242\r\n\r\n",
            $this->opj->notes['Results']
        );
        $this->assertEquals(
            "[3/5/2009 13:32 \"/DeltaH\" (2454895)]\r\n" .
            "Data: Data1_NDH\r\n" .
            "Model: OneSites\r\n" .
            "Chi^2/DoF = 3008\r\n" .
            "N\t0.800\t0.0346\r\n" .
            "K\t1.75E4\t1.86E3\r\n" .
            "H\t-5406\t340.5\r\n" .
            "S\t1.29\r\n\r\n",
            $this->opj->notes['ResultsLog']
        );
    }

}
?>
