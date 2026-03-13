<?php

namespace Service;

class SessionService
{
	public function isStarted(): bool
	{
		return session_status() === PHP_SESSION_ACTIVE;
	}

	public function start(): void
	{
		if (!$this->isStarted()) {
			session_start();
		}
	}

	public function destroy(): void
	{
		if ($this->isStarted()) {
			session_destroy();
		}
	}

	public function get(string $key): mixed
	{
		$this->start();
		return $_SESSION[$key] ?? null;
	}

	public function set(string $key, mixed $value): void
	{
		$this->start();
		$_SESSION[$key] = $value;
	}

	public function unset(string $key): void
	{
		$this->start();

		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}
}