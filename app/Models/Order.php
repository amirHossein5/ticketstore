<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\UniqueConstraintViolationException;

class Order extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    public function addTicket(Ticket $ticket, int $count = 1): self
    {
        for ($i = 1; $i <= $count; $i++) {
            try {
                $this->tickets()->attach($ticket, [
                    'code' => short_code(),
                ]);
            } catch (UniqueConstraintViolationException $e) {
                $this->addTicket($ticket);
            }
        }

        return $this;
    }

    public function uniqueIds()
    {
        return ['code'];
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class)->withPivot('code');
    }

    protected function chargedInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->charged / 100, 2)
        );
    }
}
