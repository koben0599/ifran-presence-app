<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'titre',
        'message',
        'type',
        'action_url',
        'action_text',
        'lu',
        'lu_at'
    ];

    protected $casts = [
        'lu' => 'boolean',
        'lu_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marquer comme lue
     */
    public function markAsRead(): void
    {
        $this->update([
            'lu' => true,
            'lu_at' => now()
        ]);
    }

    /**
     * Marquer comme non lue
     */
    public function markAsUnread(): void
    {
        $this->update([
            'lu' => false,
            'lu_at' => null
        ]);
    }

    /**
     * Vérifier si la notification est récente (moins de 24h)
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInHours(now()) < 24;
    }

    /**
     * Obtenir la classe CSS selon le type
     */
    public function getCssClassAttribute(): string
    {
        $classes = [
            'info' => 'bg-blue-50 border-blue-200 text-blue-800',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'danger' => 'bg-red-50 border-red-200 text-red-800',
            'success' => 'bg-green-50 border-green-200 text-green-800',
        ];

        return $classes[$this->type] ?? $classes['info'];
    }

    /**
     * Obtenir l'icône selon le type
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'info' => 'fas fa-info-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'danger' => 'fas fa-times-circle',
            'success' => 'fas fa-check-circle',
        ];

        return $icons[$this->type] ?? $icons['info'];
    }

    /**
     * Scope pour les notifications non lues
     */
    public function scopeUnread($query)
    {
        return $query->where('lu', false);
    }

    /**
     * Scope pour les notifications lues
     */
    public function scopeRead($query)
    {
        return $query->where('lu', true);
    }

    /**
     * Scope pour les notifications récentes
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope pour un type spécifique
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
} 