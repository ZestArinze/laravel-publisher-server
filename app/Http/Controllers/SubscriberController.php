<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Utils\SecurityUtils;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class SubscriberController extends Controller
{

    //----------------------------------------------------------
    // Ignored managing credentials, revoking, refresh, etc.
    //----------------------------------------------------------


    /**
     * make client ID and secret
     */
    public function makeCredentials(): JsonResponse 
    {
        // for the purpose of this demo
        // make some random string to serve as the secret key
        $secretKey = Str::random(32);
        $subscriber = new Subscriber();
        $subscriber->client_secret = SecurityUtils::getEncrypted($secretKey);

        // make unique client id
        $date = new DateTime();
        $subscriber->client_id = Str::random() . $date->getTimestamp();

        $subscriber->save();

        return response()->json([
            'status'  => true,
            'message' => 'New client credentials generated successfully.',
            'data'    => [
                'client_id' => $subscriber->client_id,
                'client_secret' => $secretKey
            ],
            'error'   => null,
        ], 201);
    }
}
