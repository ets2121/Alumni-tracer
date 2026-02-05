<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsEventReaction extends Model
{
    protected $fillable = ['news_event_id', 'user_id', 'type'];

    public function newsEvent()
    {
        return $this->belongsTo(NewsEvent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
