<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    protected $fillable = [
        'peminjaman_id',
        'user_id',
        'inventori_buku_id',
        'return_date',
        'condition',
        'notes',
        'fine_amount',
        'late_days',
        'confirmation_status',
        'confirmed_by',
        'confirmed_at',
        'confirmed_notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'confirmed_at' => 'datetime',
        'fine_amount' => 'decimal:2',
        'late_days' => 'integer',
    ];

    /**
     * Get the related peminjaman
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /**
     * Get the user who returned the book
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that was returned
     */
    public function inventoriBuku(): BelongsTo
    {
        return $this->belongsTo(InventoriBuku::class, 'inventori_buku_id');
    }

    /**
     * Get the staff who confirmed this return
     */
    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Scope for pending confirmations
     */
    public function scopePending($query)
    {
        return $query->where('confirmation_status', 'pending');
    }

    /**
     * Scope for approved returns
     */
    public function scopeApproved($query)
    {
        return $query->where('confirmation_status', 'approved');
    }

    /**
     * Calculate late days based on due date
     */
    public function calculateLateDays(): int
    {
        if (!$this->peminjaman) {
            return 0;
        }
        
        return $this->return_date->diffInDays($this->peminjaman->due_date, false);
    }

    /**
     * Calculate fine based on late days
     */
    public function calculateFine(int $dailyFine = 5000): float
    {
        $lateDays = $this->late_days ?? $this->calculateLateDays();
        return $lateDays > 0 ? $lateDays * $dailyFine : 0;
    }

    /**
     * Get human readable condition
     */
    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'good' => 'Baik',
            'damaged' => 'Rusak Ringan',
            'damaged_heavy' => 'Rusak Berat',
            'lost' => 'Hilang',
            default => $this->condition,
        };
    }

    /**
     * Get human readable confirmation status
     */
    public function getConfirmationLabelAttribute(): string
    {
        return match($this->confirmation_status) {
            'pending' => 'Menunggu Konfirmasi',
            'approved' => 'Dikonfirmasi',
            'rejected' => 'Ditolak',
            default => $this->confirmation_status,
        };
    }
}

