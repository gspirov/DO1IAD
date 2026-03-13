<?php

namespace PDO;

use PDO;

class Connection
{
	private static ?PDO $pdo = null;

	public static function getPdo(): ?PDO
	{
		if (self::$pdo === null) {
			self::$pdo = new PDO('mysql:host=localhost;dbname=course', 'root', 'root');
		}

		return self::$pdo;
	}
}