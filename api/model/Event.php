<?php 
namespace Evn\model;

/**
 * Represents a Database details row 
 */
class Event {
    const ULTRA = 'Ultra';
    const HIGH = 'High';
    const MED = 'Med';
    const LOW = 'Low';

	/**
	 * The Detail class for the event
	 */
	public $detail;

    /**
     * The event id
     */
    public $id;
	
	/**
	 * Event Start Time in UNIX
	 */
	public $unixStartTime;

    /**
     * Event Start Time in human readable format
     */
    public $readableStartTime;
	
	/**
	 * Event End Time 
	 */
	public $unixEndTime;

    /**
     * Event End Time in human readable format
     */
    public $readableEndTime;

    /**
     * Date Added
     */
    public $unixDateAdded;

    /**
     * Date Added
     */
    public $readableDateAdded;

    /**
     * Priority
     */
    public $priority;

    /**
     * Priority
     */
    public $readablePriority;

    /**
     * Will return a readable version of the priority value
     */
    public static function toReadablePriority($priority) {
        switch($priority) {
            case 0 :
                return self::ULTRA;
            case 1 :
                return self::HIGH;
            case 2 :
                return self::MED;
            case 3 :
                return self::LOW;
        }
        return '';
    }
}
?>