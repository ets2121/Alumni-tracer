<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsEventComment extends Model
{
    protected $fillable = ['news_event_id', 'user_id', 'parent_id', 'content'];

    public function newsEvent()
    {
        return $this->belongsTo(NewsEvent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(NewsEventComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(NewsEventComment::class, 'parent_id')->latest();
    }
}
