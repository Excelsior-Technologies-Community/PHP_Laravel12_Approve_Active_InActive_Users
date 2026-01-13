<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'last_login'
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

    // Helpers
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'inactive' => 'bg-danger',
            'pending' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    public function getApprovalBadgeClass()
    {
        return $this->is_approved ? 'bg-success' : 'bg-warning';
    }
}