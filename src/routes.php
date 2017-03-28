<?php
// Routes

$app->get('/index.php', function ($request, $response, $args) {	
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/hello/{name}', function ($request, $response, $args) {
	$name = $request->getAttribute('name');
	$response->getBody()->write("Hello, $name");

	return $response;
});

$app->get('/api/db', function ($request, $response, $args) {
	$db = new \Evn\classes\Database();

	return $response;
});