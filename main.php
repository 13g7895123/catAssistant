<?php
include_once(__DIR__ . '/__Class/ClassLoad.php');
include_once(__DIR__ . '/actions/actions.php');
include_once(__DIR__ . '/frenzyTotem.php');

$bodyMsg = file_get_contents('php://input');
$obj = json_decode($bodyMsg, true);

foreach ($obj['events'] as $event) {
    $userId = $event['source']['userId'];
    $message = $event['message']['text'];
    $userName = custom_class::get_profile($userId);

    $reply = [];
    $reply['replyToken'] = $event['replyToken'];

    switch ($message){
        case 'menu':
            $msg = "你好，這裡是貓貓助手\n";
            $msg = $msg . "請選擇您要的項目:\n";
            $msg = $msg . "1. 新增輪燒紀錄\n";
            $msg = $msg . "2. 結束輪燒紀錄\n";
            $msg = $msg . "3. 查詢輪燒紀錄\n";
            $msg = $msg . "4. 使用說明\n";
            $reply['msg'] = $msg;
            reply::common($reply);
            break;
        case '1':
            $msg = frenzyTotem::start();
            break;
        case '2':
            reincarnationRecords::finish_table($event);
            break;
        case 'test':
            $msg = '123';
            break;
        default:
            default_action::table($event);
    }

    if ($msg != ''){
        $reply['msg'] = $msg;
        reply::common($reply);
    }
}

?>
