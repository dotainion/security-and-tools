<?php
namespace tools\infrastructure;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

class DateHelper
{
    protected string $format = "Y-m-d H:i:s";
    protected DateTime $date;
    protected static string $timezone = 'America/Grenada';

    public function __construct(?string $date=null){
        $date !== null && $this->set($date);
    }

    public function __toString(){
        $stringDate = $this->date->format($this->format);
        Assert::validDate($stringDate);
        return $stringDate;
    }

    private function timezone():DateTimeZone{
        return new DateTimeZone(self::$timezone);
    }

    public static function setTimezone(string $timezone):void{
        self::$timezone = $timezone;
    }

    public function toString():string{
        return $this->__toString();
    }

    public function toStringAsDateOnly():string{
        return explode(' ', $this->__toString())[0];
    }

    public function toStringAsTimeOnly():string{
        return explode(' ', $this->__toString())[1];
    }

    public function new():self{
        $this->date = new DateTime('now', $this->timezone());
        return $this;
    }

    public function year():string{
        return $this->modify('Y');
    }

    public function month():string{
        return $this->modify('m');
    }

    public function date():string{
        return $this->modify('d');
    }

    public function daysInMonth():int{
        return cal_days_in_month(CAL_GREGORIAN, $this->month(), $this->year());
    }

    public function modify(string $format):string{
        return $this->date->format($format);
    }

    public function addDays(int $days):self{
        $this->date->modify('+'.$days.' days');
        return $this;
    }

    public function subDays(int $days):self{
        $this->date->modify('-'.$days.' days');
        return $this;
    }

    public function addMinutes(int $days):self{
        $this->date->modify('+'.$days.' minutes');
        return $this;
    }

    public function subMinutes(int $days):self{
        $this->date->modify('-'.$days.' minutes');
        return $this;
    }

    public function set(string $dateTime):self{
        if(!$this->isValid($dateTime)){
            throw new InvalidArgumentException('Invalid Date Time');
        }
        $this->date = new DateTime($dateTime, $this->timezone());
        return $this;
    }

    public function isValid($dateTime):bool{
        $date = DateTime::createFromFormat($this->format, $dateTime);
        if(!$date || $date->format($this->format) === $this->nullable()){
            return false;
        }
        return $date && $date->format($this->format) == $dateTime;
    }

    public function expired(string $dateTime):bool{
        return new DateTime($this->toString(), $this->timezone()) >= new DateTime($dateTime, $this->timezone());
    }

    public function format(){
        return $this->format;
    }

    public function dateTime():DateTime{
        return $this->date;
    }

    public function difference(DateHelper $startDate, DateHelper $endDate):string{
        return $endDate->dateTime()->diff($startDate->dateTime())->format("%a");
    }

    public function nullable():string{
        return '0000-00-00 00:00:00';
    }
}

?>