<?php

/**
 * @var AuthService $authService
 */

use Service\AuthService;

$user = $authService->getUser();

?>

<nav>
	<ul>
		<?php if ($user) : ?>
			<li>
				<a href="/courses">Courses</a>
			</li>
			<li>
				<a href="/logout">Logout</a>
			</li>
		<?php else : ?>
			<li>
				<a href="/login">Login</a>
			</li>
			<li>
				<a href="/register">Register</a>
			</li>
		<?php endif; ?>
	</ul>
</nav>
