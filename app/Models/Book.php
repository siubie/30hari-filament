<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Book extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    //add fillables
    protected $fillable = ['title', 'author', 'description', 'status', 'image'];

    //define collection name
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('book-pdf')
            ->acceptsMimeTypes(['application/pdf']);
    }

    //add model observer
    protected static function booted()
    {
        //add delete event
        static::deleting(function ($book) {
            //delete image
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }
        });
    }
}
