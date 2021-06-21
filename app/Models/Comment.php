<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comment extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['user'];

    /**
     * Get the post that owns the comment.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * Get the post that owns the comment.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
