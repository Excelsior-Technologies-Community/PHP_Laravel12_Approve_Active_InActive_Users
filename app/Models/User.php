<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'status',
        'is_approved',
        'phone',
        'approved_at',
        'last_login',
        'id_document_type',
        'id_document_path',
        'kyc_status',
        'kyc_notes',
        'kyc_verified_at',
        'kyc_verified_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'last_login' => 'datetime',
        'kyc_verified_at' => 'datetime',
    ];

    // Relationships
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function kycVerifier()
    {
        return $this->belongsTo(User::class, 'kyc_verified_by');
    }

    // Scopes
    public function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'admin');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kyc_status', 'verified');
    }

    public function scopeKycPending($query)
    {
        return $query->where('kyc_status', 'pending');
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->user_type === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isKycVerified(): bool
    {
        return $this->kyc_status === 'verified';
    }

    public function isKycPending(): bool
    {
        return $this->kyc_status === 'pending';
    }

    public function isKycRejected(): bool
    {
        return $this->kyc_status === 'rejected';
    }

    public function getKycDocumentUrlAttribute(): ?string
    {
        if (!$this->id_document_path) {
            return null;
        }

        return Storage::disk('public')->url($this->id_document_path);
    }

    public function isKycDocumentImage(): bool
    {
        if (!$this->id_document_path) {
            return false;
        }

        $extension = strtolower(pathinfo($this->id_document_path, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'inactive' => 'bg-danger',
            'pending' => 'bg-warning text-dark',
            default => 'bg-secondary'
        };
    }

    public function getApprovalBadgeClass(): string
    {
        return $this->is_approved ? 'bg-success' : 'bg-warning text-dark';
    }

    public function getKycBadgeClass(): string
    {
        return match ($this->kyc_status) {
            'verified' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }
}
