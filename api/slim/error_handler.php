<?php 

// get the app's di-container
$container = $app->getContainer();

$container['errorHandler'] = function ($container) {
	return function ($request, $response, $exception) use ($container) {
		$response = $container['response'];
		$response->withHeader('Content-Type', 'text/html');
		
		$message = $exception->getMessage();
		if ($message == \Evn\classes\Authenticator::UNAUTHORIZED) {
			$response->withStatus(401)
                     ->write($message);
		} else {
			$response->withStatus(500)
                     ->write('Something went wrong!' . $message);
		}
		return $response;
	};
};
?>