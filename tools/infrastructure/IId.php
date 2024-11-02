<?php
namespace tools\infrastructure;

interface IId{
    public function toString():string;
    public function set(?string $uuid):self;
}