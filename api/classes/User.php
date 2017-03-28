<?php 
namespace Evn\classes;

/**
 * Represents an application user
 */
class User {
	/**
	 * Client Type Constant
	 */
	const CLIENTTYPE = "clienttype";
	
	/**
	 * Admin Type Constant
	 */
	const ADMINTYPE = "admintype";
	
	/**
	 * Invalid Type Constant
	 */
	const INVALIDTYPE = "invalidtype";
	
	/**
	 * The User Type
	 */
	var $userType;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->userType = Self::INVALIDTYPE;
	}
	
	/**
	 * Sets the User Type 
	 * @param unknown $type
	 */
	public function setType($type) {
		$this->userType = $type;
	}
	
	/**
	 * Returns the User Type 
	 * @param unknown $type
	 */
	public function getType() {
		return $this->userType;
	}
	
	
	/**
	 * Returns if the user is an Admin type
	 */
	function isAdmin() {
		return $this->userType == self::ADMINTYPE;
	}
	
	/**
	 * Returns if the user is unauthorized / invalid
	 */
	function isValid() {
		return $this->userType != self::INVALIDTYPE;
	}
}