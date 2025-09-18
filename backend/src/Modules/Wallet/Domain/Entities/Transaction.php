<?php

namespace Modules\Wallet\Domain\Entities;

use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Domain\Enums\TransactionStatus;

class Transaction
{
    private function __construct(
        private ?int $id,
        private int $walletId,
        private int $userId,
        private TransactionType $type,
        private float $amount,
        private ?int $recipientWalletId = null,
        private ?string $description = null,
        private ?int $referenceId = null,
        private TransactionStatus $status = TransactionStatus::PENDING,
        private ?\DateTime $createdAt = null
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getWalletId(): int { return $this->walletId; }
    public function getUserId(): int { return $this->userId; }
    public function getType(): TransactionType { return $this->type; }
    public function getAmount(): float { return $this->amount; }
    public function getRecipientWalletId(): ?int { return $this->recipientWalletId; }
    public function getDescription(): ?string { return $this->description; }
    public function getReferenceId(): ?int { return $this->referenceId; }
    public function getStatus(): TransactionStatus { return $this->status; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }


    public static function createNew(
        int $walletId,
        int $userId,
        TransactionType $type,
        float $amount,
        ?int $recipientWalletId = null,
        ?string $description = null
    ): self {
        $transaction = new self(null, $walletId, $userId, $type, $amount, $recipientWalletId, $description);
        $transaction->validateAmount($amount);
        // $transaction->validateTransferRecipient($type, $recipientWalletId);

        return $transaction;
    }

    // 👉 usado no repositório (sem validar regras)
    public static function reconstitute(
        ?int $id,
        int $walletId,
        int $userId,
        TransactionType $type,
        float $amount,
        ?int $recipientWalletId,
        ?string $description,
        ?int $referenceId,
        TransactionStatus $status,
        ?\DateTime $createdAt
    ): self {
        return new self(
            $id,
            $walletId,
            $userId,
            $type,
            $amount,
            $recipientWalletId,
            $description,
            $referenceId,
            $status,
            $createdAt
        );
    }

    private function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
    }

    private function validateTransferRecipient(TransactionType $type, ?int $recipientWalletId): void
    {
        if ($type === TransactionType::TRANSFER && $recipientWalletId) {
            throw new \InvalidArgumentException('Transfer transactions must have a recipient');
        }
    }
}