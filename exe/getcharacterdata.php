<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/function.php');
    header("Content-type: application/json; charset=utf-8");
    
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
    $char_id='';
    if(!empty($_POST['char_id'])){
        $char_id=$_POST['char_id'];
    }else{
        echo json_encode(array(
            'error'=>'no_specified_id',
            'error_description'=>'キャラクターが指定されていません。'
        ));
        exit;
    }
    $room_id='';
    if(!empty($_POST['room_id'])){
        $room_id=basename($_POST['room_id']);
        $room_dir=DIR_ROOT.'r/n/'.$room_id.'/';
        $room_file=$room_dir.'data.xml';
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
    $forced_load_flag=0;
    if(!empty($_POST['forced_load'])){
        $forced_load_flag=$_POST['forced_load'];
        if($forced_load_flag==-1){
            if(file_exists(classCharacter::getCharDir($room_id,$principal_id).$char_id.'.xml')){
                exit;
            }
        }
    }
    // 本人確認（IPチェック） → キャラクター情報JSON取得 → キャラクター情報ファイルの保存
    $c_character=new classCharacter;
    if($c_character->saveDataFromLobby($room_id,getCharDataFromLobbyServer($char_id,$principal_id,$principal_ip),$forced_load_flag)){
        // キャラクター画像データの読み込み
        $image_list=array();
        if(!empty($c_character->image)){
            $image_list[]=$c_character->image;
        }
        foreach($c_character->stand as $value){
            if(!empty($value[1])){
                $image_list[]=$value[1];
            }
        }
        foreach($image_list as $value){
            $c_character->getImageFromLobby($value,$room_id,$principal_id);
        }
    }
    // キャラIDがない場合、ﾛｰﾄﾞする
    if(empty($c_character->id)){
        if(!$c_character->load($room_id,$char_id,$principal_id)){
            echo json_encode(array(
                'error'=>'not_exist_char_info',
                'error_description'=>'キャラクターの読み込みに失敗またはキャラクター情報がありません。'
            ));
            exit;
        }
    }
    // JSONデータの作成
    $tag_array=array(array('char_id','id'),
                     array('game_type','game_type'),
                     array('char_name','name'),
                     array('char_image','image'),
                     array('char_hp','hp'),
                     array('char_mhp','mhp'),
                     array('char_mp','mp'),
                     array('char_mmp','mmp'),
                     array('char_memo','condition'),
                     array('detail_a','detail_a'),
                     array('detail_b','detail_b'),
                     array('detail_c','detail_c'),
                     array('macro','macro'),
                     array('designated','designated'),
                     array('outer_url','outer_url'),
                     array('created_in_room','created_in_room'));
    $json_base_data=array();
    foreach($tag_array as $value){
        $json_base_data[$value[0]]=$c_character->{$value[1]};
    }
    $json_base_data['stand_image']=$c_character->stand;
    if(0>=count($json_base_data)){
        echo json_encode(array(
            'error'=>'not_exist_char_info',
            'error_description'=>'キャラクター情報はありません。'
        ));
        exit;
    }
    if(($characterlist_array=$c_character->loadCharacterList($room_id,$principal_id))!==false){
        if(isset($c_character->macro)){
            $characterlist_array[$c_character->id][10]=$c_character->macro;
        }
        if(isset($c_character->stand_image)){
            $characterlist_array[$c_character->id][11]=$c_character->stand_image;
        }
        $c_character->saveCharacterList($room_id,$principal_id,$characterlist_array);
    }
    $json_base_data['charlist']=$c_character->convertCharListArray($characterlist_array);
    echo json_encode($json_base_data);