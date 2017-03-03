<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	require '../vendor/autoload.php';
	require '../src/config/db.php';

	// Customer Routes
	require '../src/routes/task.php';

	$app->run();
?>