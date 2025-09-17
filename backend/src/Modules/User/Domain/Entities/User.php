<?php

namespace Modules\User\Domain\Entities;

class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $password,
        private float $balance = 0.0,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null
    ) {
        $this->validateEmail($email);
        $this->validateName($name);
        $this->createdAt = $this->createdAt ?? new \DateTime();
        $this->updatedAt = $this->updatedAt ?? new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function updateBalance(float $amount): void
    {
        $this->balance = $amount;
        $this->updatedAt = new \DateTime();
    }

    public function changeName(string $name): void
    {
        $this->validateName($name);
        $this->name = $name;
        $this->updatedAt = new \DateTime();
    }

    public function changePassword(string $password): void
    {
        $this->password = $password;
        $this->updatedAt = new \DateTime();
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
    }
}