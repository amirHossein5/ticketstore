<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->price / 100, 2)
        );
    }

    protected function formattedTimeToUse(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->time_to_use->format('D M d h:iA')
        );
    }
}
