<?php

namespace VeneerTest\Component\Security;

use Veneer\Component\Security\EncryptionService;

class EncryptionServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSymmetry()
    {
        $subject = new EncryptionService();

        $data = 'secrets';

        $encrypted = $subject->encrypt($data);
        $this->assertEquals('U4jGhg3Z7Y+9SwZ1XA4+LF9v9jTcduY/KS6zPLsWoYA=', $encrypted);

        $this->assertEquals($data, $subject->decrypt($encrypted));
    }

    public function testSymmetryKey()
    {
        $subject = new EncryptionService([
            'key' => 'secretforsecrets',
        ]);

        $data = 'secrets';

        $encrypted = $subject->encrypt($data);
        $this->assertEquals('yNbTPCkHV+o+Wc/FcUXYDfUmm2FBAeyxnFhXA+YWmfM=', $encrypted);

        $this->assertEquals($data, $subject->decrypt($encrypted));
    }

    public function testSymmetrySalt()
    {
        $subject = new EncryptionService();

        $salt = 'saltforsecrets';
        $data = 'secrets';

        $encrypted = $subject->encrypt($data, $salt);
        $this->assertEquals('BwCGGlohni2/4vynuH0KkjF99iRoVTnRoRMOWRmiqVY=', $encrypted);

        $this->assertEquals($data, $subject->decrypt($encrypted, $salt));
    }
}
