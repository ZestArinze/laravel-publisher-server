<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\Topic;
use App\Utils\SecurityUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookServer\WebhookCall;

class NotifySubscribers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Post $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $topic = Topic::find($this->post->topic_id);
        $payload = [
            'topic' => $topic->topic,
            'data' => [
                'title'     => $this->post->title,
                'body'      => $this->post->body,
                'slug'      => $this->post->slug,
            ],
        ];

        $subscriptions = $topic->subscriptions;
        
        foreach($subscriptions as $subscription) {

            $secret = SecurityUtils::getDecrypted($subscription->subscriber->client_secret);
            $mac = SecurityUtils::getHashMac($subscription->subscriber->client_id, $secret);

            if(!$secret) {
                // @TODO?
                continue;
            }

            WebhookCall::create()
                ->url($subscription->url)
                ->withHeaders([
                    'HashMac' => $mac,
                ])
                ->payload($payload)
                ->useSecret($secret)
                ->dispatch();
        }
    }
}
