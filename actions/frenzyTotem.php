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

    public static function finish($data){

        $message = $data['message'];
        $reply['replyToken'] = $data['token'];

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