<?php
namespace tools\infrastructure;

use InvalidArgumentException;

class TaxHelper{
    const NOTIFY = 'notify';
    const AUTO = 'auto';
    const NOTIFY_AND_AUTO = 'notify-and-auto';

    public static function valid($string, string $message = 'Invalid tax notification value.'):bool{
        if(!in_array($string, [self::AUTO, self::NOTIFY, self::NOTIFY_AND_AUTO])){
            throw new InvalidArgumentException($message);
        }
        return true;
    }
}