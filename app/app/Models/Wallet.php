<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['balance' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    public function credit(float $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return $this->applyTransaction('credit', $amount, $description, $reference);
    }

    public function debit(float $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return $this->applyTransaction('debit', $amount, $description, $reference);
    }

    protected function applyTransaction(string $type, float $amount, string $description, ?Model $reference): WalletTransaction
    {
        return DB::transaction(function () use ($type, $amount, $description, $reference) {
            $wallet = self::lockForUpdate()->find($this->id);

            $newBalance = $type === 'credit'
                ? $wallet->balance + $amount
                : $wallet->balance - $amount;

            if ($newBalance < 0) {
                throw new \RuntimeException('Insufficient wallet balance.');
            }

            $wallet->update(['balance' => $newBalance]);
            $this->balance = $newBalance;

            return $wallet->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
            ]);
        });
    }
}
