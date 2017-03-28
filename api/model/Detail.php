<?php 
namespace Evn\model;

/**
 * Represents a Database details row 
 */
class Detail {
	/**
	 * Unique ID
	 */
	public $id;
	
	/**
	 * The location / event's name
	 */
	public $name;
	
	/**
	 * The location / event's short description
	 */
	public $shortDesc;
	
	/**
	 * The location / event's long description
	 */
	public $longDesc;
	
	/**
	 * The location / event's thumbnail URL
	 */
	public $thumbURL;
	
	/**
	 * The location / event's image URL
	 */
	public $imageURL;

    /**
     * The location / event's contact phone
     */
    public $phone;

    /**
     * Array of activity data types
     */
    public $activities;
}
?>
