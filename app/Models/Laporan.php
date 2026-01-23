<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    protected $fillable = ['title', 'type', 'content', 'generated_at', 'user_id', 'start_date', 'end_date', 'period_type', 'summary', 'total_records', 'page_count', 'file_path'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
