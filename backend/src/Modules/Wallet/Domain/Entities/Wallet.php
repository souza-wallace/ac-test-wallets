<?php

namespace Modules\Wallet\Domain\Entities;

use Modules\Shared\Exceptions\InsufficientFundsException;

class Wallet
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private float $balance = 0.0,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTime();
        $this->updatedAt = $this->updatedAt ?? new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
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

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        $this->balance += $amount;
        $this->updatedAt = new \DateTime();
    }

    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        if ($this->balance < $amount) {
            throw new InsufficientFundsException();
        }

        $this->balance -= $amount;
        $this->updatedAt = new \DateTime();
    }

    public function canWithdraw(float $amount): bool
    {
        return $this->balance >= $amount && $amount > 0;
    }
}