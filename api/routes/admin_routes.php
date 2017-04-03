<?php
// Routes

/**
 * Route to return the list of ongoing events
 */
$app->get('/adminApi/getEvents', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $sorton = $request->getQueryParam('sorton');
    $sortdir = $request->getQueryParam('sortdir');

    $query = "SELECT "
        . '`d`.id, `d`.name as `name`, `d`.short_desc as `short_desc`, `d`.long_desc as `long_desc`, '
        . '`d`.thumb_url as `thumb_url`, `d`.image_url as `image_url`, `d`.phone as `phone`, '
        . "`e`.`id` as `event_id`, UNIX_TIMESTAMP(`e`.start_time) as `start_time`, UNIX_TIMESTAMP(`e`.end_time) as `end_time`, "
        . "UNIX_TIMESTAMP(`e`.date_added) as `date_added`, `e`.priority as `priority`"
        . "FROM event as `e` LEFT JOIN detail as `d` ON `e`.detail_id=`d`.`id` ";

    $stmt = $db->prepare($query);

    $stmt->execute();

    $data = array();
    while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
        // Build the detail instance
        $detail = new \Evn\model\Detail($row);
        $detail->activities = \Evn\util\ActivityMapUtil::mapToDetail($db, $detail->id, [], []);

        // Build the event instance
        $event = new \Evn\model\Event($row, $detail, $db);

        $data[] = $event;
    }

    return $response->withJson(
        array(
            'data' => $data,
        )
    );
});

/**
 * Route to return the list of locations within a defined space
 */
$app->get('/adminApi/getDestinations', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $params = $request->getQueryParams();
    $categories = $request->getQueryParam('category');
    $activities = $request->getQueryParam('activity');


    $query = "SELECT "
        . '`d`.id, `d`.name, `d`.short_desc, `d`.long_desc, `d`.thumb_url, `d`.image_url, `d`.phone, '
        . '`dest`.`latitude`, `dest`.`longitude`, '
        . '`a`.`address_line_one`, `a`.`address_line_two`, `a`.`postal_code`,`a`.`city` '
        . 'FROM destination as `dest` '
        . 'LEFT JOIN `detail` as `d` ON `dest`.`detail_id`=`d`.`id` '
        . 'LEFT JOIN `address` as `a` ON `a`.`id`=`dest`.`address_id` ';

    $stmt = $db->prepare($query);
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
});

/**
 * Route to return the list of locations within a defined space
 */
$app->get('/adminApi/getCategoryData', function ($request, $response, $args) {
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

/**
 * Event API Endpoints
 */
$app->post('/adminApi/updateEvent', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;

    /**
     * Update the Event
     */
    $event = $request->getParsedBody()['event'];

    $query = 'UPDATE `event` as `ev` '
        . 'SET `ev`.`priority`=:priority, `ev`.`start_time`=FROM_UNIXTIME(:starttime), `ev`.`end_time`=FROM_UNIXTIME(:endtime) '
        . 'WHERE `ev`.`id`=:eventId';
    $stmt = $db->prepare($query);

    // Bind the Parameters
    $stmt->bindParam(':priority', $event['priority'], \PDO::PARAM_INT);
    $stmt->bindParam(':starttime', $event['unixStartTime'], \PDO::PARAM_INT);
    $stmt->bindParam(':endtime', $event['unixEndTime'], \PDO::PARAM_INT);
    $stmt->bindParam(':eventId', $event['id'], \PDO::PARAM_INT);
    $stmt->execute();

    return $response->getBody()->write("$query::".$event['priority']."::".$event['unixStartTime']."::".$event['unixEndTime']);
});