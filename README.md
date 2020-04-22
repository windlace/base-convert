Base convert
---
Convert an arbitrarily large number from any base to any base.

#### Install:
```php
composer require cast/base-convert
```

`string convBase(string $numberInput, string $fromBaseInput, string $toBaseInput)`\
`$numberInput` - number to convert as a string\
`$fromBaseInput` - base of the number to convert as a string\
`$toBaseInput` - base the number should be converted to as a string

Examples for `$fromBaseInput` and `$toBaseInput`\
`0123456789ABCDEF` for Hexadecimal (Base16)\
`0123456789` for Decimal (Base10)\
`01234567` for Octal (Base8)\
`01` for Binary (Base2)

You can really put in whatever you want and the first character is the 0.


#### Examples:

```php
<?php

require __DIR__.'/vendor/autoload.php';

use function Cast\convBase;

convBase('123', '0123456789', '01234567');
//Convert '123' from decimal (base10) to octal (base8).
//result: 173

convBase('70B1D707EAC2EDF4C6389F440C7294B51FFF57BB', '0123456789ABCDEF', '01');
//Convert '70B1D707EAC2EDF4C6389F440C7294B51FFF57BB' from hexadecimal (base16) to binary (base2).
//result:
//111000010110001110101110000011111101010110000101110
//110111110100110001100011100010011111010001000000110
//001110010100101001011010100011111111111110101011110
//111011

convBase('1324523453243154324542341524315432113200203012', '012345', '0123456789ABCDEF');
//Convert '1324523453243154324542341524315432113200203012' from senary (base6) to hexadecimal (base16).
//result: 1F9881BAD10454A8C23A838EF00F50

convBase('355927353784509896715106760','0123456789','Christopher');
//Convert '355927353784509896715106760' from decimal (base10) to undecimal (base11) using "Christopher" as the numbers.
//result: iihtspiphoeCrCeshhorsrrtrh

convBase('1C238Ab97132aAC84B72','0123456789aAbBcCdD', '~!@#$%^&*()');
//Convert'1C238Ab97132aAC84B72' from octodecimal (base18) using '0123456789aAbBcCdD' as the numbers to undecimal (base11) using '~!@#$%^&*()' as the numbers.
//result: !%~!!*&!~^!!&(&!~^@#@@@&

function convBase($numberInput, $fromBaseInput, $toBaseInput)
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
            $retval = bcadd($retval, bcmul(array_search($number[$i-1], $fromBase),bcpow($fromLen,$numberLen-$i)));
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
        $retval = $toBase[bcmod($base10,$toLen)].$retval;
        $base10 = bcdiv($base10,$toLen,0);
    }
    return $retval;
}

```


Based on https://www.php.net/manual/ru/function.base-convert.php#106546.