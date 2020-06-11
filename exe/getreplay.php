<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/function.php');
    if(isset($_POST['auth_code'])){
        if(!checkAuth($_POST['auth_code'])){
            echo json_encode(array(
                'error'=>'authentication_error',
                'error_description'=>'アクセスの認証に失敗しました。'
            ));
            exit;
        }
    }else{
        echo json_encode(array(
            'error'=>'not_access_from_lobby',
            'error_description'=>'アクセスの認証に失敗しました。(2)'
        ));
        exit;
    }
    $result=false;
    $room_id='';
    $room_log_dir='';
    if(!empty($_POST['room_id'])){
        $room_id=$_POST['room_id'];
        $room_log_dir=DIR_ROOT.'r/n/'.$room_id.'/log/';
    }else{
        echo json_encode(array(
            'replay'=>'リプレイはありません。'
        ));
        exit;
    }
    $principal_id='';
    if(!empty($_POST['principal_id'])){
        $principal_id=$_POST['principal_id'];
    }
    $log_array=getChatLogArray($room_id);
    if(!isset($log_array[0][0])){
        echo json_encode(array(
            'replay'=>'リプレイはありません。'
        ));
        exit;
    }
    $last_time=0;
    $replay_text='';
    foreach($log_array as $log){
        $replay_text.=changeLogStringFormLogRecord($log,$principal_id,$last_time,1);
    }
    echo json_encode(array(
        'replay'=>$replay_text
    ));
    exit;