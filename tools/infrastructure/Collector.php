<?php
namespace tools\infrastructure;

use Exception;
use InvalidArgumentException;
use src\module\school\objects\Group;
use tools\infrastructure\exeptions\NoResultsException;

class Collector{
    protected $collected = [];

    public function add($item):void{
        if($item instanceof IObjects && $this->includes($item->id()->toString())){
            return;
        }
        $this->collected[] = $item;
    }

    public function prepend($item):void{
        if($item instanceof IObjects && $this->includes($item->id()->toString())){
            return;
        }
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
            $content = $item->$attr();
            if(
                !is_null($content) && 
                !is_bool($content) && 
                !is_string($content) && 
                method_exists($content, 'toString')
            ){
                $content = $item->$attr()->toString();
            }
            if($content === $value){
                $collector->add($item);
            }
        }
        return $collector;
    }

    public function remove(IObjects $item):self{
        $copies = [];
        foreach($this->list() as $record){
            if($record->id()->toString() !== $item->id()->toString()){
                $copies[] = $record;
            }
        }
        $this->collected = $copies;
        return $this;
    }

    public function includes($value, $attr='id'):bool{
        foreach($this->list() as $record){
            if(!method_exists($record, $attr)){
                throw new Exception('Method not exist in collector records.');
            }
            if(
                method_exists($record->$attr(), 'toString') &&
                $record->$attr()->toString() === $value || 
                method_exists($record->$attr(), 'toString') &&
                $record->$attr() === $value
            ){
                return true;
            }
        }
        return false;
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