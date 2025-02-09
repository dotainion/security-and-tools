<?php
namespace tools\infrastructure;

class NumberHelper
{
    public static function to2dp($number):string{
        return number_format((float)$number, 2, '.', '');
    }
}

?>