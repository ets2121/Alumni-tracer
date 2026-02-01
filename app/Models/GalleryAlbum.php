<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryAlbum extends Model
{
    protected $fillable = ['name', 'description', 'cover_image', 'category', 'is_default'];

    public function photos()
    {
        return $this->hasMany(GalleryPhoto::class, 'album_id');
    }
}
