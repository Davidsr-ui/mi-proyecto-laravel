<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'director',
        'year',
        'genre',
        'rating',
        'image'
    ];

    protected $casts = [
        'year' => 'integer',
        'rating' => 'decimal:1'
    ];

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return asset('images/no-image.png');
    }

    /**
     * Delete the image file when deleting the movie
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($movie) {
            if ($movie->image && Storage::exists($movie->image)) {
                Storage::delete($movie->image);
            }
        });
    }
}