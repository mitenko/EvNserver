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
     * Priority
     */
    public $priority;

    /**
     * Priority
     */
    public $readablePriority;

    /**
     * Array of SimpleDestinations attached to this Event
     */
    public $destinations;

    /**
     * Will bind the given DB row
     */
    public function __construct($row, $detail, $db) {
        $this->detail = $detail;
        $this->id = intval($row['event_id']);
        $this->unixStartTime = intval($row['start_time']);
        $this->readableStartTime = date(DATETIME_FORMAT, intval($row['start_time']));
        $this->unixEndTime = intval($row['end_time']);
        $this->readableEndTime = date(DATETIME_FORMAT, intval($row['end_time']));
        $this->priority = intval($row['priority']);
        $this->readablePriority = \Evn\model\Event::toReadablePriority($this->priority);

        // Map the destinations for this event
        $eventQuery = 'SELECT `map`.`destination_id` as `destination_id` '
            . 'FROM `event_destination_map` as `map` '
            . 'WHERE `map`.`event_id`='.$this->id;
        $eventStmt = $db->prepare($eventQuery);
        $eventStmt->execute();
        $destIds = array();
        while (($eventRow = $eventStmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            $destIds[] = intval($eventRow['destination_id']);
        }
        $this->destinations = $destIds;
    }

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