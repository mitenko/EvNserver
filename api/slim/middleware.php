<?php
// Application middleware

// TODO: check that the route is https

/**
 * API Authentication middleware closure
 *
 * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
 * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
 * @param  callable                                 $next     Next middleware
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
$apiAuthenticate = function ($request, $response, $next) {
    $path = $request->getUri()->getPath();
    if (strpos($path, 'api')) {
        $APIAuth = new \Evn\classes\APIAuthenticator;
        $user = $APIAuth->authenticate($request);
        $request->withAttribute('user', $user);
    }
	return $next($request, $response);
};
$app->add($apiAuthenticate);

/**
 * Pages / login middleware closure
 *
 * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
 * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
 * @param  callable                                 $next     Next middleware
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
$pageAuthenticate = function ($request, $response, $next) {
    $path = $request->getUri()->getPath();
    if (strpos($path, 'pages')) {
        $db = new \Evn\classes\Database;
        $email = $_SESSION['email'];
        $token = $_SESSION['token'];

        $query = 'SELECT COUNT(*) as count FROM `user` WHERE `email`=:email AND `token`=:token';
        $stmt = $db->prepare($query);

        // Attach the parameters
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);

        $stmt->execute();
        // User and token check out
        if (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false && $row['count'] == 1) {
            return $next($request, $response);
        } else {
            return $response->withStatus(303)->withHeader('Location', 'login');
        }
    }
    return $next($request, $response);
};
$app->add($pageAuthenticate);
