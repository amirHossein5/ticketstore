<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    protected $casts = [
        'time_to_use' => 'datetime',
    ];

    public function uniqueIds()
    {
        return ['ulid'];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->published()->where($field, $value)->firstOrFail();
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
}
