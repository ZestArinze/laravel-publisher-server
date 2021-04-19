<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'status',
        'topic_id',
    ];

    /**
     * the topic this post belongs to.
     */
    public function topic() {
        return $this->belongsTo(Topic::class);
    }
}
