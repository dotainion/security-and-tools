<?php
namespace tools\module\login\repository;

use tools\infrastructure\Repository;
use tools\infrastructure\Collector;
use tools\infrastructure\ICredential;
use tools\infrastructure\Id;
use tools\infrastructure\Password;
use tools\infrastructure\Token;
use tools\module\login\factory\CredentialFactory;

class CredentialRepository extends Repository{
    protected CredentialFactory $factory;

    public function __construct(){
        parent::__construct();
        $this->factory = new CredentialFactory();
    }
    
    public function create(ICredential $creds):void{
        $this->insert('credential')        
            ->column('id', $this->uuid($creds->id()))
            //->column('expire', $creds->expire())
            ->column('token', $creds->token()->toString());
            //->column('refreshToken', $creds->refreshToken());
        if($creds->password()->hasPassword()){
            $this->column('password', $creds->password()->toHash());
        }
        $this->execute();
    }
    
    public function editPassword(Id $id, Password $password, Password $newPassword):void{
        $this->update('credential')        
            ->column('password', $newPassword->toHash())
            ->where()
            ->eq('id', $this->uuid($id))
            ->eq('password', $password->toHash());
        $this->execute();
    }
    
    public function updatePasswordByRefreshToken(Id $id, Password $password, Token $token):void{
        $this->update('credential')        
            ->column('password', $password->toHash())
            ->where()
            ->eq('id', $this->uuid($id))
            ->eq('refreshToken', $token);
        $this->execute();
    }
    
    public function unsetTokenRefreshToken(Id $id, Token $token):void{
        $this->update('credential')        
            ->column('refreshToken', '')
            ->where()
            ->eq('id', $this->uuid($id))
            ->eq('refreshToken', $token);
        $this->execute();
    }
    
    public function listHasCredential(array $where):Collector{
        $this->select('credential')
            ->join()->inner('user', 'id', 'credential', 'id')
            ->cursor()->where()->eq('hide', 0, 'user');

        if(isset($where['id'])){
            $this->where()->eq('id', $this->uuid($where['id']));
        }
        if(isset($where['password'])){
            $this->where()->eq('password', $where['password']);
        }
        if(isset($where['refreshToken'])){
            $this->where()->eq('refreshToken', $where['refreshToken']);
        }
        $this->pagination()->set($where);
        $this->execute();
        return $this->factory->map(
            $this->results()
        );
    }
}