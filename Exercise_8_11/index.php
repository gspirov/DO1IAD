<?php

use Service\SessionService;
use Service\AuthService;

require_once 'Service/AuthService.php';
require_once 'Service/SessionService.php';
require_once 'PDO/Connection.php';
require_once 'Entity/Student.php';
require_once 'Entity/Course.php';

$sessionService = new SessionService;
$sessionService->start();

$authService = new AuthService($sessionService);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
	'/' => function () use ($authService) {
		$authService->redirectUnauthenticated();
		return 'pages/home.php';
	},
	'/login' => function () use ($authService, $sessionService) {
		$authService->redirectAuthenticated();

		extract([
			'authService' => $authService
		]);

		if (!empty($sessionService->get('successfullyRegistered'))) {
			echo '<p>You have successfully registered, please login!</p>';
			$sessionService->unset('successfullyRegistered');
		}

		return 'pages/login.php';
	},
	'/register' => function () use ($authService, $sessionService) {
		$authService->redirectAuthenticated();

		extract([
			'authService' => $authService,
			'sessionService' => $sessionService
		]);
		return 'pages/register.php';
	},
	'/logout' => function () use ($authService) {
		$authService->redirectUnauthenticated();
		$authService->logout();
		header('Location: /login');
	},
	'/courses' => function () use ($authService) {
		$authService->redirectUnauthenticated();
		return 'pages/courses.php';
	}
];

include_once 'pages/partial/nav.php';

if (preg_match('#^/course/(\d+)$#', $uri, $matches)) {
	$authService->redirectUnauthenticated();
	$courseId = $matches[1];
	extract([
		'courseId' => $courseId
	]);
	require 'pages/course.php';
	exit;
}

if (isset($routes[$uri])) {
	require $routes[$uri]();
} else {
	http_response_code(404);
	echo 'Oops! Page not found.';
}

exit;