<?php 
namespace Evn\model;

/**
 * Represents a Database details row 
 */
class Address {
    /**
     * the address id
     */
    public $id;

	/**
	 * First line in the address
	 */
	public $lineOne;
	
	/**
	 * Second line in the address
	 */
	public $lineTwo;
	
	/**
	 * The Postal Code
	 */
	public $postalCode;
	
	/**
	 * The city
	 */
	public $city;
}
?>
