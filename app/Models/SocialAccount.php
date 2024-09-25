<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedExplosion\Sqids\Concerns\HasSqids;

class SocialAccount extends Model
{
    use HasFactory, HasSqids;

    protected $fillable = [
        'user_id',
        'provider_name',
        'provider_id',
        'token',
        'refresh_token',
        'expires_in',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
