<?php

namespace Modules\Wallet\Infra\Persistence\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'related_wallet',
        'type',
        'amount',
        'status',
        'reference_id',
        'description',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function relatedWallet()
    {
        return $this->belongsTo(Wallet::class, 'related_wallet');
    }

    public function reference()
    {
        return $this->belongsTo(Transaction::class, 'reference_id');
    }

    public function reversals()
    {
        return $this->hasMany(Transaction::class, 'reference_id');
    }
}
