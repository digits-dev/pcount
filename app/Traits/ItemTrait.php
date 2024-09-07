<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait ItemTrait
{
    public function getApiData($url){
        $secretKey = config('item-api.secret_key');
        $uniqueString = time();
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        if($userAgent == '' || is_null($userAgent)){
            $userAgent = config('item-api.user_agent');
        }
        $xAuthorizationToken = md5( $secretKey . $uniqueString . $userAgent);
        $xAuthorizationTime = $uniqueString;

        $apiItems = Http::withHeaders([
            'X-Authorization-Token' => $xAuthorizationToken,
            'X-Authorization-Time' => $xAuthorizationTime,
            'User-Agent' => $userAgent
        ])->get($url,[
            'page' => 1,
			'limit' => 1000,
        ]);

        return json_decode($apiItems->body(), true);
    }
}
