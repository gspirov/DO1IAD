<?php

/**
 * @var AuthService $authService
 * @var SessionService $sessionService
 */

use PDO\Connection;
use Service\AuthService;
use Service\SessionService;

if (isset($_POST['register'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$name = trim($_POST['name']);

    $hasError = false;

	if (empty($username)) {
        echo '<p> Username cannot be blank.</p >';
        $hasError = true;
	}

	if (empty($password)) {
		echo '<p>Password cannot be blank.</p>';
        $hasError = true;
	}

    if (empty($name)) {
		echo '<p>Name cannot be blank.</p>';
        $hasError = true;
	}

    $existsStmt = Connection::getPdo()->prepare('SELECT EXISTS(SELECT 1 FROM `user` WHERE username = :username);');
    $existsStmt->bindParam(':username', $username);
    $existsStmt->execute();

    if ($existsStmt->fetchColumn() === 1) {
        $hasError = true;
        echo '<p>Username already exists.</p>';
        http_response_code(400);
    }

    if (!$hasError) {
        if ($authService->register($username, $password, $name)) {
            $sessionService->set('successfullyRegistered', true);
            header('Location: /login');
        } else {
            echo '<p>Failed creating user.</p>';
            http_response_code(400);
            header('Location: /register');
        }
    }
}

?>

<form action="/register" method="post">
	<div>
		<label for="username">Username: </label>
		<input id="username" type="text" name="username" />
	</div>
	<div>
		<label for="password">Password: </label>
		<input id="password" type="password" required name="password" />
	</div>
    <div>
        <label for="name">Name: </label>
        <input id="name" type="text" required name="name" />
    </div>
	<div>
		<input type="submit" name="register" value="Register">
	</div>
</form>