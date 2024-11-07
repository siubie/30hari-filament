<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    //add fillables
    protected $fillable = ['title', 'author', 'description', 'status', 'image'];

    //add model observer
    protected static function booted()
    {
        //add delete event
        static::deleting(function ($book) {
            //delete image
            Storage::disk('public')->delete($book->image);
        });
    }
}
