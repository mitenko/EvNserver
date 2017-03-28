<?php 
namespace Evn\model;

/**
 * Represents a Database details row 
 */
class CategoryData {
	/**
	 * The category's id
	 */
	public $id;
	
	/**
	 * The category's name
	 */
	public $name;

	/**
     * The activities associated with this category
     */
	public $activities;
}
?>