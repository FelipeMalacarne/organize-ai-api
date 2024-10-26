<?php

namespace App\Models;

use App\Events\Document\Created;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedExplosion\Sqids\Concerns\HasSqids;

class Document extends Model
{
    use HasFactory, HasSqids, SoftDeletes;

    protected $sqidPrefix = 'doc';

    protected $fillable = [
        'user_id',
        'title',
        'file_path',
        'file_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $dispatchesEvents = [
        'created' => Created::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function extractions(): HasMany
    {
        return $this->hasMany(Extraction::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
