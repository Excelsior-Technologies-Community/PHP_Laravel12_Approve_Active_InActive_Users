<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'status',
        'admin_notes',
        'submitted_at',
        'processed_at',
        'processed_by'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    // Get status badge class
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'in_progress' => 'bg-info',
            'pending' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    // Get request type label
    public function getTypeLabel()
    {
        return match($this->type) {
            'account_activation' => 'Account Activation',
            'service_request' => 'Service Request',
            'information_change' => 'Information Change',
            'other' => 'Other Request',
            default => ucfirst($this->type)
        };
    }
}