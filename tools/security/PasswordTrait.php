<?php
namespace tools\security;

use tools\infrastructure\Assert;
use tools\infrastructure\exeptions\NotAuthenticatedException;
use tools\infrastructure\Password;

trait PasswordTrait
{
    public function assertSignInPass(Password $hasPassword, string $plainPassword): bool
    {
        Assert::validPassword($plainPassword, 'Invalid password.');
        $isValid = password_verify($plainPassword, $hasPassword->toString());
        if(!$isValid){
            throw new NotAuthenticatedException('Invalid password.');
        }
        return true;
    }
}