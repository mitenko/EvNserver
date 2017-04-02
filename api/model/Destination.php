<?php 
namespace Evn\model;

/**
 * Represents a Database details row 
 */
class Destination {
	/**
	 * The latitude
	 */
	public $latitude;
	
	/**
	 * The longitude
	 */
	public $longitude;

	/**
	 * The address
	 */
	public $address;

	/**
	 * Will bind the given DB row
	 */
	public function __construct($row, $detail) {
        $this->detail = $detail;
        $this->detail = $detail;
        $this->longitude = floatval($row['longitude']);
        $this->latitude = floatval($row['latitude']);

        // And the address
        $address = new \Evn\model\Address();
        $address->line_one = $row['address_line_one'];
        $address->line_two = $row['address_line_two'];
        $address->postal_code = $row['postal_code'];
        $address->city = $row['city'];

        $this->address = $address;
    }
}
?>
