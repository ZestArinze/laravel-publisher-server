<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
    ];

    /**
     * The subscriptions to the topic.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
