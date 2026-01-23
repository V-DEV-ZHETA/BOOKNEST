<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $fillable = ['user_id', 'inventori_buku_id', 'borrow_date', 'due_date', 'return_date', 'status', 'notes', 'borrower_name', 'borrower_phone'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inventoriBuku(): BelongsTo
    {
        return $this->belongsTo(InventoriBuku::class, 'inventori_buku_id');
    }
}
