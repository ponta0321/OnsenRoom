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
    // キャラクターをロードする
    $c_character=new classCharacter;
    if(!$c_character->load($room_id,$char_id,$principal_id)){
        echo json_encode(array(
            'error'=>'not_exist_char_info',
            'error_description'=>'キャラクターの読み込みに失敗またはキャラクター情報がありません。'
        ));
        exit;
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
                     array('macro','macro'));
    $json_base_data=array();
    foreach($tag_array as $value){
        $json_base_data[$value[0]]=$c_character->{$value[1]};
    }
    if(0>=count($json_base_data)){
        echo json_encode(array(
            'error'=>'not_exist_char_info',
            'error_description'=>'キャラクター情報はありません。'
        ));
        exit;
    }
    echo json_encode($json_base_data);