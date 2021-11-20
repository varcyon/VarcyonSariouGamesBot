<?php

class vsgTwitchAPI{
    const TWITCH_ID_DOMAIN = 'https://id.twitch.tv/';
    const TWITCH_API_DOMAIN = 'https://api.twitch.tv/helix/';

    private $_clientId;
    private $_clientSecret;
    private $_accessToken;
    Private $_refresthToken;

    public function __construct($clientId,$clientSecret,$accessToken = ''){
        $this->_clientId = $clientId;
        $this->_clientSecret = $clientSecret;
        $this->_accessToken = $accessToken;
    }
    public function getLoginUrl($redirectUri){
        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/authorize';
        $_SESSION['twitch_state']= md5(microtime().mt_rand());
        $params = array(
            'client_id' => $this->_clientId,
            'redirect_uri'=> $redirectUri,
            'response_type' => 'code',
            'scope'=> 'user:read:email',
            'state'=> $_SESSION['twitch_state']
        );
        return $endpoint . '?'. http_build_query($params);
    }
    public function tryAndLoginWithTwitch($code,$redirectUri){
        $accessToken = $this->getTwitchAccessToken($code,$redirectUri);
        echo '<pre>';
        print_r($accessToken);
        die();
    }
    public function getTwitchAccessToken($code,$redirectUri){
        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/token';

        $apiParams = array(
            'endpoint' => $endpoint,
            'type' => 'POST',
            'url_params' => array(
                'client_id' => $this->_clientId,
                'client_secret' => $this->_clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri
            )
        );

        return $this->makeApiCall ($apiParams);
    }
    public function makeApiCall($params){
        $curlOptions = array(
            CURLOPT_URL => $params['endpoint'],
            CURLOPT_CAINFO => PATH_TO_CERT,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => TRUE,
            CURLOPT_SSL_VERIFYHOST=> 2
        );

        if('POST'== $params['type']){
            $curlOptions[CURLOPT_POST] = TRUE;
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($params['url_params']);
        }

        $ch = curl_init();
        curl_setopt_array($ch,$curlOptions);
        $apiResponse = curl_exec($ch);
        $apiResponse = json_decode($apiResponse, true);
        curl_close($ch);

        return array(
            'status' =>isset($apiResponse['status'])? 'fail' : 'ok',
            'message'=>isset($apiResponse['message'])? $apiResponse['message'] : '',
            'api_data' => $apiResponse,
            'endpoint'=> $curlOptions[CURLOPT_URL],
            'url_params'=> $params['url_params']
        );
    }
}