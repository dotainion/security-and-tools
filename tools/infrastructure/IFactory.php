<?php
namespace tools\infrastructure;

interface IFactory{

    public function map(array $records):self;

    public function mapResult($record):IObjects;

    public function isValid($record):bool;

    public function isDefaultUuId($uuidBytes):bool;

    public function isValidUUid($uuid):bool;

    public function uuid($uuidBytes):?string;
}