<?php
namespace tools\infrastructure;

use InvalidArgumentException;
use tools\security\ValidatePassword;

class Assert extends Repository{
    public static function stringNotEmpty($string, string $message = 'String is empty.'):bool{
        if(empty($string)){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function arrayNotEmpty($array, string $message = 'Array is empty.'):bool{
        self::isArray($array, 'Must be an array.');
        return self::stringNotEmpty($array, $message);
    }

    public static function isArray($array, string $message = 'Not an array.'):bool{
        if(!is_array($array)){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function positiveNumber($number, string $message = 'Must be a positive number.'):bool{
        if(((float)$number??0) <= 0){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function minNumber($number, float $min, string $message = 'Must be more then the minimum value.'):bool{
        if(((float)$number??0) < $min){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function validEmail($email, string $message = 'Invalid email format.'):bool{
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function validUuid($uuid, string $message = 'Invalid id.'):bool{
        if(!(new Id())->isValid((string)$uuid)){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function validUuidArray($uuidArray, string $message = 'Invalid id.'):bool{
        foreach($uuidArray as $uuid){
            self::validUuid($uuid, $message);
        }
        return true;
    }

    public static function validToken($token, string $message = 'Invalid token.'):bool{
        if(!(new Id())->isValid((string)$token)){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function validDate($uuid, string $message = 'Invalid date.'):bool{
        if(!(new DateHelper())->isValid((string)$uuid)){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function validPassword($password, string $message = 'The password you entered is incorrect.', bool $useRequirements=true):bool{
        $validate = new ValidatePassword();
        $validate->validate($password);
        if($validate->chain()->hasError()){
            $validate->chain()->prependMessage($message);
            $useRequirements && $message = $validate->chain()->messages();
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function validPasswordMatch($password, $confirmPassword, string $message = 'Password Mismatch.'):bool{
        Assert::validPassword($password, 'The password you entered is incorrect.');
        Assert::validPassword($confirmPassword, 'Invalid confirm Password.');
        if($password !== $confirmPassword){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function inArray($string, array $list, string $message = 'Not in list.'):bool{
        if(!in_array($string, $list)){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function validNumericValue($string, string $message = 'Must be a number.'):bool{
        if(!is_numeric($string)){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function maxChar($string, int $max, string $message = 'maximum length reach.'):bool{
        if(strlen($string) > $max){
            throw new InvalidArgumentException($message);
        }
        return true;
    }

    public static function pin($pin, ?string $message = null):bool{
        Assert::stringNotEmpty($pin, $message ?: 'Incorrect pin.');
        Assert::positiveNumber($pin, $message ?: 'Incorrect pin.');
        Assert::minNumber($pin, 4, $message ?: 'Pin number should be 4 to 6 digits.');
        Assert::maxChar($pin, 8, $message ?: 'Pin number should be 4 to 6 digits.');
        return true;
    }
}