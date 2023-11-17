<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StripePaymentMethod extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['response' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
