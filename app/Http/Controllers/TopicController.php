<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Topic;
use App\Utils\SecurityUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    /**
     * Get a listing of topics.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {

        $data = Topic::all();

        return response()->json([
            'status'  => true,
            'message' => 'OK.',
            'data'    => $data,
            'error'   => null,
        ]);
    }

    /**
     * subscribe to topic
     * 
     * @return JsonResponse
     */
    public function subscribe(Request $request, $topicIdentifier): JsonResponse
    {
        $request->merge(['topic_identifier' => $topicIdentifier,]);
        Validator::make($request->all(), [
            'url'           => 'required|url|max:255',
            'topic_identifier'   => 'required|string|exists:topics,identifier'
        ],[
           'topic_identifier.exists' => 'No such :attribute.', 
        ])->validate();

        // validate HMAC and get subscriber
        $subscriber = SecurityUtils::getSubscriberFrom($request->header('ClientId'), $request->header('HashMac')); 

        if (!$subscriber) {
            return response()->json([
                'status'  => false,
                'message' => 'Valid ClientId and HashMac headers required.',
                'data'    => null,
                'error'   => null,
            ], 401);
        }

        $responseCode = 200;

        $topic = Topic::where('identifier', $request->topic_identifier)->first();
        $subscriptionData = [
            'topic_id'          => $topic->id,
            'url'               => $request->url,
            'subscriber_id'     => $subscriber->id,
        ];
        $subscription = Subscription::where($subscriptionData)->first();
        if($subscription) {
            $subscription->save($subscriptionData);
        } else {
            $subscription = Subscription::create($subscriptionData);
            $responseCode = 201;
        }

        // return response()->json([
        //     'status'  => true,
        //     'message' => 'OK.',
        //     'data'    => [
        //         'url' => $request->url,
        //         'topic' => $topic->topic,
        //         'topic_identifier'  => $topic->identifier,
        //     ],
        //     'error'   => null,
        // ], $responseCode);

        // response structure tha corresponds to the 
        // one in the assignment doc
        return response()->json([
            'url' => $request->url,
            'topic' => $topic->topic,

            'topic_identifier'  => $topic->identifier,
            'status'  => true,
            'message' => 'Subscription successful.',
            'error'   => null,
        ], $responseCode);
    }

    /**
     * Store a newly created topic in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'topic' => 'required|string|max:100|unique:topics,topic',
        ]);

        $topic = new Topic($validatedData);
        $topic->identifier = Str::slug($request->topic);
        $topic->user_id = auth()->id();
        $topic->save(); 
        
        return response()->json([
            'status'  => true,
            'message' => 'Topic created.',
            'data'    => $topic,
            'error'   => null,
        ], 201);
    }

    /**
     * Get the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $topic = Topic::find($id);

        if(!$topic) {
            return response()->json([
                'status'  => false,
                'message' => 'No such topic.',
                'data'    => $topic,
                'error'   => null,
            ], 404);
        }
        
        return response()->json([
            'status'  => true,
            'message' => 'Resource retrieved.',
            'data'    => $topic,
            'error'   => null,
        ]);
    }

    /**
     * Update the specified topic in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $topic = Topic::find($id);

        if(!$topic) {
            return response()->json([
                'status'  => false,
                'message' => 'No such topic.',
                'data'    => $topic,
                'error'   => null,
            ], 404);
        }

        $validatedData = $request->validate([
            'topic' => 'required|string|max:100|unique:topics,topic,' . $topic->id,
        ]);

        $topic->update($validatedData);
        
        return response()->json([
            'status'  => true,
            'message' => 'Topic updated.',
            'data'    => $topic,
            'error'   => null,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $topic = Topic::find($id);

        if(!$topic) {
            return response()->json([
                'status'  => false,
                'message' => 'No such topic.',
                'data'    => $topic,
                'error'   => null,
            ], 404);
        }

        $topic->destroy();
        
        return response()->json([
            'status'  => true,
            'message' => 'Topic deleted.',
            'data'    => null,
            'error'   => null,
        ]);
    }
}
