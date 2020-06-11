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
    $principal_id='';
    if(!empty($_POST['principal_id'])){
        $principal_id=$_POST['principal_id'];
    }else{
        echo json_encode(array(
            'error'=>'no_specified_id',
            'error_description'=>'プレイヤーが指定されていません。'
        ));
        exit;
    }
    $principal_ip='';
    if(!empty($_POST['principal_ip'])){
        $principal_ip=$_POST['principal_ip'];
    }else{
        echo json_encode(array(
            'error'=>'unable_to_delete_room',
            'error_description'=>'あなたはルームを削除できません。'
        ));
        exit;
    }
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
    // ルーム制作者チェック
    $room_list=new classRoomList;
    if($room_list->load()){
        $creater_flag=false;
        foreach($room_list->room as $room_data){
            if($room_data['creater']==$principal_id){
                $creater_flag=true;
                break;
            }
        }
        if($creater_flag==false){
            echo json_encode(array(
                'error'=>'Not_a_creator',
                'error_description'=>'ルーム制作者でないため、削除できません。'
            ));
            exit;
        }
    }else{
        echo json_encode(array(
            'error'=>'failed_to_load_room_info',
            'error_description'=>'ルーム情報の読み込みに失敗しました。'
        ));
        exit;
    }
    // ルームの削除
    if(deleteTargetRoom($room_id)!==false){
        $send_info['deleted']=(string)$room_id;
        echo json_encode($send_info);
        exit;
    }else{
        echo json_encode(array(
            'error'=>'failed_to_delete_room',
            'error_description'=>'ルームの削除に失敗しました。'
        ));
        exit;
    }