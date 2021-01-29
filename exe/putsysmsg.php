<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/exefunction.php');
    $result=false;
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
    $system_msg=0;
    if(!empty($_POST['system_msg'])){
        $system_msg=$_POST['system_msg'];
    }
    if($system_msg==1){
        $comment=$nick_name.'さんがDISCORD（ボイスチャット）に接続を開始しました。';
    }else{
        exit;
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
        if(file_exists($room_file)){
            $result=true;
        }
    }
    $exfilelock=new classFileLock($room_dir,$room_id.'_lockfile',5);
    if($exfilelock->flock($room_dir)){
        if($result==true){
            // ルームファイルの読み込み
            if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
                $exfilelock->unflock($room_dir);
                exit;
            }
            $BContent=$room_xml->body->addChild('content');
            $BContent->addAttribute('id',creatChatMsgId());
            $say_time=time();
            $BContent->addChild('date',$say_time);
            $BContent->addChild('text',htmlentities($comment,ENT_XML1));
            $BContent->addChild('chat_color','#909090');
            $BContent->addChild('ctyp',1);
            $BContent->addChild('author','システム');
            // ルームの保存
            if(saveRoomXmlFile($room_xml,$room_file,$principal,$nick_name,0)){
                // phpログ保存
                @include(DIR_ROOT.'s/common/exewritelog.php');
            }else{
                echo 'ERR=情報を更新できませんでした。';
            }
        }
    }else{
        echo 'ERR=アクセスが集中したため送信に失敗しました。';
    }
    $exfilelock->unflock($room_dir);
    exit;