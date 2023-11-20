<?php
include_once(__DIR__ . '/ClassLoad.php');

class reply
{
    /** 
     * 單筆回復 
     * @param string $replyToken    Line回覆Token
     * @param string $msg           回覆訊息
     */
    public static function common($reply){
        $payload = [
            'replyToken' => $reply['replyToken'],
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $reply['msg']
                ]
            ]
        ];
        custom_class::send_reply($payload);
    }

    // public static function custom($payload){
    //     custom_class::send_reply($payload);
    // }
}

?>