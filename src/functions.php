<?php

declare(strict_types=1);

namespace Cast\BaseConv;

const BASE_2  = '01';
const BASE_10 = '0123456789';
const BASE_16 = '0123456789abcdef';
const BASE_16_UPPER = '0123456789ABCDEF';
const BASE_58 = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

function _hexdec($input){ return convBase($input, BASE_16, BASE_10);}
function _hexbin($input){ return convBase($input, BASE_16, BASE_2);}
function _binhex($input){ return convBase($input, BASE_2, BASE_16);}
function _bindec($input){ return convBase($input, BASE_2, BASE_10);}

function convBase($numberInput, $fromBaseInput, $toBaseInput) : string
{
    if ($fromBaseInput==$toBaseInput) return $numberInput;
    $fromBase = str_split($fromBaseInput,1);
    $toBase = str_split($toBaseInput,1);
    $number = str_split($numberInput,1);
    $fromLen=strlen($fromBaseInput);
    $toLen=strlen($toBaseInput);
    $numberLen=strlen($numberInput);
    $retval='';
    if ($toBaseInput == '0123456789')
    {
        $retval=0;
        for ($i = 1;$i <= $numberLen; $i++)
            $retval = bcadd((string)$retval, bcmul((string)array_search($number[$i-1], $fromBase),bcpow((string)$fromLen,(string)($numberLen-$i))));
        return $retval;
    }
    if ($fromBaseInput != '0123456789')
        $base10=convBase($numberInput, $fromBaseInput, '0123456789');
    else
        $base10 = $numberInput;
    if ($base10<strlen($toBaseInput))
        return $toBase[$base10];
    while($base10 != '0')
    {
        $retval = $toBase[bcmod($base10,(string)$toLen)].$retval;
        $base10 = bcdiv($base10,(string)$toLen,0);
    }
    return $retval;
}

/**
 * @param string $string Binary string
 * @return string
 */
function base58Encode($string) {
    $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
    $base = strlen($alphabet);
    // Type validation
    if (is_string($string) === false) {
        throw new \InvalidArgumentException('Argument $string must be a string.');
    }

    // If the string is empty, then the encoded string is obviously empty
    if (strlen($string) === 0) {
        return '';
    }

    // Now we need to convert the byte array into an arbitrary-precision decimal
    // We basically do this by performing a base256 to base10 conversion
    $hex = unpack('H*', $string);
    $hex = reset($hex);
    $decimal = gmp_init($hex, 16);

    // This loop now performs base 10 to base 58 conversion
    // The remainder or modulo on each loop becomes a base 58 character
    $output = '';
    while (gmp_cmp($decimal, $base) >= 0) {
        list($decimal, $mod) = gmp_div_qr($decimal, $base);
        $output .= $alphabet[gmp_intval($mod)];
    }

    // If there's still a remainder, append it
    if (gmp_cmp($decimal, 0) > 0) {
        $output .= $alphabet[gmp_intval($decimal)];
    }

    // Now we need to reverse the encoded data
    $output = strrev($output);

    // Now we need to add leading zeros
    $bytes = str_split($string);
    foreach ($bytes as $byte) {
        if ($byte === "\x00") {
            $output = $alphabet[0] . $output;
            continue;
        }
        break;
    }

    return (string) $output;
}

function base58Decode($base58)
{
    $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
    $base = strlen($alphabet);
    // Type Validation
    if (is_string($base58) === false) {
        throw new \InvalidArgumentException('Argument $base58 must be a string.');
    }

    // If the string is empty, then the decoded string is obviously empty
    if (strlen($base58) === 0) {
        return '';
    }

    $indexes = array_flip(str_split($alphabet));
    $chars = str_split($base58);

    // Check for invalid characters in the supplied base58 string
    foreach ($chars as $char) {
        if (isset($indexes[$char]) === false) {
            throw new \InvalidArgumentException('Argument $base58 contains invalid characters.');
        }
    }

    // Convert from base58 to base10
    $decimal = gmp_init($indexes[$chars[0]], 10);

    for ($i = 1, $l = count($chars); $i < $l; $i++) {
        $decimal = gmp_mul($decimal, $base);
        $decimal = gmp_add($decimal, $indexes[$chars[$i]]);
    }

    // Convert from base10 to base256 (8-bit byte array)
    $output = '';
    while (gmp_cmp($decimal, 0) > 0) {
        list($decimal, $byte) = gmp_div_qr($decimal, 256);
        $output = pack('C', gmp_intval($byte)) . $output;
    }

    // Now we need to add leading zeros
    foreach ($chars as $char) {
        if ($indexes[$char] === 0) {
            $output = "\x00" . $output;
            continue;
        }
        break;
    }

    return $output;
}

function base58EncodeCheck($binaryString)
{
    $checksumLength = 4;
    $checksum = substr(hash('sha256', hash('sha256', $binaryString, true), true), 0, $checksumLength);
    return base58Encode($binaryString.$checksum);
}

function base58DecodeCheck($base58)
{
    $checksumLength = 4;
    $decoded = base58Decode($base58);
    if (strlen($decoded) < $checksumLength) throw new \InvalidArgumentException('Missing base58 checksum');
    $data = substr($decoded, 0, -$checksumLength);
    $checksumVerify = substr($decoded, -$checksumLength);
    $checksum = substr(hash('sha256', hash('sha256', $data, true), true), 0, $checksumLength);
    if (!hash_equals($checksum, $checksumVerify)) throw new \InvalidArgumentException('Failed to verify checksum');
    return $data;
}

