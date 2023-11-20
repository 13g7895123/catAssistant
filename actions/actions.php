<?php

include_once(__DIR__ . '/reincarnationRecords.php');

class default_action
{
    public static function table($event){
        $message = $event['message']['text'];
        $token = $event['replyToken'];

        if (strpos($message, '※') !== false){
            $tmp_msg = nl2br($message);
            $first_line = explode('<br />', $tmp_msg)[0];

            $rR_data = [];
            $rR_data['message'] = $tmp_msg;
            $rR_data['token'] = $token;
            
            switch ($first_line){
                case '※結束輪燒紀錄※':
                    reincarnationRecords::finish($rR_data);
                    break;
            }
        }
    }
}

?>