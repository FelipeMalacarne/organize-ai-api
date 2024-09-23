<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RedExplosion\Sqids\Concerns\HasSqids;

class Tag extends Model
{
    use HasFactory, HasSqids;

    protected $sqidPrefix = 'tag';

    protected $fillable = [
        'name',
    ];

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class)->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, int $user_id)
    {
        return $query->where('user_id', $user_id)->orWhereNull('user_id');
    }
}
