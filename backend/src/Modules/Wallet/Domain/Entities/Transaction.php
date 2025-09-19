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
        private ?int $recipientWalletId,
        private ?string $description,
        private ?int $referenceId,
        private TransactionStatus $status,
        private \DateTime $createdAt,
        private bool $canReverse
    ) {}

    // ========================
    // FACTORY METHODS
    // ========================
       /**
     * Cria uma nova transação de carteira.
     *
     * @param int              $walletId          ID da carteira de origem da transação.
     * @param int              $userId            ID do usuário responsável pela transação.
     * @param TransactionType  $type              Tipo da transação (ex.: DEPOSIT, TRANSFER, REVERSAL).
     * @param float            $amount            Valor da transação
     * @param int|null         $recipientWalletId ID da carteira de destino (usado em transferências).
     * @param string|null      $description       Descrição ou observação da transação.
     * @param int|null         $referenceId       ID de referência para relacionar com outra transação (ex.: reversão).
     * @param bool             $canReverse        Indica se a transação pode ser revertida (padrão: true).
     *
     * @return self
     */
    public static function create(
        int $walletId,
        int $userId,
        TransactionType $type,
        float $amount,
        ?int $recipientWalletId = null,
        ?string $description = null,
        ?int $referenceId = null,
        bool $canReverse = true
    ): self {
        $transaction = new self(
            null,
            $walletId,
            $userId,
            $type,
            $amount,
            $recipientWalletId,
            $description,
            $referenceId,
            TransactionStatus::COMPLETED,
            new \DateTime(),
            $canReverse
        );

        $transaction->validateAmount($amount);
        $transaction->validateTransferRecipient($type, $recipientWalletId);

        return $transaction;
    }


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
        \DateTime $createdAt,
        bool $canReverse = true
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
            $createdAt,
            $canReverse
        );
    }

    // ========================
    // BEHAVIORS
    // ========================
    public function adjustAmount(float $amount): void
    {
        $this->validateAmount($amount);
        $this->amount = $amount;
    }

    public function markAsIrreversible(): void
    {
        $this->canReverse = false;
    }

    public function complete(): void
    {
        $this->status = TransactionStatus::COMPLETED;
    }

    public function fail(): void
    {
        $this->status = TransactionStatus::FAILED;
    }

    // ========================
    // GETTERS
    // ========================
    public function getId(): ?int { return $this->id; }
    public function getWalletId(): int { return $this->walletId; }
    public function getUserId(): int { return $this->userId; }
    public function getType(): TransactionType { return $this->type; }
    public function getAmount(): float { return $this->amount; }
    public function getRecipientWalletId(): ?int { return $this->recipientWalletId; }
    public function getDescription(): ?string { return $this->description; }
    public function getReferenceId(): ?int { return $this->referenceId; }
    public function getStatus(): TransactionStatus { return $this->status; }
    public function getCreatedAt(): \DateTime { return $this->createdAt; }
    public function getCanReverse(): bool { return $this->canReverse; }

    // ========================
    // VALIDATIONS
    // ========================
    private function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
    }

    private function validateTransferRecipient(TransactionType $type, ?int $recipientWalletId): void
    {
        if ($type === TransactionType::TRANSFER && !$recipientWalletId) {
            throw new \InvalidArgumentException('Transfer transactions must have a recipient');
        }
    }
}