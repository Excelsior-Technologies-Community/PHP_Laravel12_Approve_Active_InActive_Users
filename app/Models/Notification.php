<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    // Get notification icon
    public function getIconClass()
    {
        return match($this->type) {
            'request_update' => 'bi bi-envelope-check',
            'account_update' => 'bi bi-person-check',
            'system' => 'bi bi-gear',
            'info' => 'bi bi-info-circle',
            default => 'bi bi-bell'
        };
    }
}