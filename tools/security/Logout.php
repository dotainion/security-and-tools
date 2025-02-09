<?php
namespace tools\security;

use tools\infrastructure\Id;

class Logout{
    protected SecurityRepository $repo;

    public function __construct(){
        $this->repo = new SecurityRepository();
    }

    public function logout(Id $id):void{
        $this->repo->removeToken($id);
    }
}