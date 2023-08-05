<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class NationalCodeChecker implements Rule
{
    public function passes($attribute, $value): bool
    {
        if(!preg_match('/^\d{10}$/',$value))
            return false;
        for($i=0;$i<10;$i++) {
            if (preg_match('/^' . $i . '{10}$/', $value))
                return false;
        }
        for($i=0,$sum=0;$i<9;$i++)
            $sum+=((10-$i)*intval(substr($value, $i,1)));
        $ret=$sum%11;
        $parity=intval(substr($value, 9,1));
        if(($ret<2 && $ret==$parity) || ($ret>=2 && $ret==11-$parity))
            return true;
        return false;
    }
    public function message(): string
    {
        return ('کد ملی وارد شده معتبر نمی باشد');
    }
}
