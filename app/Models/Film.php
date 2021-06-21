<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'description',
        'release_date',
        'rating',
        'price',
        'photo',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['genres', 'country', 'comments'];

    /**
     * Film belongs to many Genres
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'films_genres');
    }

    /**
     * Film belongs to a country
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the comments for the film.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
