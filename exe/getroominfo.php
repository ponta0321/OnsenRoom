<?php
    require('../s/common/core.php');
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
    require(DIR_ROOT.'s/common/exefunction.php');
    $room_id='';
    $room_dir='';
    $room_file='';
    $room_mirror_file='';
    if(!empty($_POST['room_id'])){
        $room_id=basename($_POST['room_id']);
        $room_dir=DIR_ROOT.'r/n/'.$room_id.'/';
        $room_file=$room_dir.'data.xml';
        $room_mirror_file=$room_dir.'data-mirror.xml';
        if(!file_exists($room_file)){
            echo json_encode(array(
                'error'=>'no_room',
                'error_description'=>'ルームが見つかりません。'
            ));
            exit;
        }
    }else{
        echo json_encode(array(
            'error'=>'no_room_id',
            'error_description'=>'ルームIDが指定されていません。'
        ));
        exit;
    }
    // ルームファイルの読み込み
    if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))!==false){
        $room_info['name']=(string)$room_xml->head->name;
        $room_info['creator']=(string)$room_xml->head->creator;
        $room_info['game_dicebot']=(string)$room_xml->head->game_dicebot;
        $room_info['tour']=(string)$room_xml->head->tour;
        echo json_encode($room_info);
        exit;
    }else{
        echo json_encode(array(
            'error'=>'failed_to_load_room_info',
            'error_description'=>'ルーム情報の読み込みに失敗しました。'
        ));
        exit;
    }