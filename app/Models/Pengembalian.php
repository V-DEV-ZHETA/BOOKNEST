<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    protected $fillable = ['peminjaman_id', 'return_date', 'condition', 'notes', 'fine_amount', 'received_by', 'checked_by', 'late_days'];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }
}
