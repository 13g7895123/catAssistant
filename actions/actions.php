<?php

include_once(__DIR__ . '/frenzyTotem.php');

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
        }else{
            /* 取得目前動作 */
            MYPDO::$table = 'action';
            MYPDO::$order = ['time' => 'desc'];
            $result = MYPDO::first();

            /* 時間差 */
            $exe_time = strtotime($result['time']);
            $now_time = strtotime(date('Y/m/d H:i:s'));
            $diff_minute = round(abs($now_time - $exe_time) / 60, 2);

            if ($result['code'] == 1 && $diff_minute < 3){  /* 新增紀錄 */
                /* 執行動作 */
                $data = [];
                $data['message'] = $message;
                $data['token'] = $token;
                frenzyTotem::start_detail($data);
            }else{                                          /* 預設動作為回覆一樣的訊息 */
                $reply['msg'] = $message;
                $reply['replyToken'] = $token;
                reply::common($reply);
            }
        }
    }
}

?>