<?php
namespace tools\security;

use tools\infrastructure\DateHelper;
use tools\infrastructure\ICredential;
use tools\infrastructure\Id;
use tools\infrastructure\IId;
use tools\infrastructure\IUser;
use tools\infrastructure\Password;
use tools\infrastructure\Token;


class Security implements ICredential{
	protected Id $id;
	protected Token $token;
	protected ?IUser $user = null;
	protected ?DateHelper $expire = null;
	protected Password $password;
	protected ?string $refreshToken = null;

	public function __construct(){
		$this->id = new Id();
		$this->token = new Token();
		$this->password = new Password();
	}

	public function id():IId{
		return $this->id;
	}

	public function user():?IUser{
		return $this->user;
	}

	public function token():Token{
		return $this->token;
	}

	public function expire():?DateHelper{
		return $this->expire;
	}

	public function hasPassword():bool{
		return $this->password->hasPassword();
	}

	public function password():Password{
		return $this->password;
	}

	public function refreshToken():?string{
		return $this->refreshToken;
	}

	public function setId(string $id):void{
		$this->id->set($id);
	}	

	public function setUser(IUser $user):void{
		$this->user = $user;
	}

	public function setToken(string $token):void{
		if(!$this->token->stringIsValid($token)){
			return;
		}
		$this->token->set($token);
	}

	public function setExpire(?string $expire):void{
		if($expire === null){
			return;
		}
		$this->expire = (new DateHelper())->set($expire);
	}

	public function setPassword(string $password):void{
		$this->password->set($password);
	}

	public function setRefreshToken(?string $refreshToken):void{
		if($refreshToken === null){
			return;
		}
		$this->refreshToken = $refreshToken;
	}
}

?>