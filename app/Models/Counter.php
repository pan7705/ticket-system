<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'is_online',
        'is_serving',
        'current_ticket_number'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
