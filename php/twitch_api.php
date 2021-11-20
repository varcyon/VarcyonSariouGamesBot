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
        $status = $accessToken['status'];
        $message = $accessToken['message'];

        if('ok' == $status){
            $this->_accessToken = $accessToken['api_data']['access_token'];
            $this->_refreshToken = $accessToken['api_data']['refresh_token'];

            $user_info = $this->getUserInfo();

            echo '<pre>';
            print_r($user_info);
            
            $status = $user_info['status'];
            $message = $user_info['message'];

            if('ok' == $user_info['status'] && isset($user_info['api_data']['data'][0])){
                $this->_logUserInWithTwitch($user_info['api_data']['data'][0]);
            }
    
        }
        return array(
            'status'=> $status,
            'message'=> $message
        );
    }
    private function _logUserInWithTwitch($apiUserInfo){
        $_SESSION['twitch_user_info'] = $apiUserInfo;
        $_SESSION['twitch_user_info']['access_token'] = $this->_accessToken;
        $_SESSION['twitch_user_info']['refresh_token'] = $this->_refreshToken;
        echo '<pre>';
        print_r($_SESSION['twitch_user_info']);
        die();
    //TODO: Look user up in DynamoDB
    }
    public function getUserInfo(){
        $endpoint = self::TWITCH_API_DOMAIN . 'users';
        $apiParams = array(
            'endpoint' => $endpoint,
            'type' => 'GET',
            'authorization' => $this->getAuthorizationHeaders(),
            'url_params' => array()
        );
        return $this->makeApiCall($apiParams);

    }
    public function getAuthorizationHeaders(){
        return array(
            'Client-ID: '. $this->_clientId,
            'Authorization: Bearer ' . $this->_accessToken
        );
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

        if(isset($params['authorization'])){
            $curlOptions[CURLOPT_HEADER] = TRUE;
            $curlOptions[CURLOPT_HTTPHEADER] = $params['authorization'];
        }

        if('POST'== $params['type']){
            $curlOptions[CURLOPT_POST] = TRUE;
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($params['url_params']);
        } elseif ('GET' == $params['type']){
            $curlOptions[CURLOPT_URL].= '?' . http_build_query($params['url_params']);
        }

        $ch = curl_init();
        curl_setopt_array($ch,$curlOptions);
        $apiResponse = curl_exec($ch);
        if(isset($params['authorization'])){
            $headerSize = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
            $apiResponseBody = substr($apiResponse,$headerSize);
            $apiResponse = json_decode($apiResponseBody, true);
        } else {
            $apiResponse = json_decode($apiResponse, true);
        }
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