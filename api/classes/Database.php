<?php 
namespace Evn\classes;

/**
 * Represents a MySQL connection to the database
 */
class Database {
	/**
	 * Database Name
	 */
	const databaseName = "eventsna_db";
	
	/**
	 * User Name
	 */
	const userName = "eventsna_evn";
	
	/**
	 * User Password
	 */
	const userPassword = "CM63@?)6L,X_";
	
	/**
	 * Database Connection
	 */
	var $DBH;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->DBH = new \PDO("mysql:host=localhost;dbname=".self::databaseName, 
				self::userName, self::userPassword);
		$stmt = $this->DBH->prepare('SET NAMES utf8');
		$stmt->execute();		
	}
	
	/**
	 * Executes a query in a way that it populates the passed object
	 */
	function prepare($query) {
		return $this->DBH->prepare($query);
	}
	
	/**
	 * Specifies the class to be fetched into
	 */
	function execute($query) {
		return $this->DBH->execute($query);
	}
	
	/**
	 * Specifies the class to be fetched into
	 */
	function fetch() {
		$this->DBH->execute();
	}
}
?>