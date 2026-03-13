<?php

namespace Entity;

use InvalidArgumentException;

class Student
{
	public function __construct(
		public int $id,
		public string $username,
		private string $name,
		public string $password,
		private int $grade = 0
	) {
		$this->setName($name);
		$this->setGrade($grade);
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		if (trim($name) === '') {
			throw new InvalidArgumentException(' Could not create the new object!');
		}

		$this->name = $name;

		return $this;
	}

	public function getGrade(): int
	{
		return $this->grade;
	}

	public function setGrade(int $grade): static
	{
		if ($grade < 0 || $grade > 100) {
			throw new InvalidArgumentException(' Could not create the new object!');
		}

		$this->grade = $grade;

		return $this;
	}

	public function pass(): bool
	{
		return $this->grade >= 40;
	}
}