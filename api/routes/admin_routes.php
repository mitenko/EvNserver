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
        $detail->activities = \Evn\util\ActivityMapUtil::mapToDetail($db, $detail->id, $categories, $activities);

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
    $response->withJson(array('this' => 'is', 'your' => 'response!'));
});