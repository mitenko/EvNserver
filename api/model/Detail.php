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
     * The location / event's contact website URL
     */
    public $website;

    /**
     * The location / event's price
     */
    public $cost;

    /**
     * The contact email
     */
    public $email;

    /**
     * Array of activity data types
     */
    public $activities;

    /**
	 * Will bind the given DB row
	 */
    public function __construct($row) {
        $this->id = intval($row['detailId']);
        $this->name = $row['name'];
        $this->shortDesc = $row['short_desc'];
        $this->longDesc = $row['long_desc'];
        $this->thumbURL = $row['thumb_url'];
        $this->imageURL = $row['image_url'];
        $this->phone = $row['phone'];
        $this->website = $row['website'];
        $this->cost = intval($row['cost']);
        $this->email = $row['email'];
	}
}
?>
