<?php 
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT');
    session_start();
    require('./s/common/core.php');
    require(DIR_ROOT.'s/common/function.php');
    require(DIR_ROOT.'s/common/exefunction.php');
    
    $global_page_title='ルームから出ました｜'.SITE_TITLE;
    $local_page_url='exit.php';
    $global_page_url=URL_ROOT.$local_page_url;
    
    $lobby_url=LOBBY_URL_ROOT;
    if(isset($_SESSION['lobby_url'])){
        $lobby_url=$_SESSION['lobby_url'];
        unset($_SESSION['lobby_url']);
    }
    $room_name='';
    if(isset($_SESSION['room_name'])){
        $room_name=basename($_SESSION['room_name']);
        unset($_SESSION['room_name']);
    }
    if(isset($_SESSION['room_pass'])){
        unset($_SESSION['room_pass']);
    }
    $principal_id='';
    if(isset($_SESSION['principal_id'])){
        $principal_id=$_SESSION['principal_id'];
        unset($_SESSION['principal_id']);
    }
    $nick_name=$principal_id;
    if(isset($_SESSION['nick_name'])){
        $nick_name=$_SESSION['nick_name'];
        unset($_SESSION['nick_name']);
    }
    $observer_flag=1;
    if(isset($_SESSION['observer_flag'])){
        $observer_flag=$_SESSION['observer_flag'];
        unset($_SESSION['observer_flag']);
    }
    $room_dir=DIR_ROOT.'r/n/'.$room_name.'/';
    $room_file=$room_dir.'data.xml';
    $room_mirror_file=$room_dir.'data-mirror.xml';
    if(!file_exists($room_file)){
        header('Location: '.$lobby_url.'roomout.php?err=1') ;
        exit;
    }
    $exfilelock=new classFileLock($room_dir,$room_name.'_lockfile',5);
    if($exfilelock->flock($room_dir)){
        if(($xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
            $exfilelock->unflock($room_dir);
            header('Location: '.$lobby_url.'roomout.php?err=8') ;
            exit;
        }
        $playerid_for_count='_guest';
        if(!empty($principal_id)){
            $playerid_for_count=$principal_id;
        }else{
            if($observer_flag!=1){
                $exfilelock->unflock($room_dir);
                header('Location: '.$lobby_url.'roomout.php?err=2') ;
                exit;
            }else{
                $playerid_for_count=getClientIP();
                if(empty($nick_name)){
                    $nick_name='ゲスト';
                }
            }
        }
        if($observer_flag!=1){
            $say_time=time();
            $say_man='システム';
            $contentComment=$xml->body->addChild('content');
            $contentComment->addAttribute('id',creatChatMsgId());
            $contentComment->addChild('date',$say_time);
            $comment=$nick_name.'さんが、退室しました。';
            $contentComment->addChild('text',htmlentities($comment,ENT_XML1));
            $contentComment->addChild('chat_color','#909090');
            $contentComment->addChild('ctyp',1);
            $contentComment->addChild('author',htmlentities($say_man));
        }
        // ルームの保存
        if(saveRoomXmlFile($xml,$room_file,$playerid_for_count,$nick_name,$observer_flag,0)){
            // phpログ保存
            @include(DIR_ROOT.'s/common/exewritelog.php');
        }else{
            $exfilelock->unflock($room_dir);
            header('Location: '.$lobby_url.'roomout.php?err=8') ;
            exit;
        }
        // 人数確認ファイルの保存 add.2016.11.07
        $room_list=new classRoomList;
        $room_list->update($room_name,(string)$xml->head->login_players,(string)$xml->head->game_dicebot);
    }else{
        $exfilelock->unflock($room_dir);
        header('Location: '.$lobby_url.'roomout.php?err=8');
        exit;
    }
    $exfilelock->unflock($room_dir);
    header('Location: '.$lobby_url.'roomout.php?url='.urlencode(URL_ROOT).'&rn='.$room_name.'&pi='.$principal_id.'&of='.$observer_flag);