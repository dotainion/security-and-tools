<?php
namespace tools\infrastructure;

use permission\database\Permission;
use permission\SqlRepository;
use tools\security\Session;

class Repository extends SqlRepository{
	protected static ?string $userId = null;

	public function __construct(){
		parent::__construct();
		if(Permission::userId() === null && Permission::requirePermission()){
			(Session::user() !== null) && Permission::setUserId(Session::user()->id()->toString());
		}
		(self::$userId !== null) && $this->setUserId(self::$userId);
	}
}

?>