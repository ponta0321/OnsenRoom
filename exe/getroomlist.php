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
    require(DIR_ROOT.'s/common/function.php');
    $room_list=new classRoomList;
    if($room_list->load()){
        $room_info['domain']=THIS_DOMAIN;
        $room_info['stime']=time();
        $room_info['load_avg']=checkLoadAvg(CPU_CORE);
        checkPlayerCapacity($num_of_player);
        $room_info['num_of_player']=$num_of_player;
        $room_info['player_capacity']=PLAYER_CAPACITY;
        $room_info['room_list']=$room_list->room;
        echo json_encode($room_info);
    }else{
        echo json_encode(array(
            'error'=>'failed_to_load_room_info',
            'error_description'=>'ルームリスト情報の読み込みに失敗しました。'
        ));
    }
    exit;