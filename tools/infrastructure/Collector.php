<?php
namespace tools\infrastructure;

use InvalidArgumentException;
use tools\infrastructure\exeptions\NoResultsException;

class Collector{
    protected $collected = [];

    public function add($item):void{
        $this->collected[] = $item;
    }

    public function prepend($item):void{
        $this->collected = [$item, ...$this->collected];
    }

    public function mergeCollection(Collector $collector):self{
        foreach($collector->list() as $item){
            $this->add($item);
        }
        return $this;
    }
    
    public function list():array{
        return $this->collected;
    }
    
    public function first(){
        return $this->collected[0] ?? null;
    }
    
    public function last(){
        return $this->collected[count($this->collected) -1] ?? null;
    }

    public function count():int{
        return count($this->collected);
    }

    public function clear():self{
        $this->collected = [];
        return $this;
    }

    public function hasItem():bool{
        return !empty($this->collected);
    }

    public function isEmpty():bool{
        return !$this->hasItem();
    }

    public function idArray():array{
        $idArray = [];
        foreach($this->list() as $item){
            $idArray[] = $item->id();
        }
        return $idArray;
    }

    public function attrArray($attr):array{
        $attrArray = [];
        foreach($this->list() as $item){
            $attrArray[] = $item->$attr();
        }
        return $attrArray;
    }

    public function filter($attr, $value):Collector{
        $collector = new Collector();
        foreach($this->list() as $item){
            if($item->$attr() === $value){
                $collector->add($item);
            }
        }
        return $collector;
    }

    public function assertHasItem(string $message='No results'):bool{
        if(!$this->hasItem()){
            throw new NoResultsException($message);
        }
        return true;
    }

    public function assertItemNotExist(string $message='Already exist'):bool{
        if($this->hasItem()){
            throw new InvalidArgumentException($message);
        }
        return true;
    }
}