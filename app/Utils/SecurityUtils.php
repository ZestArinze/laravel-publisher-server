<?php

namespace App\Utils;

use App\Models\Subscriber;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class SecurityUtils {

    /**
     * @param string $secret a string to encrypt
     * @return string an encrypted string
     */
    public static function getEncrypted(string $secret) {
        return Crypt::encryptString($secret);
    }

    /**
     * @param string $secret a string to decrypt
     * @return string|null decrypted string or null
     */
    public static function getDecrypted(string $secret) {
        $decrypted = null;
        
        try {
            $decrypted = Crypt::decryptString($secret);
        } catch (DecryptException $e) {
            //
        }

        return $decrypted;
    }

    /**
     * 
     * get subscriber via HMAC
     * 
     * @param string $clientId client id
     * @param string $macString HMAC
     * @return Subscriber subscriber
     */
    public static function getSubscriberFrom($clientId, $macString): ?Subscriber {

        if((strlen(trim($macString)) < 1) || strlen(trim($clientId)) < 1) return null;

        $subscriber = Subscriber::where('client_id', $clientId)->first();
        if(!$subscriber) return null;

        $clientSecret = SecurityUtils::getDecrypted($subscriber->client_secret);
        if(!$clientSecret) return null;

        // use the client secret to compute the mac
        $mac = base64_encode(hash_hmac('sha256', $clientId, $clientSecret, true));    

        if($mac !== null && hash_equals($mac, $macString)) {
            return $subscriber;
        }

        return null;
    }

    /**
     * 
     * get subscriber via HMAC
     * 
     * @param string $clientId client id
     * @param string $clientSecret client secret
     * @return Subscriber HMAC|null
     */
    public static function getHashMac($clientId, $clientSecret): ?string {

        if((strlen(trim($clientSecret)) < 1) || strlen(trim($clientId)) < 1) return null;

        // use the client secret to compute the mac
        return base64_encode(hash_hmac('sha256', $clientId, $clientSecret, true)); 
    }
}
