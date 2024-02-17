<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'time_to_use' => 'datetime',
    ];

    public function uniqueIds()
    {
        return ['ulid'];
    }

    public function isPublished(): bool
    {
        if ($this->published_at === null) {
            return false;
        }

        return $this->published_at->lte(now());
    }

    public function totalTicketsCount(): int
    {
        return $this->quantity + $this->sold_count;
    }

    public function totalRevenueInDollars(): string
    {
        return number_format($this->orders()->get(['charged'])->pluck('charged')->sum() / 100, 2);
    }

    public function soldOutPercentage(): string
    {
        return number_format($this->sold_count / $this->totalTicketsCount() * 100, 2);
    }

    public function chunkAttendeeEmails(int $chunkCount, callable $callable): void
    {
        $this->orders()->chunk($chunkCount, function ($orders) use ($callable) {
            $orders->pluck('email')->each(fn ($email) => $callable($email));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->distinct();
    }

    public function attendeeMessages(): HasMany
    {
        return $this->hasMany(AttendeeMessage::class);
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->price / 100, 2)
        );
    }

    protected function formattedTimeToUse(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->time_to_use->format('D M d h:iA')
        );
    }

    protected function soldOut(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity === 0
        );
    }

    public function scopePublished(Builder $builder): void
    {
        $builder->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $builder): void
    {
        $builder->whereNull('published_at');
    }
}
