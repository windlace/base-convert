<?php

namespace Cast\BaseConv\Tests;

use function Cast\BaseConv\base58DecodeCheck;
use function Cast\BaseConv\base58Encode;
use function Cast\BaseConv\base58EncodeCheck;
use function Cast\BaseConv\base58Decode;
use PHPUnit\Framework\TestCase;

class Base58Test extends TestCase
{
    function test_base58Encode()
    {
        // From hex to base58
        $this->assertEquals('4mYe63rvrhHmtbXDxDRYGpMPAQnPS8UmhaaiQNrvAQjt', base58Encode(hex2bin('37fc642590af8494e497a0f7cb07d05cd50458e072ec05895291c00e9bcc570b')));
    }

    function test_base58EncodeCheck()
    {
        // From hex to base58check
        $this->assertEquals('Rf5v7Yem3hDn4WqsLW9bWnHrMiVMAFFkJEK5hu7qwkuPus7f2', base58EncodeCheck(hex2bin('37fc642590af8494e497a0f7cb07d05cd50458e072ec05895291c00e9bcc570b')));
    }
    function test_base58Decode()
    {
        // From base58 to hex
        $this->assertEquals('37fc642590af8494e497a0f7cb07d05cd50458e072ec05895291c00e9bcc570b', bin2hex(base58Decode('4mYe63rvrhHmtbXDxDRYGpMPAQnPS8UmhaaiQNrvAQjt')));
    }

    function test_base58DecodeCheck()
    {
        // From base58check to hex
        $this->assertEquals('37fc642590af8494e497a0f7cb07d05cd50458e072ec05895291c00e9bcc570b', bin2hex(base58DecodeCheck('Rf5v7Yem3hDn4WqsLW9bWnHrMiVMAFFkJEK5hu7qwkuPus7f2')));
    }
}
