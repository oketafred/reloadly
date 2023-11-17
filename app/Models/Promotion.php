<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'end_date',
        'start_date',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
