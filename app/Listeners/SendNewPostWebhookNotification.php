<?php

namespace App\Listeners;

use App\Events\PostPublished;
use App\Jobs\NotifySubscribers;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewPostWebhookNotification
{

    private Post $post;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PostPublished  $event
     * @return void
     */
    public function handle(PostPublished $event)
    {
        $this->post = $event->post;
        
        NotifySubscribers::dispatch($this->post);
    }
}
