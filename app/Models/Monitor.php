<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'url', 'check_interval', 'expected_status_code',
        'timeout', 'track_ssl', 'status', 'uptime', 'avg_response_time',
        'total_checks', 'last_checked_at', 'ssl_expiry_date', 'ssl_issuer',
        'ssl_days_remaining', 'ssl_expiring',
    ];

    protected $casts = [
        'track_ssl' => 'boolean',
        'ssl_expiring' => 'boolean',
        'last_checked_at' => 'datetime',
        'uptime' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(MonitorCheck::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function getIntervalInSeconds(): int
    {
        return match ($this->check_interval) {
            '30s' => 30,
            '1m' => 60,
            '5m' => 300,
            '15m' => 900,
            '30m' => 1800,
            '1h' => 3600,
            '6h' => 21600,
            '24h' => 86400,
            default => 60,
        };
    }

    public function isDueForCheck(): bool
    {
        if (! $this->last_checked_at) {
            return true;
        }

        return now()->diffInSeconds($this->last_checked_at) >= $this->getIntervalInSeconds();
    }
}
