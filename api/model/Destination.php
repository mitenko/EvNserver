<?php 
namespace Evn\model;

/**
 * Represents a Database details row 
 */
class Destination {
    /**
     * Id
     */
    public $id;

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
     * The detail
     */
    public $detail;

    /**
     * The display icon name
     */
    public $displayIcon;

	/**
	 * Will bind the given DB row
	 */
	public function __construct($row, $detail) {
        $this->detail = $detail;
        $this->id = intval($row['destId']);
        $this->longitude = floatval($row['longitude']);
        $this->latitude = floatval($row['latitude']);
        if (!empty($detail->activities)) {
            $this->displayIcon=$detail->activities[0]->category;
        }

        // And the address
        $address = new \Evn\model\Address();
        $address->id = $row['address_id'];
        $address->lineOne = $row['address_line_one'];
        $address->lineTwo = $row['address_line_two'];
        $address->postalCode = $row['postal_code'];
        $address->city = $row['city'];

        $this->address = $address;
    }
}
?>
