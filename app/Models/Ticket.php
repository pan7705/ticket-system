<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'ticket_number',
        'status',
        'counter_id'
    ];

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }
}
