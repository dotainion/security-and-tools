<?php
namespace tools\infrastructure;

interface IUser extends IObjects{
    public function id():IId;

    public function token():?string;

    public function setToken(string $token):void;

    public function addressId():?IId;
}