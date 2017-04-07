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
        . "`e`.`id` as event_id, `e`.start_time as start_time, `e`.end_time as end_time, "
        . "UNIX_TIMESTAMP(`e`.date_added) as date_added, `e`.priority, "
        . "`d`.`phone` as `phone`, `d`.website as `website`, `d`.cost as `cost` "
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
        $detail = new \Evn\model\Detail($row);
        $detail->activities = \Evn\util\ActivityMapUtil::mapToDetail($db, $detail->id, $categories, $activities);

        // Build the event instance
        $event = new \Evn\model\Event($row, $detail, $db);

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
            . '`a`.`id` as `address_id`, `a`.`address_line_one`, `a`.`address_line_two`, `a`.`postal_code`,`a`.`city` '
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
            $detail = new \Evn\model\Detail($row);
            $detail->activities = \Evn\util\ActivityMapUtil::mapToDetail($db, $detail->id, $categories, $activities);

            // Build the destination instance
            $destination = new \Evn\model\Destination($row, $detail);
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