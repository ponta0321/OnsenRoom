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
    require(DIR_ROOT.'s/common/exefunction.php');
    
    $now_time=time();
    $flag=''; // フラグ create_room=ルーム新規作成 update_room=ディスコードギルド登録
    if(!empty($_POST['flag'])){
        $flag=$_POST['flag'];
    }
    $room_name='';
    if(!empty($_POST['room_name'])){
        $room_name=$_POST['room_name'];
    }else{
        echo json_encode(array(
            'error'=>'no_room_name',
            'error_description'=>'ルーム名が見つかりません。'
        ));
        exit;
    }
    $room_pass='';
    if(!empty($_POST['room_pass'])){
        $room_pass=$_POST['room_pass'];
    }
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
            'error'=>'can_not_get_char_info',
            'error_description'=>'あなたはキャラクター情報を取得できません。'
        ));
        exit;
    }
    $expiration_time=0;
    if(!empty($_POST['expiration_time'])){
        $expiration_time=$_POST['expiration_time'];
    }
    $tour_flag=0;
    if(!empty($_POST['tour_flag'])){
        $tour_flag=$_POST['tour_flag'];
    }
    $game_type='g99';
    if(!empty($_POST['game_type'])){
        $game_type=$_POST['game_type'];
    }
    $observe_write=0;
    if(!empty($_POST['observe_write'])){
        $observe_write=$_POST['observe_write'];
    }
    $invite_code=-1;
    if(!empty($_POST['invite_code'])){
        $invite_code=$_POST['invite_code'];
    }
    $discord_room_id=-1;
    if(!empty($_POST['discord_room_id'])){
        $discord_room_id=$_POST['discord_room_id'];
    }
    if($flag==='create_room'){ // ルームの新規作成
        $csr_error_msg='';
        if(($room_list_array=createSessionRoom($room_name,$room_pass,$principal_id,$principal_ip,$expiration_time,$tour_flag,$csr_error_msg,$game_type,$observe_write))!==false){
            $room_info['domain']=THIS_DOMAIN;
			$room_info['stime']=time();
			$room_info['load_avg']=checkLoadAvg(CPU_CORE);
			checkPlayerCapacity($num_of_player);
			$room_info['num_of_player']=$num_of_player;
			$room_info['player_capacity']=PLAYER_CAPACITY;
			$room_info['room_list']=$room_list_array;
			echo json_encode($room_info);
            exit;
        }else{
            if(empty($csr_error_msg)){
                echo json_encode(array(
                    'error'=>'failed_to_create_room',
                    'error_description'=>'ルームの作成に失敗しました。'
                ));
            }else{
                echo json_encode(array(
                    'error'=>'failed_to_create_room',
                    'error_description'=>$csr_error_msg
                ));
            }
            exit;
        }
    }elseif($flag==='update_room'){ // ルーム情報の更新
        $room_id=changeIDformRoomName($room_name);
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
        $exfilelock=new classFileLock($room_dir,$room_id.'_lockfile',5);
        if($exfilelock->flock($room_dir)){
            // ルームファイルの読み込み
            if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
                $exfilelock->unflock($room_dir);
                echo json_encode(array(
                    'error'=>'failed_to_load_room_info',
                    'error_description'=>'ルーム情報の読み込みに失敗しました。'
                ));
                exit;
            }
            complementRoomData($room_xml); // ルームデータ補完
            if($principal_id!=$room_xml->head->creator){
                $exfilelock->unflock($room_dir);
                echo json_encode(array(
                    'error'=>'failed_to_update_room_info',
                    'error_description'=>'ルーム情報の更新はできません。'
                ));
                exit;
            }
            $room_xml->head->password=$room_pass;
            $room_xml->head->tour=$tour_flag;
            $room_xml->head->observe_write=$observe_write;
            $room_xml->head->game_dicebot=$game_type;
            if($invite_code!=-2){
                $room_xml->head->voice_invite_code=$invite_code;
            }
            if($discord_room_id!=-2){
                $room_xml->head->voice_room_id=$discord_room_id;
            }
            // ルームの保存
            if($room_xml->asXML($room_file)){
                // ルームリストの保存
                if(empty($room_pass)){
                    $password_flag=0;
                }else{
                    $password_flag=1;
                }
                $room_list=new classRoomList;
                if($room_list->save(THIS_DOMAIN,
                                   $room_id,
                                   $room_name,
                                   $principal_id,
                                   $principal_ip,
                                   $room_pass,
                                   (string)$room_xml->head->voice_room_id,
                                   $now_time,
                                   $now_time,
                                   ((int)$now_time+(int)$expiration_time),
                                   TRANSFER_PROTOCOL,
                                   $password_flag,
                                   $tour_flag,
                                   0,
                                   0,
                                   $game_type)){
                
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
                        'error'=>'failed_to_seve_room_info',
                        'error_description'=>'ルーム情報の書き込みに失敗しました。(3)'
                    ));
                }
            }else{
                echo json_encode(array(
                    'error'=>'failed_to_seve_room_info',
                    'error_description'=>'ルーム情報の書き込みに失敗しました。(2)'
                ));
            }
        }else{
            echo json_encode(array(
                'error'=>'failed_to_load_room_info',
                'error_description'=>'ルーム情報の読み込みに失敗しました。'
            ));
        }
        $exfilelock->unflock($room_dir);
        exit;
    }else{
        $t='';
        foreach($_POST as $k => $p){
            $t.=$k.':'.$p.'  ';
        }
        echo json_encode(array(
            'error'=>'illegal_flag',
            'error_description'=>'不正な値です。'.$t
        ));
        exit;
    }