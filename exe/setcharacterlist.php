<?php
    require('../s/common/core.php');
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
            echo 'ERR=ルームが見つかりません。';
            exit;
        }
    }else{
        echo 'ERR=ルームIDが指定されていません。';
        exit;
    }
    $flag=0; // 0=削除 、1=追加
    if(!empty($_POST['flag'])){
        $flag=$_POST['flag'];
    }
    $principal_id='';
    if(!empty($_POST['principal_id'])){
        $principal_id=$_POST['principal_id'];
    }else{
        echo 'ERR=プレイヤーが指定されていません。';
        exit;
    }
    $nick_name='';
    if(!empty($_POST['nick_name'])){
        $nick_name=$_POST['nick_name'];
    }else{
        $nick_name=$principal_id;
    }
    $chat_color='#000000';
    if(!empty($_POST['chat_color'])){
        $chat_color=$_POST['chat_color'];
    }
    $char_id='';
    if(!empty($_POST['char_id'])){
        $char_id=$_POST['char_id'];
    }else{
        echo 'ERR=キャラクターIDが指定されていません。';
        exit;
    }
    $char_image='';
    if(!empty($_POST['char_image'])){
        $char_image=$_POST['char_image'];
    }
    $char_name='';
    if(!empty($_POST['char_name'])){
        $char_name=replaceObWord($_POST['char_name']);
    }else{
        echo 'ERR=キャラクター名が指定されていません。';
        exit;
    }
    $char_hp='';
    if(!empty($_POST['char_hp'])){
        $char_hp=replaceObWord($_POST['char_hp']);
    }
    $char_mhp='';
    if(!empty($_POST['char_mhp'])){
        $char_mhp=replaceObWord($_POST['char_mhp']);
    }
    $char_mp='';
    if(!empty($_POST['char_mp'])){
        $char_mp=replaceObWord($_POST['char_mp']);
    }
    $char_mmp='';
    if(!empty($_POST['char_mmp'])){
        $char_mmp=replaceObWord($_POST['char_mmp']);
    }
    $outer_url='';
    if(!empty($_POST['outer_url'])){
        $outer_url=replaceObWord($_POST['outer_url']);
    }
    $char_memo='';
    if(!empty($_POST['char_memo'])){
        $char_memo=replaceObWord($_POST['char_memo']);
    }
    $comment='';
    $exfilelock=new classFileLock($room_dir,$room_id.'_lockfile',5);
    if($exfilelock->flock($room_dir)){
        // ルームファイルの読み込み
        if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
            $exfilelock->unflock($room_dir);
            echo 'ERR=ルーム情報の読み込みに失敗しました。';
            exit;
        }
        $check_character_existence=false;
        if(isset($room_xml->body->character)){
            $count_character_in_room=count($room_xml->body->character);
            for($i=0;$i<$count_character_in_room;$i++){
                if((string)$room_xml->body->character[$i]->char_id==$char_id){
                    $check_character_existence=true;
                    break;
                }
            }
        }
        if($check_character_existence==true){
            if($flag==0){ // リストから削除
                $comment='キャラクターリストから'.$char_name.'が削除されました。';
                unset($room_xml->body->character[$i]);
            }
        }elseif($check_character_existence==false){
            if($flag==1){ // リストに追加
                $comment='キャラクターリストに'.$char_name.'が追加されました。';
                // characterノードの追加
                $nodeCharacter=$room_xml->body->addChild('character');
                $nodeCharacter->addChild('char_owner',$principal_id);
                $nodeCharacter->addChild('char_id',$char_id);
                $nodeCharacter->addChild('char_name',$char_name);
                $nodeCharacter->addChild('char_hp',$char_hp);
                $nodeCharacter->addChild('char_mhp',$char_mhp);
                $nodeCharacter->addChild('char_mp',$char_mp);
                $nodeCharacter->addChild('char_mmp',$char_mmp);
                $nodeCharacter->addChild('char_outer_url',$outer_url);
                $nodeCharacter->addChild('char_memo',$char_memo);
                // 画像処理
                $base_image_name=preg_replace('^(https?:|)'.URL_ROOT.'^',DIR_ROOT,$char_image);
                $copy_image_name='char_'.$principal_id.'_'.basename($base_image_name);
                $nodeCharacter->addChild('char_image',URL_ROOT.'r/n/'.$room_id.'/'.$copy_image_name);
                if(!file_exists($room_dir.$copy_image_name)){
                    if(!copy($base_image_name,$room_dir.$copy_image_name)){
                        $nodeCharacter->char_image='';
                    }
                }
            }
        }
        if($comment!=''){
            $nodeContent=$room_xml->body->addChild('content');
            $nodeContent->addAttribute('id',creatChatMsgId());
            $say_time=time();
            $nodeContent->addChild('date',$say_time);
            $nodeContent->addChild('text',htmlentities($comment,ENT_XML1));
            $nodeContent->addChild('chat_color',$chat_color);
            $nodeContent->addChild('ctyp',1);
            $say_man='システム';
            $nodeContent->addChild('author',htmlentities($say_man,ENT_XML1));
        }
        // ルームの保存
        if(saveRoomXmlFile($room_xml,$room_file,$principal_id,$nick_name,0)){
            // phpログ保存
            @include(DIR_ROOT.'s/common/exewritelog.php');
        }else{
            echo 'ERR=情報を更新できませんでした。';
        }
    }else{
        echo 'ERR=アクセスが集中したため送信に失敗しました。';
    }
    $exfilelock->unflock($room_dir);