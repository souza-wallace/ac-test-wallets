<?php

namespace Modules\Wallet\Infra\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Wallet\Infra\Persistence\Models\Wallet as WalletModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Infra\Persistence\Models\User as UserModel;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'wallet_id',
        'related_wallet',
        'user_id',
        'type',
        'status',
        'amount',
        'description',
        'reference_id',
        'can_reverse'
    ];

    protected $casts = [
        'amount' => 'float',
        'wallet_id' => 'integer',
        'related_wallet' => 'integer',
        'user_id' => 'integer',
        'reference_id' => 'integer',
        'can_reverse' => 'boolean',
    ];

    /**
     * Carteira de origem
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(WalletModel::class, 'wallet_id');
    }

    /**
     * Carteira destinatária (nullable)
     */
    public function recipientWallet(): BelongsTo
    {
        return $this->belongsTo(WalletModel::class, 'related_wallet');
    }

    /**
     * Usuário que fez a transação
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /**
     * Transação de referência (reversão ou link)
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'reference_id');
    }
}