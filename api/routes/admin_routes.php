<?php
// Routes

/**
 * Route to return the list of ongoing events
 */
$app->get('/adminApi/getEvents', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $sortOn = $request->getQueryParam('sorton');
    $sortDir = $request->getQueryParam('sortdir');

    $query = "SELECT "
        . '`d`.id as `detailId`, `d`.name as `name`, `d`.short_desc as `short_desc`, `d`.long_desc as `long_desc`, '
        . '`d`.thumb_url as `thumb_url`, `d`.image_url as `image_url`, `d`.phone as `phone`, '
        . "`e`.`id` as `event_id`, `e`.start_time as `start_time`, `e`.end_time as `end_time`, "
        . "UNIX_TIMESTAMP(`e`.date_added) as `date_added`, `e`.priority as `priority`, "
        . "`d`.`phone` as `phone`, `d`.website as `website`, `d`.cost as `cost` "
        . "FROM event as `e` LEFT JOIN detail as `d` ON `e`.detail_id=`d`.`id` ";

    if ($sortOn && $sortDir) {
        // Validate the unbindable values
        if (!preg_match('/(ASC)|(DESC)/', $sortDir)
            || !preg_match('/(priority)|(name)|(start_time)|(short_desc)|(cost)/', $sortOn)) {
            throw new Exception('Invalid Parameters');
        }
        $query .= "ORDER BY `$sortOn` $sortDir";
    }

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
            'query' => $query,
            'data' => $data,
        )
    );
});

/**
 * Route to return the list of locations within a defined space
 */
$app->get('/adminApi/getDestinations', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $sortOn = $request->getQueryParam('sorton');
    $sortDir = $request->getQueryParam('sortdir');
    $categories = $request->getQueryParam('category');
    $activities = $request->getQueryParam('activity');

    $query = "SELECT "
        . '`d`.id as `detailId`, `d`.name, `d`.short_desc, `d`.long_desc, `d`.thumb_url, `d`.image_url, `d`.phone, '
        . '`dest`.id as `destId`, `dest`.`latitude`, `dest`.`longitude`, '
        . '`a`.`id` as `address_id`, `a`.`address_line_one`, `a`.`address_line_two`, `a`.`postal_code`,`a`.`city` '
        . 'FROM destination as `dest` '
        . 'LEFT JOIN `detail` as `d` ON `dest`.`detail_id`=`d`.`id` '
        . 'LEFT JOIN `address` as `a` ON `a`.`id`=`dest`.`address_id` ';

    if ($sortOn && $sortDir) {
        // Validate the unbindable values
        if (!preg_match('/(ASC)|(DESC)/', $sortDir)
            || !preg_match('/(priority)|(name)|(start_time)|(short_desc)|(cost)/', $sortOn)) {
            throw new Exception('Invalid Parameters');
        }
        $query .= "ORDER BY `$sortOn` $sortDir";
    }

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
        . 'SET `ev`.`priority`=:priority, `ev`.`start_time`=:starttime, `ev`.`end_time`=:endtime '
        . 'WHERE `ev`.`id`=:eventId';
    $stmt = $db->prepare($query);

    // Bind the Parameters
    $stmt->bindParam(':priority', $event['priority'], \PDO::PARAM_INT);
    $stmt->bindParam(':starttime', $event['unixStartTime'], \PDO::PARAM_INT);
    $stmt->bindParam(':endtime', $event['unixEndTime'], \PDO::PARAM_INT);
    $stmt->bindParam(':eventId', $event['id'], \PDO::PARAM_INT);
    $stmt->execute();

    // Update the event destination map
    \Evn\util\DBUtil::updateEventDestinationMap($db, $event);

    // Update the detail
    \Evn\util\DBUtil::updateDetail($db, $event['detail']);

    return $response;
});

/**
 * Deletes an event and all associated data from the database
 */
$app->post('/adminApi/deleteEvent', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $eventId = $request->getParsedBody()['eventId'];

    if (!$eventId) {
        throw new Exception('Expected event data ' + print_r($request->getQueryParams(), true));
    }

    // Grab the detailId
    $query = 'SELECT `detail_id` FROM `event` WHERE `id`=:eventId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':eventId', $eventId, \PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$row) {
        throw new Exception('Invalid Detail Id');
    }
    $detailId = $row['detail_id'];

    // Delete from event
    $query = 'DELETE FROM `event` WHERE `id`=:eventId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':eventId', $eventId, \PDO::PARAM_INT);
    $stmt->execute();

    // Delete the image
    $query = 'SELECT `image_url` FROM `detail` WHERE `id`=:detailId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['image_url']) {
        $delFileParts = pathinfo($row['image_url']);
        $delFile = __IMGDIR__ . $delFileParts['basename'];
        unlink($delFile);
    }

    // Delete from detail
    $query = 'DELETE FROM `detail` WHERE `id`=:detailId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);
    $stmt->execute();

    // Delete from the event_destination_map
    $query = 'DELETE FROM `event_destination_map` WHERE `event_id`=:eventId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':eventId', $eventId, \PDO::PARAM_INT);
    $stmt->execute();

    // Delete from the detail_activity_map
    $query = 'DELETE FROM `detail_activity_map` WHERE `detail_id`=:detailId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);
    $stmt->execute();

    return $response;
});

/**
 * Adds a new event into the database
 */
$app->post('/adminApi/addEvent', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $event = $request->getParsedBody()['event'];

    /**
     * First insert the detail to get the detailId
     */
    $detailId = \Evn\util\DBUtil::addDetail($db, $event['detail']);

    // Update the event destination map
    \Evn\util\DBUtil::updateEventDestinationMap($db, $event);

    /**
     * Now insert the Event
     */
    $query = 'INSERT INTO `event` '
        . '(`detail_id`,`priority`,`start_time`,`end_time`) '
        . ' VALUES (:detailId, :priority, :starttime, :endtime)';
    $stmt = $db->prepare($query);

    // Bind the Parameters
    $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);
    $stmt->bindParam(':priority', $event['priority'], \PDO::PARAM_INT);
    $stmt->bindParam(':starttime', $event['unixStartTime'], \PDO::PARAM_INT);
    $stmt->bindParam(':endtime', $event['unixEndTime'], \PDO::PARAM_INT);
    $stmt->execute();

    $eventId = $db->getLastInsertId();


    return $response->withJson(
        array(
            'detailId' => $detailId,
            'eventId' => $eventId,
        ));
});

/**
 * Upload an Image
 */
$app->post('/adminApi/updateImage', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;

    $files = $request->getUploadedFiles();
    $detailId = $request->getParsedBody()['detailId'];

    if (empty($files['uploadImage']) || !$detailId) {
        throw new Exception('Expected an uploaded image');
    }

    // Validate the detailId
    $query = 'Select * from `detail` WHERE id=:detailId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        throw new Exception('Invalid Detail Id');
    }

    // Move the new image
    $newfile = $files['uploadImage'];
    $pathParts = pathinfo($newfile->getClientFilename());
    $newfileName = randomString(32) . "." . $pathParts['extension'];
    $targetPath = __IMGDIR__  . $newfileName;
    $newfile->moveTo($targetPath);

    // Delete the old image
    $query = 'SELECT `image_url` FROM `detail` WHERE `id`=:detailId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['image_url']) {
        $delFileParts = pathinfo($row['image_url']);
        $delFile = __IMGDIR__ . $delFileParts['basename'];
        unlink($delFile);
    }

    // Update detail row with the new detail image
    $newURL =  'https://' . $_SERVER['SERVER_NAME'] . '/img/' . $newfileName;
    $query = 'UPDATE `detail` SET `image_url`=:newURL WHERE `id`=:detailId';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);
    $stmt->bindParam(':newURL', $newURL, \PDO::PARAM_STR);
    $stmt->execute();

    return $response->withJson(
        array(
            'fileparts' => $delFileParts,
            'delfile' => $delFile,
        ));
});

/**
 * Updates a Destination
 */
$app->post('/adminApi/updateDest', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    /**
     * Update the Event
     */
    $dest = $request->getParsedBody()['dest'];

    $query = 'UPDATE `destination` as `d` '
        . 'SET `d`.`latitude`=:latitude, `d`.`longitude`=:longitude '
        . 'WHERE `d`.`id`=:destId';
    $stmt = $db->prepare($query);

    // Bind the Parameters
    $stmt->bindParam(':latitude', $dest['latitude']);
    $stmt->bindParam(':longitude', $dest['longitude']);
    $stmt->bindParam(':destId', $dest['id'], \PDO::PARAM_INT);
    $stmt->execute();
    $returnQ = $query;

    // Update the address
    $address = $dest['address'];
    $query = 'UPDATE `address` as `a` '
        . 'SET `a`.`address_line_one`=:lineOne, `a`.`address_line_two`=:lineTwo, '
        . '`a`.`postal_code`=:postalCode, `a`.`city`=:city '
        . 'WHERE `a`.`id`=:addressId';
    $stmt = $db->prepare($query);

    // Bind the Parameters
    $stmt->bindParam(':lineOne', $address['lineOne'], \PDO::PARAM_STR);
    $stmt->bindParam(':lineTwo', $address['lineTwo'], \PDO::PARAM_STR);
    $stmt->bindParam(':postalCode', $address['postalCode'], \PDO::PARAM_STR);
    $stmt->bindParam(':city', $address['city'], \PDO::PARAM_STR);
    $stmt->bindParam(':addressId', $address['id'], \PDO::PARAM_INT);
    $stmt->execute();


    // Update the detail
    \Evn\util\DBUtil::updateDetail($db, $dest['detail']);

    return $response->getBody()->write($returnQ);
});