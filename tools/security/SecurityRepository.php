<?php
namespace tools\security;

use tools\infrastructure\Repository;
use tools\infrastructure\Collector;
use tools\infrastructure\DateHelper;
use tools\infrastructure\Id;
use tools\infrastructure\Token;

class SecurityRepository extends Repository{
    protected SecurityFactory $factory;

    public function __construct(){
        $this->permissionOff();
        parent::__construct();
        $this->factory = new SecurityFactory();
    }
    
    public function updateToken(Id $id, Token $token, DateHelper $expire):void{
        $this->update('credential')
            ->column('token', $token->toString())
            ->column('expire', $expire->toString());
        $this->where()->eq('id', $this->uuid($id));
        $this->execute();
    }
    
    public function removeToken(Id $id):void{
        $this->update('credential')
            ->column('token', '');
        $this->where()->eq('id', $this->uuid($id));
        $this->execute();
    }

    public function listSecurity(array $where = []):Collector{
        $this->select(Setup::userTableName())
            ->join()->inner('credential', 'id', 'user', 'id')
            ->cursor()->join()->inner('role', 'userId', 'user', 'id')
            ->cursor()->join()->inner('rolePermission', 'userId', 'user', 'id');
            
        if(isset($where['id'])){
            $this->where()->eq('id', $this->uuid($where['id']));
        }
        if(isset($where['email'])){
            $this->where()->eq('email', $where['email']);
        }
        if(isset($where['phoneNumber'])){
            $this->where()->eq('phoneNumber', $where['phoneNumber']);
        }
        if(isset($where['foreignId'])){
            $this->where()->eq('foreignId', $where['foreignId']);
        }
        if(isset($where['password'])){
            $this->where()->eq('password', $where['password'], 'credential');
        }
        if(isset($where['token'])){
            $this->where()->eq('token', $where['token'], 'credential');
        }
        if(isset($where['expire'])){
            $this->where()->lessThanOrEqualTo('expire', $where['expire'], 'credential');
        }
        $this->execute();

        return $this->factory->map(
            $this->results()
        );
    }
}