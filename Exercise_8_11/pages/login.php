<?php

/**
 * @var AuthService $authService
 * @var SessionService $sessionService
 */

use Service\AuthService;
use Service\SessionService;

if (isset($_POST['login'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	$hasError = false;

	if (empty($username)) {
		echo '<p> Username cannot be blank.</p >';
		$hasError = true;
	}

	if (empty($password)) {
		echo '<p>Password cannot be blank.</p>';
		$hasError = true;
	}

	if (!$hasError && $authService->login($username, $password)) {
		header('Location: /');
        exit;
	}

    echo '<p>Invalid credentials.</p>';
	http_response_code(400);
}

?>

<form action="/login" method="post">
	<div>
		<label for="username">Username: </label>
		<input id="username" type="text" name="username" />
	</div>
	<div>
		<label for="password">Password: </label>
		<input id="password" type="password" required name="password" />
	</div>
	<div>
		<input type="submit" name="login" value="Login">
	</div>
</form>
