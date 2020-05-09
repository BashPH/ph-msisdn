<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class MsisdnTest extends TestCase
{
    protected $networks = [
        'all',
        'globe',
        'tm',
        'smart',
        'sun',
        'tnt',
    ];

    protected $errorNetworks = [
        'allx',
        'globex',
        'tmx',
        'smartx',
        'sunx',
        'tntx',
        '',
    ];
    
    protected $mobile = [
        '091737654321',
        '+6391757654321',
        '+6391767654321',
        '639557654321',
        '9077654321',
    ];
    
    protected $prefix = [
        '9173',
        '9175',
        '9176',
        '955',
        '907',
    ];
    
    protected $errorMobile = [
        '0919137654321',
        '+639127576543213',
        '+6391376-765-43214',
        '63954576543215',
        '907576543216',
    ];

    /**
     * Test array.
     */
    public function testNetwork()
    {
        foreach ($this->networks as $network) {
            $this->assertIsArray(BashPH\Msisdn::listPrefix($network));
        }
    }

    /**
     * Test array error.
     */
    public function testErrorNetwork()
    {
        foreach ($this->errorNetworks as $network) {
            $this->assertIsArray(BashPH\Msisdn::listPrefix($network));
            $this->assertArrayHasKey("error", BashPH\Msisdn::listPrefix($network));
        }
    }

    /**
     * Test prefix are equals.
     */
    public function testGetPrefix()
    {
        for ($i=0; $i<count($this->mobile); $i++) {
            $this->assertEquals($this->prefix[$i], BashPH\Msisdn::getPrefix($this->mobile[$i])['prefix']);
        }
    }

    /**
     * Test prefix are not equal.
     */
    public function testErrorGetPrefix()
    {
        for ($i=0; $i<count($this->errorMobile); $i++) {
            $this->assertEquals(false, BashPH\Msisdn::getPrefix($this->errorMobile[$i])['valid']);
        }
    }

    /**
     * Test for formating.
     */
    public function testFormat()
    {
        // with country code, no separator.
        $this->assertEquals("+6391737654321", BashPH\Msisdn::format('091737654321', true));

        // with country code, separator is "-".
        $this->assertEquals("+63-9175-765-4321", BashPH\Msisdn::format('+6391757654321', true, '-'));

        // with country code, separator is " ".
        $this->assertEquals("+63 9176 765 4321", BashPH\Msisdn::format('091767654321', true, ' '));

        // with country code, separator is ".".
        $this->assertEquals("+63.955.765.4321", BashPH\Msisdn::format('+639557654321', true, '.'));

        // no country code, separator is "-".
        $this->assertEquals("0955-765-4321", BashPH\Msisdn::format('+639557654321', false, '-'));

        // no country code, no separator.
        $this->assertEquals("09077654321", BashPH\Msisdn::format('9077654321'));
    }

    /**
     * Test for sanitizing msisdn.
     */
    public function testSanitize()
    {
        $this->assertEquals("6391737654321", BashPH\Msisdn::sanitize('+6391737654321'));
        $this->assertEquals("6391737654321", BashPH\Msisdn::sanitize('+63-9173-765-4321'));
        $this->assertEquals("6391737654321", BashPH\Msisdn::sanitize('+63 9173 765 4321'));
        $this->assertEquals("6391737654321", BashPH\Msisdn::sanitize('+63 A9173 B765 C4321 X'));
        $this->assertEquals("", BashPH\Msisdn::sanitize('+abcdefghi'));
    }

    /**
     * Test for removing leading zero.
     */
    public function testRemoveLeadingZero()
    {
        $this->assertEquals("91737654321", BashPH\Msisdn::removeLeadingZero('091737654321'));
        $this->assertEquals("9177654321", BashPH\Msisdn::removeLeadingZero('09177654321'));
        $this->assertEquals("", BashPH\Msisdn::removeLeadingZero(''));
    }

    /**
     * Test for removing country code.
     */
    public function testRemoveCountryCode()
    {
        $this->assertEquals("91737654321", BashPH\Msisdn::removeCountryCode('+6391737654321'));
        $this->assertEquals("9177654321", BashPH\Msisdn::removeCountryCode('+639177654321'));
        $this->assertEquals("9177654321", BashPH\Msisdn::removeCountryCode('639177654321'));
        $this->assertEquals("", BashPH\Msisdn::removeCountryCode(''));
    }

    /**
     * Test for checking if msisdn is null.
     */
    public function testIsNull()
    {
        $this->assertTrue(BashPH\Msisdn::isNull(null));
        $this->assertFalse(BashPH\Msisdn::isNull(''));
        $this->assertFalse(BashPH\Msisdn::isNull(true));
        $this->assertFalse(BashPH\Msisdn::isNull('+6391737654321'));
    }

    /**
     * Test for checking if msisdn is empty.
     */
    public function testIsEmpty()
    {
        $this->assertTrue(BashPH\Msisdn::isEmpty(null));
        $this->assertTrue(BashPH\Msisdn::isEmpty(''));
        $this->assertFalse(BashPH\Msisdn::isEmpty(' '));
        $this->assertFalse(BashPH\Msisdn::isEmpty('+6391737654321'));
    }

    /**
     * Test for checking if msisdn is a number.
     */
    public function testIsNumber()
    {
        $this->assertTrue(BashPH\Msisdn::isNumber('+6391737654321'));
        $this->assertTrue(BashPH\Msisdn::isNumber('6391737654321'));
        $this->assertFalse(BashPH\Msisdn::isNumber(' '));
        $this->assertFalse(BashPH\Msisdn::isNumber('09173-765-4321'));
    }

    /**
     * Test for cleaning msisdn.
     */
    public function testClean()
    {
        $this->assertEquals('91737654321', BashPH\Msisdn::clean('091737654321'));
        $this->assertEquals('91737654321', BashPH\Msisdn::clean('+6391737654321'));
        $this->assertEquals('91737654321', BashPH\Msisdn::clean('09173-765-4321'));
        $this->assertEquals('91737654321', BashPH\Msisdn::clean('+63-9173-765-4321'));
        $this->assertEquals('91737654321', BashPH\Msisdn::clean('+63 9173 765 4321'));
        $this->assertEquals('91737654321', BashPH\Msisdn::clean('+63 A9173 B765 C4321'));
        $this->assertEquals('', BashPH\Msisdn::clean('abcdefghijklmnop'));
    }

    /**
     * Test for validating msisdn.
     */
    public function testValidate()
    {
        $validation = [];

        //test valid
        for ($i=0; $i<count($this->mobile); $i++) {
            $validation[$i] = BashPH\Msisdn::validate($this->mobile[$i]);
            $this->assertArrayHasKey("valid", $validation[$i]);
            $this->assertTrue($validation[$i]['valid']);
        }

        //test invalid
        for ($i=0; $i<count($this->errorMobile); $i++) {
            $this->assertFalse(BashPH\Msisdn::validate($this->errorMobile[$i])['valid']);
        }

        //test invalid
        $this->assertFalse(BashPH\Msisdn::validate('091976543212')['valid']);

        //test invalid with network
        $this->assertFalse(BashPH\Msisdn::validate('09197654321', 'Globe')['valid']);
        
        //091737654321

        //prefix
        $this->assertEquals("9173", $validation[0]['prefix']);

        //network
        $this->assertEquals("Globe", $validation[0]['carrier']["network"]);
        $this->assertEquals("Postpaid", $validation[0]['carrier']["type"]);

        //format with country code
        $this->assertEquals("+6391737654321", $validation[0]['format'][0]);

        //format with leading zero
        $this->assertEquals("091737654321", $validation[0]['format'][1]);

        //verify no error
        $this->assertNull($validation[0]['error']);

        //validate by network
        $this->assertTrue(BashPH\Msisdn::validate($this->mobile[0], 'Globe')['valid']);

        //validate by network, get format with country code
        $this->assertEquals("+6391737654321", BashPH\Msisdn::validate($this->mobile[0], 'Globe')['format'][0]);

        //validate by network, get format with leading zero
        $this->assertEquals("091737654321", BashPH\Msisdn::validate($this->mobile[0], 'Globe')['format'][1]);

        //validate by network with separator
        $this->assertTrue(BashPH\Msisdn::validate($this->mobile[0], 'Globe', '-')['valid']);

        //validate by network with separator, get format with country code
        $this->assertEquals("+63-9173-765-4321", BashPH\Msisdn::validate($this->mobile[0], 'Globe', '-')['format'][0]);

        //validate by network with separator, get format with leading zero
        $this->assertEquals("09173-765-4321", BashPH\Msisdn::validate($this->mobile[0], 'Globe', '-')['format'][1]);
    }

    /**
     * Test for validating msisdn.
     */
    public function testIsValid()
    {
        //test valid
        $this->assertTrue(BashPH\Msisdn::isValid($this->mobile[0]));

        //test valid with network
        $this->assertTrue(BashPH\Msisdn::isValidGlobe($this->mobile[0]));

        //test invalid
        $this->assertFalse(BashPH\Msisdn::isValid($this->errorMobile[0]));

        //test invalid with network
        $this->assertFalse(BashPH\Msisdn::isValidSmart($this->errorMobile[0]));

        //test invalid with network
        $this->assertFalse(BashPH\Msisdn::isValidSun($this->mobile[0]));

        //test invalid with network
        $this->assertFalse(BashPH\Msisdn::isValidTnt($this->errorMobile[0]));

        //test invalid with network
        $this->assertFalse(BashPH\Msisdn::isValidTm($this->mobile[0]));
    }
}
