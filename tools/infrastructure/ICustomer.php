<?php
namespace tools\infrastructure;

interface ICustomer extends IObjects{
    public function id():Id;

    public function name():string;

    public function email():?string;
    
    public function phone():?string;

    public function gender():?string;
}