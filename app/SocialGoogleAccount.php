<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialGoogleAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider_user_id',
        'provider'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
