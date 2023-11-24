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

        /* 日期 */
        $date = $message_data[0];
        /* 租用時長 */
        $time_long_str = strtoupper($message_data[1]);
        if (strpos($time_long_str, 'H') !== false){
            $time_long = explode('H', $time_long_str)[0];
        }
        /* 租用時間 */
        if ($message_data_count == 4){

        }
        /* 金額 */
        if ($message_data_count == 3){
            $amount_str = strtoupper($message_data[2]);   
        }else if ($message_data_count == 4){
            $amount_str = strtoupper($message_data[3]);  
        }
        if (strpos($amount_str, '+') !== false){
            /* 取得金額 */
            $amount = explode('+', $amount_str)[1];
            /* 判斷單位 */
            $unit = substr($amount, -1);        // 楓幣
            if ($unit == 'W'){
                $amount_maple = (explode('W', $amount_str)[0]) / 1000;
            }else if ($unit == 'E'){            // 楓幣
                $amount_maple = explode('E', $amount_str)[0];
            }else{                              // Linepay
                $amount_ntd = $amount;
            }
        }

        $amount_maple = ($amount_maple > 0) ? $amount_maple : 0;
        $amount_ntd = ($amount_ntd > 0) ? $amount_ntd : 0;

        $record = [];
        $record['type'] = '0';
        $record['amount_maple'] = $amount_maple;
        $record['amount_ntd'] = $amount_ntd;
        $record['start_at'] = date("H:i:s");
        $record['finished_at'] = date("H:i:s", strtotime(date("H:i:s")) + $time_long * 60 * 60);
        $record['date'] = date("Y/m/d");

        MYPDO::$table = 'frenzyTotemRecords';
        MYPDO::$data = $record;
        $insert_id = MYPDO::insert();

        // $reply['msg'] = json_encode($record);
        $reply['msg'] = $insert_id;
        reply::common($reply);
        return;

        /* 變更動作 */
        MYPDO::$table = 'action';
        MYPDO::$data = [
            'code' => 1,
            'name' => '新增輪燒紀錄',
            'time' => date("Y/m/d H:i:s")
        ];
        MYPDO::insert();

        if ($insert_id > 0){
            $msg = '出租資料已建立';
        }else{
            $msg = '出租資料紀錄失敗';
        }
        $reply['msg'] = $msg;
        reply::common($reply);
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