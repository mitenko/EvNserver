<?php

/**
 * Route to return the list of ongoing events
 */
$app->get('/api/getEvents', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $categories = $request->getQueryParam('category');
    $activities = $request->getQueryParam('activity');

    $query = "SELECT "
        . "`d`.id, `d`.name, `d`.short_desc, `d`.long_desc, `d`.thumb_url, `d`.image_url, `d`.phone, "
        . "`e`.`id` as event_id, UNIX_TIMESTAMP(`e`.start_time) as start_time, UNIX_TIMESTAMP(`e`.end_time) as end_time, "
        . "UNIX_TIMESTAMP(`e`.date_added) as date_added, `e`.priority "
        . "FROM event as `e` LEFT JOIN detail as `d` ON `e`.detail_id=`d`.`id` ";

    /** extra joins if we're limiting categories **/
    if (!empty($categories)) {
        $query .= 'LEFT JOIN detail_activity_map as `m1` ON `m1`.`detail_id`=`d`.`id` '
            . 'LEFT JOIN category_activity_map as `m2` ON `m2`.`activity_id`=`m1`.`activity_id` ';
    }

    $query .= "WHERE "
        . "end_time >= UNIX_TIMESTAMP() ";
    if (!empty($categories)) {
        $query .= " AND (";
        $category_query = array();
        foreach($categories as $key => $value) {
            $category_query[] = '`m2`.`category_id`=:category'.$key;
        }
        $query .= implode(' OR ', $category_query) . ")";

        if (!empty($activities)) {
            $query .= " AND (";
            $activity_query = array();
            foreach($activities as $key => $value) {
                $activity_query[] = '`m1`.`activity_id`=:activity'.$key;
            }
            $query .= implode(' OR ', $activity_query) . ")";
        }
    }

    $query .= " ORDER BY "
        . " `e`.priority DESC, `e`.end_time ASC";

    $stmt = $db->prepare($query);

    /*** bind the categories ***/
    if (!empty($categories)) {
        foreach ($categories as $key => &$value) {
            $stmt->bindParam(':category' . $key, $value);
        }
        if (!empty($activities)) {
            foreach ($activities as $key => &$value) {
                $stmt->bindParam(':activity' . $key, $value);
            }
        }
    }
    $stmt->execute();

    $data = array();
    while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
        // Build the detail instance
        $detail = new \Evn\model\Detail();
        $detail->id = intval($row['id']);
        $detail->name = $row['name'];
        $detail->shortDesc = $row['short_desc'];
        $detail->longDesc = $row['long_desc'];
        $detail->thumbURL = $row['thumb_url'];
        $detail->imageURL = $row['image_url'];
        $detail->phone = $row['phone'];

        $detail->activities = \Evn\util\ActivityMapUtil::mapToDetail($db, $detail->id, $categories, $activities);

        // Build the event instance
        $event = new \Evn\model\Event();
        $event->detail = $detail;
        $event->id = intval($row['event_id']);
        $event->unixStartTime = intval($row['start_time']);
        $event->readableStartTime = date(DATETIME_FORMAT, intval($row['start_time']));
        $event->unixEndTime = intval($row['end_time']);
        $event->readableEndTime = date(DATETIME_FORMAT, intval($row['end_time']));

        // Map the destinations for this event
        $eventQuery = 'SELECT `map`.`destination_id` as `destination_id` '
            . 'FROM `event_destination_map` as `map` '
            . 'WHERE `map`.`event_id`='.$event->id;
        $eventStmt = $db->prepare($eventQuery);
        $eventStmt->execute();
        $destIds = array();
        while (($eventRow = $eventStmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            $destIds[] = intval($eventRow['destination_id']);
        }
        $event->destinations = $destIds;

        $data[] = $event;
    }

    return $response->withJson(
        array(
            'data' => $data,
            'query' => $query
        ));
});

/**
 * Route to return the list of locations within a defined space
 */
$app->get('/api/getDestinations', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $params = $request->getQueryParams();
    $categories = $request->getQueryParam('category');
    $activities = $request->getQueryParam('activity');

    if (!empty($params['southwest_longitude']) && !empty($params['southwest_latitude']) &&
        !empty($params['northeast_longitude']) && !empty($params['northeast_latitude'])) {

        $query = "SELECT "
            . '`d`.id, `d`.name, `d`.short_desc, `d`.long_desc, `d`.thumb_url, `d`.image_url, `d`.phone, '
            . '`dest`.`latitude`, `dest`.`longitude`, '
            . '`a`.`address_line_one`, `a`.`address_line_two`, `a`.`postal_code`,`a`.`city` '
            . 'FROM destination as `dest` '
            . 'LEFT JOIN `detail` as `d` ON `dest`.`detail_id`=`d`.`id` '
            . 'LEFT JOIN `address` as `a` ON `a`.`id`=`dest`.`address_id` ';


        /** extra joins if we're limiting categories **/
        if (!empty($categories)) {
            $query .= 'LEFT JOIN detail_activity_map as `m1` ON `m1`.`detail_id`=`d`.`id` '
                . 'LEFT JOIN category_activity_map as `m2` ON `m2`.`activity_id`=`m1`.`activity_id` ';
        }

        $query .= " WHERE "
            . "`dest`.`longitude` >= :southwest_longitude "
            . " AND `dest`.`longitude` <= :northeast_longitude "
            . " AND `dest`.`latitude` >= :southwest_latitude "
            . " AND `dest`.`latitude` <= :northeast_latitude ";

        if (!empty($categories)) {
            $query .= " AND (";
            $category_query = array();
            foreach($categories as $key => $value) {
                $category_query[] = '`m2`.`category_id`=:category'.$key;
            }
            $query .= implode(' OR ', $category_query) . ")";

            if (!empty($activities)) {
                $query .= " AND (";
                $activity_query = array();
                foreach($activities as $key => $value) {
                    $activity_query[] = '`m1`.`activity_id`=:activity'.$key;
                }
                $query .= implode(' OR ', $activity_query) . ")";
            }
        }
        $stmt = $db->prepare($query);

        /*** bind the location parameters ***/
        $stmt->bindParam(':southwest_longitude', $params['southwest_longitude'], \PDO::PARAM_STR);
        $stmt->bindParam(':northeast_longitude', $params['northeast_longitude'], \PDO::PARAM_STR);
        $stmt->bindParam(':southwest_latitude', $params['southwest_latitude'], \PDO::PARAM_STR);
        $stmt->bindParam(':northeast_latitude', $params['northeast_latitude'], \PDO::PARAM_STR);

        /*** bind the categories ***/
        if (!empty($categories)) {
            foreach ($categories as $key => &$value) {
                $stmt->bindParam(':category' . $key, $value);
            }
            if (!empty($activities)) {
                foreach ($activities as $key => &$value) {
                    $stmt->bindParam(':activity' . $key, $value);
                }
            }
        }

        $stmt->execute();

        $data = array();
        while (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            // Build the destination's detail instance
            $detail = new \Evn\model\Detail();
            $detail->id = intval($row['id']);
            $detail->name = $row['name'];
            $detail->shortDesc = $row['short_desc'];
            $detail->longDesc = $row['long_desc'];
            $detail->thumbURL = $row['thumb_url'];
            $detail->imageURL = $row['image_url'];
            $detail->phone = $row['phone'];

            $detail->activities = \Evn\util\ActivityMapUtil::mapToDetail($db, $detail->id, $categories, $activities);

            // Build the destination instance
            $destination = new \Evn\model\Destination();
            $destination->detail = $detail;
            $destination->longitude = floatval($row['longitude']);
            $destination->latitude = floatval($row['latitude']);

            // And the address
            $address = new \Evn\model\Address();
            $address->line_one = $row['address_line_one'];
            $address->line_two = $row['address_line_two'];
            $address->postal_code = $row['postal_code'];
            $address->city = $row['city'];

            $destination->address = $address;

            $data[] = $destination;
        }

        return $response->withJson(
            array(
                'data' => $data,
                'query' => $query
            ));
    }

    return $response;
});

/**
 * Route to return the list of locations within a defined space
 */
$app->get('/api/getCategoryData', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;

    $query = 'SELECT `c`.`id`,`c`.`name` '
        . ' FROM `category` as `c`';
    $stmt = $db->prepare($query);
    $stmt->execute();

    $data = array();
    while (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
        $categoryData = new \Evn\model\CategoryData();
        $categoryData->id = intval($row['id']);
        $categoryData->name = $row['name'];

        $categoryData->activities = \Evn\util\ActivityMapUtil::mapToCategory($db, $categoryData->id);

        $data[] = $categoryData;
    }

    return $response->withJson(
        array(
            'data' => $data,
            'query' => $query
        ));
});