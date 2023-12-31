<?php
include_once(__DIR__ . '/../__Class/ClassLoad.php');

class frenzyTotem
{
    public static function start(){
        /* 變更動作 */
        MYPDO::$table = 'action';
        MYPDO::$data = [
            'code' => 1,
            'name' => '新增輪燒紀錄',
            'time' => date("Y/m/d H:i:s")
        ];
        $insert_id = MYPDO::insert();

        if ($insert_id > 0){
            $msg = '請輸入紀錄資訊';
        }else{
            $msg = '出租資料紀錄失敗';
        }
        return $msg;
    }

    public static function start_detail($data){
        $message = $data['message'];
        $reply['replyToken'] = $data['token'];
        $message_data = explode(' ', $message);
        $message_data_count = count($message_data);

        /* 11/21 1.5H +1.8E */

        foreach ($message_data as $mdkey => $mdval){
            $now_value = strtoupper($mdval);
            
            if (strpos($now_value, 'H') !== false){         /* 租用時長 */
                $time_long = explode('H', $now_value)[0];
            }else if (strpos($now_value, ':') !== false){   /* 開始時間 */
                $start_at = $now_value;
            }else if (strpos($now_value, '+') !== false){   /* 金額 */
                $amount = explode('+', $now_value)[1];  
                $unit = substr($amount, -1);            /* 單位 */
                if ($unit == 'W'){
                    $amount_maple = explode('W', $amount)[0];
                }else if ($unit == 'E'){            // 楓幣
                    $amount_maple = explode('E', $amount)[0];
                }else{                              // Linepay
                    $amount_ntd = $amount;
                }
            }
        }

        $type = '0';
        $amount_maple = ($unit == 'W') ? ($amount_maple / 10000) : $amount_maple;
        $amount_maple = ($amount_maple > 0) ? $amount_maple : 0;
        $amount_ntd = ($amount_ntd > 0) ? $amount_ntd : 0;
        $start_at = ($start_at == '') ? date("H:i") : $start_at;
        $finished_at = date("H:i", strtotime($start_at) + $time_long * 60 * 60);
        $record_date = date("Y/m/d");

        $record = [];
        $record['type'] = $type;
        $record['amount_maple'] = $amount_maple;    /* 寫入單位為億 */
        $record['amount_ntd'] = $amount_ntd;
        $record['start_at'] = $start_at;
        $record['finished_at'] = $finished_at;
        $record['date'] = $record_date;

        MYPDO::$table = 'frenzy_totem';
        MYPDO::$data = $record;
        $insert_id = MYPDO::insert();

        /* 變更動作 */
        MYPDO::$table = 'action';
        MYPDO::$data = [
            'code' => 1,
            'name' => '新增輪燒紀錄',
            'time' => date("Y/m/d H:i:s")
        ];
        MYPDO::insert();

        if ($type == 0){
            $render_type = '輪燒';
        }else if ($type == 1){
            $render_type = '單輪';
        }else if ($type == 2){
            $render_type = '單燒';
        }
        if ($amount_maple > 0){
            $amount_maple = ($unit == 'W') ? ($amount_maple * 10000) : $amount_maple;
            $message_amount = $amount_maple . $unit . '(楓幣)';
        }else if ($amount_ntd > 0){
            $message_amount = $amount_ntd . '(Linepay)';
        }
        if ($insert_id > 0){
            $msg = "紀錄資料如下\n";
            $msg = $msg . "類型: " . $render_type . "\n";
            $msg = $msg . "金額: " . $message_amount ."\n";
            $msg = $msg . "開始時間: " . $start_at ."\n";
            $msg = $msg . "結束時間: " . $finished_at ."\n";
            $msg = $msg . "日期: " . $record_date ."\n";
            $payload = [
                'replyToken' => $reply['replyToken'],
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => '出租資料已建立'
                    ],
                    [
                        'type' => 'text',
                        'text' => $msg
                    ]
                ]
            ];
            custom_class::send_reply($payload);
        }else{
            $msg = '出租資料紀錄失敗';
            $reply['msg'] = $msg;
            reply::common($reply);
        }
    }

    public static function finish($data){

        $message = $data['message'];
        $reply['replyToken'] = $data['token'];

        $data_arr = explode(' ', $message);
        $data_count = count($data_arr);
        $date = $data_arr[0];
        $time = $data_arr[1];

        $message_data = explode('<br />', $message);
        $type = trim(explode(':', $message_data[1])[1]);
        $payment = trim(explode(':', $message_data[2])[1]);
        $payment_type = trim(explode(':', $message_data[3])[1]);

        /* 判別表單資料 */
        if ($type == '') $type = 0;
        if ($payment_type == '') $payment_type = 0;

        /* 撈出最後一筆資料 */
        MYPDO::$table = 'reincarnationRecords';
        MYPDO::$order = ['id' => 'DESC'];
        $result = MYPDO::first();
        $last_id = $result['id'];

        /* 更新資料 */
        MYPDO::$table = 'reincarnationRecords';
        MYPDO::$data = [
            'type' => $type,
            'payment' => $payment,
            'payment_type' => $payment_type,
            'finished_at' => date("H:i:s")
        ];
        MYPDO::$where = ['id' => $last_id];
        $update_id = MYPDO::save();

        if ($update_id > 0){
            $msg = '出租資料更新成功，結束時間為' . date("H:i");
        }else{
            $msg = '出租資料更新失敗';
        }

        $reply['msg'] = $msg;
        reply::common($reply);
    }

    public static function finish_table($event){

        $msg = "※結束輪燒紀錄※\n";
        $msg = $msg . "類型: \n";
        $msg = $msg . "金額: \n";
        $msg = $msg . "支付方式: \n";

        $payload = [
            'replyToken' => $event['replyToken'],
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '請填寫以下表單'
                ],
                [
                    'type' => 'text',
                    'text' => $msg
                ]
            ]
        ];
        custom_class::send_reply($payload);
    }
}

?>