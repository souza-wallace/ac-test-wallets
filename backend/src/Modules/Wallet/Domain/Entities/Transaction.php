<?php

namespace Modules\Wallet\Domain\Entities;

use Modules\Wallet\Domain\Enums\TransactionType;

class Transaction
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private TransactionType $type,
        private float $amount,
        private ?int $recipientId = null,
        private ?string $description = null,
        private ?int $referenceId = null,
        private ?\DateTime $createdAt = null
    ) {
        $this->validateAmount($amount);
        $this->validateTransferRecipient($type, $recipientId);
        $this->createdAt = $this->createdAt ?? new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getRecipientId(): ?int
    {
        return $this->recipientId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getReferenceId(): ?int
    {
        return $this->referenceId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function isTransfer(): bool
    {
        return $this->type === TransactionType::TRANSFER;
    }

    public function isDeposit(): bool
    {
        return $this->type === TransactionType::DEPOSIT;
    }

    public function isReversal(): bool
    {
        return $this->type === TransactionType::REVERSAL;
    }

    private function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
    }

    private function validateTransferRecipient(TransactionType $type, ?int $recipientId): void
    {
        if ($type === TransactionType::TRANSFER && $recipientId === null) {
            throw new \InvalidArgumentException('Transfer transactions must have a recipient');
        }
    }
}