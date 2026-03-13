<?php

namespace Service;

use Entity\Student;
use PDO;
use PDO\Connection;
use Throwable;

class AuthService
{
	private ?Student $user = null;

	public function __construct(
		private readonly SessionService $sessionService
	) {}

	public function getUser(): ?Student
	{
		if ($this->user === null && null !== $userId = $this->sessionService->get('user')) {
			$this->user = $this->findUserById($userId);
		}

		return $this->user;
	}

	public function register(string $username, string $password, string $name): bool
	{
		try {
			$connection = Connection::getPdo();

			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

			$stmt = $connection->prepare('INSERT INTO `user` (username, `password`, `name`)  VALUES (:username, :password, :name)');
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':password', $hashedPassword);
			$stmt->bindParam(':name', $name);
			$stmt->execute();

			return true;
		} catch (Throwable) {
			return false;
		}
	}

	public function login(string $username, string $password): bool
	{
		if (
			null !== ($user = $this->findByUsername($username)) &&
			password_verify($password, $user->password)
		) {
			session_regenerate_id(true);
			$this->sessionService->set('user', $user->id);
			return true;
		}

		return false;
	}

	public function findUserById(int $id): ?Student
	{
		$userStmt = Connection::getPdo()->prepare('SELECT * FROM `user` WHERE id = :id;');
		$userStmt->bindParam(':id', $id);
		$userStmt->execute();

		$userAssoc = $userStmt->fetch(PDO::FETCH_ASSOC);

		if (empty($userAssoc)) {
			return null;
		}

		return new Student(
			$userAssoc['id'],
			$userAssoc['username'],
			$userAssoc['name'],
			$userAssoc['password'],
			$userAssoc['grade']
		);
	}

	public function findByUsername(string $username): ?Student
	{
		$userStmt = Connection::getPdo()->prepare('SELECT * FROM `user` WHERE username = :username;');
		$userStmt->bindParam(':username', $username);
		$userStmt->execute();

		$userAssoc = $userStmt->fetch(PDO::FETCH_ASSOC);

		if (empty($userAssoc)) {
			return null;
		}

		return new Student(
			$userAssoc['id'],
			$userAssoc['username'],
			$userAssoc['name'],
			$userAssoc['password'],
			$userAssoc['grade']
		);
	}

	public function logout(): void
	{
		$this->sessionService->destroy();
	}

	public function redirectAuthenticated(): void
	{
		if ($this->getUser()) {
			header('Location: /');
			exit;
		}
	}

	public function redirectUnauthenticated(): void
	{
		if (!$this->getUser()) {
			header('Location: /login');
			exit;
		}
	}
}