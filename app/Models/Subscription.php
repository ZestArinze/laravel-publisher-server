<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'topic_id',
        'subscriber_id',
    ];

    /**
     * the topic that owns this subscription
     */
    public function topic() {
        return $this->belongsTo(Topic::class);
    }

    /**
     * the subscriber that made this subscription
     */
    public function subscriber() {
        return $this->belongsTo(Subscriber::class);
    }
}
