<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $fillable = [
        'user_id',
        'inventori_buku_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'confirmation_status',
        'confirmed_by',
        'confirmed_at',
        'confirmed_notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the Peminjaman
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that is borrowed
     */
    public function inventoriBuku(): BelongsTo
    {
        return $this->belongsTo(InventoriBuku::class, 'inventori_buku_id');
    }

    /**
     * Get the staff who confirmed this transaction
     */
    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Get related pengembalian record
     */
    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }

    /**
     * Scope for pending confirmations
     */
    public function scopePending($query)
    {
        return $query->where('confirmation_status', 'pending');
    }

    /**
     * Scope for approved transactions
     */
    public function scopeApproved($query)
    {
        return $query->where('confirmation_status', 'approved');
    }

    /**
     * Scope for borrowed status (active loans)
     */
    public function scopeActiveLoans($query)
    {
        return $query->where('status', 'borrowed')
                    ->where('confirmation_status', 'approved');
    }

    /**
     * Check if overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'borrowed' && 
               $this->due_date->isPast();
    }

    /**
     * Get human readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'lost' => 'Hilang',
            default => $this->status,
        };
    }

    /**
     * Get human readable confirmation status
     */
    public function getConfirmationLabelAttribute(): string
    {
        return match($this->confirmation_status) {
            'pending' => 'Menunggu Konfirmasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->confirmation_status,
        };
    }
}

