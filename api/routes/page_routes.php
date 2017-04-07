<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 2017-03-15
 * Time: 8:07 PM
 */
/**
 * CSS and Javascript routes
 */
$app->get('/css/{file}', function ($request, $response, $args) {
    return $this->view->render($response, 'css/' .  $args['file'], array());
});

$app->get('/javascript/{file}', function ($request, $response, $args) {
    return $this->view->render($response, 'javascript/' . $args['file'], array());
});

/**
 * Route to the login page
 */
$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.php', array());
})->setName('login');

/**
 * Route to validate the login parameters
 */
$app->post('/login', function ($request, $response, $args) {
    $db = new \Evn\classes\Database;
    $email = $request->getParsedBody()['login_email'];
    $password = sha1($request->getParsedBody()['login_password']);
    $query = 'SELECT COUNT(*) as count FROM `user` WHERE `email`=:email AND `password`=:password';
    $stmt = $db->prepare($query);

    // Attach the parameters
    $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, \PDO::PARAM_STR);

    $stmt->execute();
    if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false && $row['count'] == 1) {

        // Set the token
        $token = randomString(32);
        $query = 'UPDATE `user` SET last_seen=CURRENT_TIMESTAMP, token=:token WHERE `email`=:email';
        $stmt = $db->prepare($query);

        // Attach the parameters
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);

        $stmt->execute();
        $_SESSION['email'] = $email;
        $_SESSION['token'] = $token;

        // And begin!
        return $response->withStatus(303)->withHeader('Location', 'pages/main');
    } else {
        // Otherwise reload the login page with an invalid flag
        return $response->withStatus(303)->withHeader('Location', 'login');
    }
})->setName('login_validate');

/**
 * Route to the main page
 */
$app->post('/pages/main', function ($request, $response, $args) {
    return $this->view->render($response, 'main.php', array());
});
$app->get('/pages/main', function ($request, $response, $args) {
    return $this->view->render($response, 'main.php', array());
});
?>