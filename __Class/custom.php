<?php

include_once(__DIR__ . '/../config/config.php');

class custom_class
{
    public static function send_reply($reply){

        // Send reply API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, lineReplyUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($reply));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . channelAccessToken
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public static function get_profile($uid){

        $url = lineProfileUrl . $uid;

        // Send profile API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . channelAccessToken
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        return $result['displayName'];
    }

    public static function test(){
        echo lineReplyUrl;
    }
}

// custom_class::test();

?>