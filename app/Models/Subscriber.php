<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['client_secret'];

    /**
     * The subscriptions to the subscriber.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
