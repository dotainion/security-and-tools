<?php
namespace tools\infrastructure;

use Exception;
use permission\database\Permission;
use tools\security\SecurityManager;
use tools\infrastructure\exeptions\NoResultsException;
use Throwable;

class Service extends Request{
    protected SecurityManager $securityManager;
    protected Collector $meta;
    protected Collector $collector;
    protected Collector $relationships;
    protected array $_excluded = [
        '__construct'
    ];

    public function __construct(bool $authCheck=true){
        $this->__REQUEST__();
        Permission::setRequirePermission($authCheck);
        $this->securityManager = new SecurityManager();
        $this->meta = new Collector();
        $this->collector = new Collector();
        $this->relationships = new Collector();
        if($authCheck){
            $this->assertUserAccessToken();
        }
    }

    public function user():IUser{
        return $this->securityManager->user();
    }

    public function security():SecurityManager{
        return $this->securityManager;
    }

    public function assertUserAccessToken():bool{
        $this->securityManager->assertUserAccess();
        return true;
    }

    public function assertHasItem():bool{
        if(!$this->output()->hasItem()){
            throw new NoResultsException('No results');
        }
        return true;
    }

    public function setRelationship($data){
        if($data instanceof Collector){
            foreach($data->list() as $object){
                $this->relationships->add($this->toJson($object));
            }
        }else if ($data instanceof IObjects){
            $this->relationships->add($this->toJson($data));
        }else{
            throw new Exception('Service response receive a object and dont know what to do with it.');
        }
    }

    public function setOutput($data){
        if($data instanceof Collector){
            foreach($data->list() as $object){
                $this->collector->add($this->toJson($object));
            }
        }else if ($data instanceof IObjects){
            $this->collector->add($this->toJson($data));
        }else{
            throw new Exception('Service response receive a object and dont know what to do with it.');
        }
    }

    public function setMeta($data){
        if($data instanceof Collector){
            foreach($data->list() as $object){
                $this->meta->add($this->toJson($object));
            }
        }else if ($data instanceof IObjects){
            $this->meta->add($this->toJson($data));
        }else{
            throw new Exception('Service response receive a object and dont know what to do with it.');
        }
    }

    public function relationship():Collector{
        return $this->relationships;
    }

    public function output():Collector{
        return $this->collector;
    }

    public function meta():Collector{
        return $this->meta;
    }

    public function mergeOutput(Service $service):void{
        foreach($service->output()->list() as $output){
            $this->collector->add($output);
        }
    }

    public function mergeMeta(Service $service):void{
        foreach($service->relationship()->list() as $meta){
            $this->meta->add($meta);
        }
    }

    public function mergeRelationship(Service $service):void{
        foreach($service->relationship()->list() as $relationship){
            $this->relationships->add($relationship);
        }
    }

    public function sendResponse(){
        $this->assertHasItem();
        $data = ['data' => $this->output()->list()];
        if($this->relationship()->hasItem()){
            $data['included'] = $this->relationship()->list();
        }
        if($this->meta()->hasItem()){
            $data['meta'] = $this->meta()->list();
        }
        echo json_encode($data);
    }

    public function dataType($data){
        if(is_null($data) || is_bool($data) || is_int($data) || is_float($data) || is_array($data)){
            return $data;
        }
        return (string)$data;
    }

    public function toJson($object){
        $json = [];
        if(!$object instanceof IObjects){
            throw new Exception('Service response receive a object and dont know what to do with it.');
        }
        foreach(get_class_methods($object) as $method){
            try{
                if(!in_array($method, $this->_excluded) && !str_contains($method, 'set') && !str_contains($method, 'new')){
                    $json[$method] = $this->dataType($object->$method());
                }
            }catch(Throwable $ex){
                if($object->$method() instanceof Collector){
                    $jsonList = [];
                    foreach($object->$method()->list() as $obj){
                        $jsonList[] = $this->toJson($obj);
                    }
                    $json[$method] = $jsonList;
                }else{
                    $json[$method] = $this->toJson($object->$method());
                }
            }
        }
        if(!isset($json['id'])){
            throw new Exception('Each object just have a id when converting into json response.');
        }
        $id = $json['id'];
        unset($json['id']);
        return [
            'id' => $id,
            'type' => lcfirst((new \ReflectionClass($object))->getShortName()),
            'attributes' => $json,
        ];
    }
}

