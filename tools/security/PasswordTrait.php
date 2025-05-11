<?php
namespace tools\security;

use tools\infrastructure\Assert;
use tools\infrastructure\exeptions\NotAuthenticatedException;
use tools\infrastructure\Password;

trait PasswordTrait
{
    public function assertSignInPass(Password $hasPassword, string $plainPassword): bool
    {
        Assert::validPassword($plainPassword, 'The password you entered is incorrect.');
        $isValid = password_verify($plainPassword, $hasPassword->toString());
        if(!$isValid){
            throw new NotAuthenticatedException('The password you entered is incorrect.');
        }
        return true;
    }
}