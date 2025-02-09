<?php
namespace tools\infrastructure;

use ReflectionClass;

class SearchRequest extends Request{

    protected array $deniedMethods = [
        '__REQUEST__',
        'request',
        'uuid', 
        'hasArgs', 
        'where',
        'isPost',
        'file',
        'get',
    ];
    
    public function __construct(){
        parent::__construct();
    }

    public function uuid(string $attribute):?Id{
        $id = new Id($this->get($attribute));
        if($id->hasId()){
            return $id;
        }
        return null;
    }

    public function hasArgs():bool{
        return !empty($this->where());
    }

    public function limit():?int{
        return $this->get('limit');
    }

    public function active():?bool{
        return $this->get('active');
    }

    public function inactive():?bool{
        return $this->get('inactive');
    }

    public function id():?Id{
        return $this->uuid('id');
    }

    public function categoryId():?Id{
        return $this->uuid('categoryId');
    }

    public function name():?string{
        return $this->get('name');
    }

    public function completed():?bool{
        return $this->get('completed');
    }

    public function canceled():?bool{
        return $this->get('canceled');
    }

    public function favorite():?bool{
        return $this->get('favorite');
    }

    public function desc():?bool{
        return $this->get('desc');
    }

    public function where():array{
        $reflect = new ReflectionClass($this);

        $where = [];
        foreach($reflect->getMethods() as $method){
            if(in_array($method->getName(), $this->deniedMethods)){
                continue;
            }
            if($this->{$method->getName()}() !== null){
                $where[$method->getName()] = $this->{$method->getName()}();
            }
        }
        return $where;
    }
}