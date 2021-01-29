<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/function.php');
    
    // 初期値
    $request='';
    $stand_no=0;
    
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
    // リクエスト処理
    if(!empty($_POST['request'])){
        $request=$_POST['request'];
    }
    if($request=='face'){
        $request='face';
    }elseif(preg_match('/^d?stand([0-9]+)$/',$request,$match_array)){
        //$request='stand';
        $stand_no=(int)$match_array[1];
    }else{
        echo json_encode(array(
            'error'=>'no_specified_id',
            'error_description'=>'リクエストが不正です。'
        ));
        exit;
    }
    $img_name='表情'.$stand_no;
    if(!empty($_POST['img_name'])){
        $img_name=$_POST['img_name'];
    }
    // キャラクターのロード
    $c_character=new classCharacter;
    if(!$c_character->load($room_id,$char_id,$principal_id)){
        echo json_encode(array(
            'error'=>'not_exist_char_info',
            'error_description'=>'キャラクターの読み込みに失敗またはキャラクター情報がありません。'
        ));
        exit;
    }
    // アップロード画像の存在チャック
    if($unupload_file_flag=empty($_FILES['upload_image']['name'])){
        if($stand_no==0){
            echo json_encode(array(
                'error'=>'not_exist_image',
                'error_description'=>'アップロードする画像が見つかりません。'
            ));
            exit;
        }
    }
    // アップロード画像のファイル名決定
    if(!empty($c_character->owner_id)){
        if($unupload_file_flag===true){
            $image_save_file='';
        }else{
            $image_save_file=$c_character->createFileName();
        }
    }else{
        echo json_encode(array(
            'error'=>'owner_is_unknown',
            'error_description'=>'キャラクターのオーナーが不明です。'
        ));
        exit;
    }
    if($unupload_file_flag!==true){
        if($stand_no==0){ // キャラ絵
            if(!uploadImage($_FILES['upload_image'],$room_dir.$image_save_file,$upload_image_err,UPLOAD_BI_LIMIT_SIZE,200)){
                echo json_encode(array(
                    'error'=>'failed_to_save_image',
                    'error_description'=>$upload_image_err
                ));
                exit;
            }
        }else{ // 立ち絵
            if(!uploadImage($_FILES['upload_image'],$room_dir.$image_save_file,$upload_image_err,UPLOAD_BI_LIMIT_SIZE,480,544)){
                echo json_encode(array(
                    'error'=>'failed_to_save_image',
                    'error_description'=>$upload_image_err
                ));
                exit;
            }
        }
    }
    // キャラクター情報の保存
    if($c_character->setImage($request,$image_save_file,$img_name,$room_dir)){
        if($characterlist_array=$c_character->editCharacterList($room_id,$principal_id)){
            $json_base_data['charlist']=$c_character->convertCharListArray($characterlist_array);
        }
        $json_base_data['set_image']=$image_save_file;
        $json_base_data['stand_image']=$c_character->stand;
        echo json_encode($json_base_data);
        exit;
    }else{
        if(file_exists($room_dir.$image_save_file)){
            @unlink($room_dir.$image_save_file);
        }
    }
    echo json_encode(array(
        'error'=>'failed_to_save_image',
        'error_description'=>'キャラクター情報の保存に失敗しました。'
    ));
    exit;