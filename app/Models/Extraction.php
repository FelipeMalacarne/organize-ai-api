<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RedExplosion\Sqids\Concerns\HasSqids;

class Extraction extends Model
{
    use HasFactory, HasSqids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'document_id',
        'type',
        'extracted_text',
        'extracted_json',
    ];

    /**
     * Get the document that owns the extraction.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
