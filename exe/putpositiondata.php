<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/exefunction.php');
    
    $principal='';
    if(!empty($_POST['principal'])){
        $principal=$_POST['principal'];
    }else{
        exit;
    }
    $nick_name='';
    if(!empty($_POST['nick_name'])){
        $nick_name=$_POST['nick_name'];
    }else{
        $nick_name=$principal;
    }
    $map_data='';
    if(!empty($_POST['map_data'])){
        $map_data=$_POST['map_data'];
    }
    $room_id='';
    $room_dir='';
    $room_file='';
    $room_mirror_file='';
    if(!empty($_POST['xml'])){
        $room_id=basename($_POST['xml']);
        $room_dir=DIR_ROOT.'r/n/'.$room_id.'/';
        $room_file=$room_dir.'data.xml';
        $room_mirror_file=$room_dir.'data-mirror.xml';
        if(!file_exists($room_file)){
            exit;
        }
    }
    $exfilelock=new classFileLock($room_dir,$room_id.'_lockfile',5);
    if($exfilelock->flock($room_dir)){
        // ルームファイルの読み込み
        if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
            $exfilelock->unflock($room_dir);
            exit;
        }
        $room_xml->head->map_data=$map_data;
        // ルームの保存
        if(saveRoomXmlFile($room_xml,$room_file,$principal,$nick_name,0)){
            // phpログ保存
            @include(DIR_ROOT.'s/common/exewritelog.php');
        }else{
            echo 'ERR=情報を更新できませんでした。';
        }
    }else{
        echo 'ERR=アクセスが集中したため送信に失敗しました。';
    }
    $exfilelock->unflock($room_dir);
    exit;