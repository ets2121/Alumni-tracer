<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsEventPhoto extends Model
{
    protected $fillable = ['news_event_id', 'image_path'];

    public function event()
    {
        return $this->belongsTo(NewsEvent::class, 'news_event_id');
    }
}
