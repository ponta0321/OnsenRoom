<?php 
/*===============================
 ルーム内で一度呼び出したら何度も使用しない関数
 もしくは利用頻度が低い、あるいは処理の重いもの
===============================*/
function removeDirectory($dir){
/*-------------------------------------------------------------------
-- 機能 --
ファイルを内包したディレクトリをまるごと削除

-- バージョン --
v1.00

-- 引数 --
$dir    :削除対象のディレクトリ

-- 更新履歴 --
2016.05.25 制作
-------------------------------------------------------------------*/
    $result=false;
    if((is_dir($dir))&&(!is_link($dir))){
        array_map('removeDirectory',glob($dir.'/*', GLOB_ONLYDIR));
        array_map('unlink',glob($dir.'/*'));
        $result=rmdir($dir);
    }
    return $result;
}
function uploadImage($image_file,$dir_save,&$err_msg,$limit_size=-1,$max_size_w=0,$max_size_h=0){
/*-------------------------------------------------------------------
-- 機能 --
画像をアップロードする。

-- バージョン --
v1.01

-- 引数 --
$image_file :$_POSTを受け取る
$dir_save   :保存先
$err_msg    :エラーメッセージ
$limit_size :アップロードファイルの最大サイズ
$max_size　　　:0=画像加工しない 1~=画像の縦横最大値で加工
-- 更新履歴 --
2016.05.10 制作
2017.03.07 更新 画像加工処理の追加
-------------------------------------------------------------------*/
    //header('Content-Type: text/plain; charset=utf-8');
    if(!isset($image_file['error'])){
        $err_msg='パラメータが不正です';
        return false;
    }elseif(!is_int($image_file['error'])){
        $err_msg='パラメータが不正です.';
        return false;
    }
    switch($image_file['error']){
        case UPLOAD_ERR_OK: // OK
            break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
            $err_msg='ファイルが選択されていません';
            return false;
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過 (設定した場合のみ)
            $err_msg='ファイルサイズが大きすぎます';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        default:
            $err_msg='その他のエラーが発生しました';
            return false;
    }
    // ここで定義するサイズ上限のオーバーチェック
    if($limit_size>0){
        if($image_file['size']>$limit_size){
            $err_msg='ファイルサイズが大きすぎます';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        }
    }
    // ファイルタイプチェック
    $mime_content_type_flag=0; // -1=不正 0=未チェック 1=合格 
    if(function_exists('mime_content_type')){
        $file_mime_content_type=mime_content_type($image_file['tmp_name']);
        if($file_mime_content_type!=='application/octet-stream'){
            if(preg_match('/image/i',mime_content_type($image_file['tmp_name']))){
                $mime_content_type_flag=1;
            }else{
                $mime_content_type_flag=-1;
            }
        }
    }
    if($mime_content_type_flag===0){
        if(preg_match('/image/i',$image_file['type'])){
            $mime_content_type_flag=1;
        }else{
            $mime_content_type_flag=-1;
        }
    }
    if($mime_content_type_flag===-1){
        $err_msg='ファイル形式が不正です';
        return false;
    }
    if(!is_dir(dirname($dir_save))){
        @mkdir(dirname($dir_save),0755,true);
    }
    if(file_exists($dir_save)){
        @unlink($dir_save);
    }
    if($max_size_w==0){
        if(!move_uploaded_file($image_file['tmp_name'],$dir_save)){
            $err_msg='ファイル保存時にエラーが発生しました';
            return false;
        }
    }else{
        if($max_size_h==0){
            $max_size_h=$max_size_w;
        }
        if(!trimImage($image_file['tmp_name'],$dir_save,$max_size_w,$max_size_h)){
            $err_msg='ファイル保存時にエラーが発生しました';
            return false;
        }
        @unlink($image_file['tmp_name']);
    }
    chmod($dir_save,0755);
    $err_msg='';
    return true;
}
function uploadMusic($music_file,$dir_save,&$err_msg,$limit_size=-1){
/*-------------------------------------------------------------------
-- 機能 --
音楽をアップロードする。

-- バージョン --
v1.00

-- 引数 --
$music_file :$_POSTを受け取る
$dir_save   :保存先
$err_msg    :エラーメッセージ
$limit_size :アップロードファイルの最大サイズ
-- 更新履歴 --
2016.05.10 制作
-------------------------------------------------------------------*/
    //header('Content-Type: text/plain; charset=utf-8');
    if(!isset($music_file['error'])){
        $err_msg='パラメータが不正です';
        return false;
    }elseif(!is_int($music_file['error'])){
        $err_msg='パラメータが不正です.';
        return false;
    }
    switch($music_file['error']){
        case UPLOAD_ERR_OK: // OK
            break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
            $err_msg='ファイルが選択されていません';
            return false;
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
            $err_msg='ファイルサイズが大きすぎます。';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過 (設定した場合のみ)
            $err_msg='ファイルサイズが大きすぎます。';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        default:
            $err_msg='その他のエラーが発生しました';
            return false;
    }
    // ここで定義するサイズ上限のオーバーチェック
    if($limit_size>0){
        if($music_file['size']>$limit_size){
            $err_msg='ファイルサイズが大きすぎます';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        }
    }
    // ファイルタイプチェック
    $mime_content_type_flag=0; // -1=不正 0=未チェック 1=合格 
    if(function_exists('mime_content_type')){
        $file_mime_content_type=mime_content_type($music_file['tmp_name']);
        if($file_mime_content_type!=='application/octet-stream'){
            if(preg_match('/audio/i',mime_content_type($music_file['tmp_name']))){
                $mime_content_type_flag=1;
            }else{
                $mime_content_type_flag=-1;
            }
        }
    }
    if($mime_content_type_flag===0){
        if(preg_match('/audio/i',$music_file['type'])){
            $mime_content_type_flag=1;
        }else{
            $mime_content_type_flag=-1;
        }
    }
    if($mime_content_type_flag===-1){
        $err_msg='ファイル形式が不正です';
        return false;
    }
    if(!is_dir(dirname($dir_save))){
        @mkdir(dirname($dir_save),0755,true);
    }
    if(file_exists($dir_save)){
        @unlink($dir_save);
    }
    if(!move_uploaded_file($music_file['tmp_name'],$dir_save)){
        $err_msg='ファイル保存時にエラーが発生しました';
        return false;
    }
    chmod($dir_save,0755);
    $err_msg='';
    return true;
}
function uploadXmlData($cardset_file,$dir_save,&$err_msg,$limit_size=-1){
/*-------------------------------------------------------------------
-- 機能 --
カードセット・ボード情報などのXMLデータ(xml)をアップロードする。

-- バージョン --
v1.00

-- 引数 --
$cardset_file :$_POSTを受け取る
$dir_save   :保存先
$err_msg    :エラーメッセージ
$limit_size :アップロードファイルの最大サイズ
-- 更新履歴 --
2016.12.14 制作
-------------------------------------------------------------------*/
    //header('Content-Type: text/plain; charset=utf-8');
    if(!isset($cardset_file['error'])){
        $err_msg='パラメータが不正です';
        return false;
    }elseif(!is_int($cardset_file['error'])){
        $err_msg='パラメータが不正です.';
        return false;
    }
    switch($cardset_file['error']){
        case UPLOAD_ERR_OK: // OK
            break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
            $err_msg='ファイルが選択されていません';
            return false;
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
            $err_msg='ファイルサイズが大きすぎます。';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過 (設定した場合のみ)
            $err_msg='ファイルサイズが大きすぎます。';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        default:
            $err_msg='その他のエラーが発生しました';
            return false;
    }
    // ここで定義するサイズ上限のオーバーチェック
    if($limit_size>0){
        if($cardset_file['size']>$limit_size){
            $err_msg='ファイルサイズが大きすぎます';
            if($limit_size!=-1){
                $err_msg.="\n".'（サイズ上限は'.$limit_size.'Byteまでです。）';
            }
            return false;
        }
    }
    // ファイルタイプチェック
    if(function_exists('mime_content_type')){
        if(!$ext=array_search(
            mime_content_type($cardset_file['tmp_name']),
            array(
                'txt_xml' => 'text/xml',
                'app_xml' => 'application/xml'
            ),
            true
        )){
            $err_msg='ファイル形式が不正です';
            return false;
        }
    }
    if(!is_dir(dirname($dir_save))){
        @mkdir(dirname($dir_save),0755,true);
    }
    if(file_exists($dir_save)){
        @unlink($dir_save);
    }
    if(!move_uploaded_file($cardset_file['tmp_name'],$dir_save)){
        $err_msg='ファイル保存時にエラーが発生しました';
        return false;
    }
    chmod($dir_save,0755);
    $err_msg='';
    return true;
}
function getCharacterImage($image,$url_root=URL_ROOT,$dir_root=DIR_ROOT){
/*-------------------------------------------------------------------
-- 機能 --
画像がない場合ノーイメージ画像URLを返す

-- バージョン --
v1.01

-- 更新履歴 --
2016.05.24 制作
2017.12.20 更新
-------------------------------------------------------------------*/
    $out_image=$url_root.'images/no_image256.jpg';
    if($image==''){
    }elseif(file_exists($dir_root.$image)){
        $out_image=$url_root.$image;
    }
    return $out_image;
}
function deleteOldRoom($dir_root=DIR_ROOT){
/*-------------------------------------------------------------------
-- 機能 --
有効期限の過ぎたルーム削除する

-- バージョン --
v1.02

-- 更新履歴 --
2016.05.04 制作
2016.05.17 更新 
2018.02.17 更新
-------------------------------------------------------------------*/
    $room_list=new classRoomList;
    if($room_list->load()){
        foreach($room_list->room as $room_data){
            if($room_list->stime>(int)$room_data['expiration_time']){
                removeDirectory($dir_root.'r/n/'.$room_data['id'].'/');
            }
        }
        if($room_list->deleteAllOld()){
            return true;
        }
    }
    return false;
}
function deleteTargetRoom($target_room,$dir_root=DIR_ROOT){
/*-------------------------------------------------------------------
-- 機能 --
対象のルームを削除する

-- バージョン --
v1.01

-- 更新履歴 --
2016.07.04 制作 
2017.12.12 更新
-------------------------------------------------------------------*/
    if(empty($target_room)){
        return false;
    }
    removeDirectory($dir_root.'r/n/'.$target_room.'/');
    $room_list=new classRoomList;
    if($room_list->delete(THIS_DOMAIN,$target_room)){
        return true;
    }else{
        return false;
    }
}
function trimImage($origin_image,$destinatin,$max_size_w=320,$max_size_h=320){
/*-------------------------------------------------------------------
-- 機能 --
画像をサイズと圧縮率、形式を加工し保存する。

-- バージョン --
v1.01

-- 更新履歴 --
2017.03.07 制作
2017.04.17 更新 pngのみ、加工後もpng形式。リサイズの必要のないものはそのまま、リネームのみ
-------------------------------------------------------------------*/
    $result=false;
    $image_info=false;
        
    if(file_exists($origin_image)){
        $image_info=getimagesize($origin_image);
    }
    if($image_info){
        $diameter=1; //縮小率
        $px=0;
        $py=0;
        $ow=$image_info[0]; //オリジナル幅
        $oh=$image_info[1]; //オリジナル高さ
        $rw=$image_info[0]; //完成時の幅
        $rh=$image_info[1]; //完成時の高さ
        // max_size_wと$max_size_hが同じ場合はトリミング
        if($max_size_w==$max_size_h){
            $max_size=$max_size_w;
            if(($ow>$max_size)&&($oh>$max_size)){ // 幅＆高さ共に大きい
                if($ow>=$oh){
                    $diameter=$max_size/$oh;
                    $px=(($diameter*$ow)-$max_size)/2;
                    $rw=$max_size;
                    $rh=$max_size;
                }else{
                    $diameter=$max_size/$ow;
                    $py=(($diameter*$oh)-$max_size)/2;
                    $rw=$max_size;
                    $rh=$max_size;
                }
            }elseif($ow>$max_size){ // 幅が大きい
                $px=($ow-$max_size)/2;
                $rw=$max_size;
            }elseif($oh>$max_size){ // 高さが大きい
                $py=($oh-$max_size)/2;
                $rh=$max_size;
            }
        // max_size_wと$max_size_hが同じ場合はリサイズのみ
        }else{
            if($oh>$max_size_h){ // 高さが大きい
                $diameter=$max_size_h/$oh;
                $rw=$diameter*$ow;
                $rh=$diameter*$oh;
            }
            if($rw>$max_size_w){ // 幅が大きい
                $diameter=$max_size_w/$rw;
                $rw=$diameter*$rw;
                $rh=$diameter*$rh;
            }
        }
        if(($ow>$rw)||($oh>$rh)||($image_info[2]==IMAGETYPE_GIF)||($image_info[2]==IMAGETYPE_WBMP)){
            $image_trim=ImageCreateTrueColor($rw,$rh);
            $png_flag=false;
            $image_data=false;
            if($image_info[2]==IMAGETYPE_GIF){
                $image_data=ImageCreateFromGIF($origin_image);
                $png_flag=true;
            }
            elseif($image_info[2]==IMAGETYPE_JPEG){
                $image_data=ImageCreateFromJPEG($origin_image);
            }
            elseif($image_info[2]==IMAGETYPE_PNG){
                $image_data=ImageCreateFromPNG($origin_image);
                $png_flag=true;
            }
            elseif($image_info[2]==IMAGETYPE_WBMP){
                $image_data=ImageCreateFromWBMP($origin_image);
            }
            if($image_data){
                if($png_flag==false){
                    imagefill($image_trim,0,0,imagecolorallocate($image_trim,255,255,255));
                }else{
                    imagealphablending($image_trim,false);
                    imagesavealpha($image_trim,true);
                }
                if(imagecopyresampled($image_trim,$image_data,0,0,$px,$py,floor($ow*$diameter),floor($oh*$diameter),$ow,$oh)){
                    if($png_flag==false){
                        if(imagejpeg($image_trim,$destinatin,75)){
                            $result=true;
                        }
                    }else{
                        if(imagepng($image_trim,$destinatin,6)){
                            $result=true;
                        }
                    }
                }
                imagedestroy($image_data);
                imagedestroy($image_trim);
            }else{
                imagedestroy($image_trim);
                return false;
            }
        }else{
            if(is_uploaded_file($origin_image)){
                if(move_uploaded_file($origin_image,$destinatin)){
                    $result=true;
                }
            }else{
                if(rename($origin_image,$destinatin)){
                    $result=true;
                }
            }
        }
    }else{
        return false;
    }
    return $result;
}
function getCharListFromLobbyServer($player_id,$player_ip,$room_token='',$lobby_getcharacterlist_url=LOBBY_URL_ROOT.'exe/getcharacterlist.php'){
/*-------------------------------------------------------------------
-- 機能 --
キャラクター一覧情報をSNSサーバーから取得する。

-- バージョン --
v1.00

-- 更新履歴 --
2017.09.05 制作
-------------------------------------------------------------------*/
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$lobby_getcharacterlist_url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $post_fields ='id='.$player_id;
    $post_fields.='&ip='.$player_ip;
    $post_fields.='&rt='.$room_token;
    curl_setopt($ch,CURLOPT_POST,3);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    $result=curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}
function getCharDataFromLobbyServer($char_id,$player_id,$player_ip,$room_token='',$lobby_getcharacterdata_url=LOBBY_URL_ROOT.'exe/getcharacterdata.php'){
/*-------------------------------------------------------------------
-- 機能 --
キャラクター情報をSNSサーバーから取得する。

-- バージョン --
v1.00

-- 更新履歴 --
2017.09.07 制作
-------------------------------------------------------------------*/
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$lobby_getcharacterdata_url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $post_fields ='id='.$player_id;
    $post_fields.='&ip='.$player_ip;
    $post_fields.='&char_id='.$char_id;
    $post_fields.='&rt='.$room_token;
    curl_setopt($ch,CURLOPT_POST,4);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    $result=curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}
function setOptionCallName($principal_id,$nick_name,$characterlist_array,$flag){
/*-------------------------------------------------------------------
-- 機能 --
ルームのチャットウィンドウにある呼び名selectのoption文字列を作成し返す。

$flag 0=ID 1=呼び名

-- バージョン --
v1.00

-- 更新履歴 --
2017.09.13 制作
-------------------------------------------------------------------*/
    $html_v='';
    if(($nick_name!=$principal_id)&&($nick_name!='')){
        $html_v.='<option value="'.($flag==0?'nick_name':$nick_name).'" selected="selected">'.$nick_name.'</option>';
        $html_v.='<option value="'.($flag==0?'gm_nick_name':'GM／'.$nick_name).'">GM／'.$nick_name.'</option>';
    }else{
        $html_v.='<option value="'.($flag==0?'principal_id':$principal_id).'" selected="selected">'.$principal_id.'</option>';
        $html_v.='<option value="'.($flag==0?'gm_principal_id':'GM／'.$principal_id).'">GM／'.$principal_id.'</option>';
    }
    foreach($characterlist_array as $value){
        $html_v.='<option value="'.$value[($flag==0?0:2)].'">'.$value[2].'</option>';
    }
    return $html_v;
}
function getTrpgSystemInfoFromLobbyServer($last_time_gtn,&$global_trpg_name,$lobby_gettrpgsyslist_url=LOBBY_URL_ROOT.'exe/gettrpgsyslist.php'){
/*-------------------------------------------------------------------
-- 機能 --
1.TRPGシステムリスト及びキャラシテンプレリストをSNSサーバーから取得する。
2.所持するTRPGシステムリストが最新でない場合、最新版に更新し、$global_trpg_nameも更新して返す。
3.所持するキャラシテンプレリストが最新でない場合、最新版に更新し、$template_charaも更新して返す。

-- バージョン --
v1.00

-- 更新履歴 --
2017.09.28 制作
-------------------------------------------------------------------*/
	$last_update_template_chara=0;
	$template_chara=array();
	$file=DIR_ROOT.'s/list/templatechar.php';
	if($file) @include($file);
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$lobby_gettrpgsyslist_url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $post_fields ='lt_gtn='.$last_time_gtn;
    $post_fields.='&lt_tc='.$last_update_template_chara;
    curl_setopt($ch,CURLOPT_POST,2);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    $result=curl_exec($ch);
    curl_close($ch);
    $result_json_array=json_decode($result,true);
    if(isset($result_json_array['latest'])){
        return true;
    }else{
        $update_flag=false;
        if(isset($result_json_array['global_trpg_name'])&&
           isset($result_json_array['lt_gtn'])){
           
            $html_v='<?php '."\n";
            foreach($result_json_array['global_trpg_name'] as $key => $value){
                $html_v.='$global_trpg_name[\''.$key.'\']=\''.escapePHP($value,"'").'\';'."\n";
            }
            $html_v.='$last_update_global_trpg_name=\''.escapePHP($result_json_array['lt_gtn'],"'").'\';'."\n";
            if(repeat_put_file(DIR_ROOT.'s/list/trpg_sys_list.php',$html_v,LOCK_EX)!==false){
                $global_trpg_name=$result_json_array['global_trpg_name'];
                $update_flag=true;
            }
        }
        if(isset($result_json_array['template_chara'])&&
           isset($result_json_array['lt_tc'])){
               
            $html_v='<?php '."\n";
            foreach($result_json_array['template_chara'] as $key => $value){
                foreach($value as $c_key => $c_value){
                    $html_v.='$template_chara[\''.$key.'\'][\''.$c_key.'\']=\''.escapePHP($c_value,"'").'\';'."\n";
                }
            }
            $html_v.='$last_update_template_chara=\''.escapePHP($result_json_array['lt_gtn'],"'").'\';'."\n";
            if(repeat_put_file(DIR_ROOT.'s/list/templatechar.php',$html_v,LOCK_EX)!==false){
                $template_chara=$result_json_array['template_chara'];
                $update_flag=true;
            }
        }
    }
    if($update_flag==true){
        return true;
    }
    return false;
}
function load2ColumsCsvFile($load_file,$file_formats,$nothing_array_flag=true,$url_root=URL_ROOT){
/*-------------------------------------------------------------------
-- 機能 --
CSV形式で2カラムのファイルを読み込んで返す
カラム[0]=ファイル $file_formatと同じかチェックを行う
カラム[1]=名称

返す配列の最初は [0]=空 [1]=なし を含む

-- バージョン --
v1.01

-- 更新履歴 --
17.09.30
20.11.30
-------------------------------------------------------------------*/
    if($nothing_array_flag==true){
        $list_array=array(array('','なし'));
    }else{
        $list_array=array();
    }
    if(!file_exists($load_file)){
        return $list_array;
    }
    if(is_array($file_formats)){
        $file_format_needle='';
        foreach($file_formats as $value){
            if(!empty($file_format_needle)){
                $file_format_needle.='|';
            }
            $file_format_needle.='\.'.preg_quote(ltrim($value,'.'),'/');
        }
        $file_format_needle='/'.$file_format_needle.'/';
    }elseif(!empty($file_formats)){
        $file_format_needle='/\.'.preg_quote(ltrim($file_formats,'.'),'/').'/';
    }else{
        return $list_array;
    }
    $loaded_data=file_get_contents($load_file);
    if($loaded_data!==false){
        $loaded_data=str_replace(array("\r\n","\r"),"\n",$loaded_data);
        $records=explode("\n",$loaded_data);
        foreach((array)$records as $record_value){
			if(!preg_match('/^(\/\/|#)/',$record_value)){
				$columns=explode(',',$record_value);
				if((preg_match($file_format_needle,$columns[0]))&&
				   (!empty($columns[1]))){
					if(!preg_match('/(^\/\/|^https?:\/\/)/',$columns[0])){
						$columns[0]=$url_root.ltrim($columns[0],'/');
					}
					$list_array[]=array($columns[0],$columns[1]);
				}
			}
        }
    }
    return $list_array;
}
function loadChessmanFile($load_file=DIR_ROOT.'s/list/chessman_list.txt',$file_formats=array('gif','jpg','jpeg','png','bmp'),$url_root=URL_ROOT){
/*-------------------------------------------------------------------
-- 機能 --
コマリストファイルを読み込んでコマリストを返す

-- バージョン --
v1.00

-- 更新履歴 --
2017.09.30 制作
-------------------------------------------------------------------*/
    $list_array=array();
    if(!file_exists($load_file)){
        return $list_array;
    }
    if(is_array($file_formats)){
        $file_format_needle='';
        foreach($file_formats as $value){
            if(!empty($file_format_needle)){
                $file_format_needle.='|';
            }
            $file_format_needle.='\.'.preg_quote(ltrim($value,'.'),'/');
        }
        $file_format_needle='/'.$file_format_needle.'/';
    }elseif(!empty($file_formats)){
        $file_format_needle='/\.'.preg_quote(ltrim($file_formats,'.'),'/').'/';
    }else{
        return $list_array;
    }
    $loaded_data=file_get_contents($load_file);
    if($loaded_data!==false){
        $loaded_data=str_replace(array("\r\n","\r"),"\n",$loaded_data);
        $records=explode("\n",$loaded_data);
        foreach((array)$records as $record_value){
            $columns=explode(',',$record_value);
            if((preg_match($file_format_needle,$columns[0]))&&
               (!empty($columns[1]))&&
               (!empty($columns[2]))&&
               (!empty($columns[3]))&&
               (!empty($columns[4]))){
                   
                if(!preg_match('/(^\/\/|^https?:\/\/)/',$columns[0])){
                    $columns[0]=$url_root.ltrim($columns[0],'/');
                }
                if(empty($columns[5])){
                    $columns[5]=0;
                }
                if(empty($columns[6])){
                    $columns[6]=0;
                }
                if(empty($columns[7])){
                    $columns[7]='';
                }
                if(empty($columns[8])){
                    $columns[8]='';
                }
                $list_array[$columns[2]][]=$columns;
            }
        }
    }
    return $list_array;
}
function getSSFromLobbyServer($player_id,$player_ip,$ss_no,$lobby_getss_url=LOBBY_URL_ROOT.'exe/getss.php'){
/*-------------------------------------------------------------------
-- 機能 --
指定のシナリオセット情報をSNSサーバーから取得する。

-- バージョン --
v1.00

-- 更新履歴 --
2018.02.28 制作
-------------------------------------------------------------------*/
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$lobby_getss_url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $post_fields ='id='.$player_id;
    $post_fields.='&ip='.$player_ip;
    $post_fields.='&ss_no='.$ss_no;
    curl_setopt($ch,CURLOPT_POST,3);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    $result=curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}
function getSSListFromLobbyServer($player_id,$player_ip,$lobby_getcharacterlist_url=LOBBY_URL_ROOT.'exe/getsslist.php'){
/*-------------------------------------------------------------------
-- 機能 --
シナリオセット一覧情報をSNSサーバーから取得する。

-- バージョン --
v1.00

-- 更新履歴 --
2017.10.12 制作
-------------------------------------------------------------------*/
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$lobby_getcharacterlist_url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $post_fields ='id='.$player_id;
    $post_fields.='&ip='.$player_ip;
    curl_setopt($ch,CURLOPT_POST,2);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    $result=curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}
function makeSSlistArrayFromJson($ss_list_from_lobby_server){
/*-------------------------------------------------------------------
-- 機能 --
ロビーサーバーから取得したシナリオセット一覧情報をシナリオセット一覧配列に変換して返す。

-- バージョン --
v1.00

-- 更新履歴 --
2017.10.12 制作
-------------------------------------------------------------------*/
    $sslist_array=array();
    if(isset($ss_list_from_lobby_server['error'])){
        return $sslist_array;
    }elseif(!isset($ss_list_from_lobby_server['sslist'])){
        return $sslist_array;
    }
    // 0   1   2
    // sno,gtp,ttl
    // sno  ID
    // gtp  システム名
    // ttl  タイトル
    foreach($ss_list_from_lobby_server['sslist'] as $value){
        if(isset($value['sno'])){
            $sslist_array[$value['sno']][0]=$value['sno'];
            $sslist_array[$value['sno']][1]='';
            if(!empty($value['gtp'])){
                $sslist_array[$value['sno']][1]=$value['gtp'];
            }
            $sslist_array[$value['sno']][2]='タイトル不明';
            if(!empty($value['ttl'])){
                $sslist_array[$value['sno']][2]=$value['ttl'];
            }
        }
    }
    return $sslist_array;
}
function displaySimpleByte($byte_size,$power=array('B','KB','MB','GB','TB'),$decimal=0){
/*-------------------------------------------------------------------
-- 機能 --
バイト数を単位付きで簡易表示する

-- バージョン --
v1.00

-- 更新履歴 --
2018.01.31 制作
-------------------------------------------------------------------*/
    if(couting($power)==0){
        $power=array('B');
    }
    for($i=(couting($power)-1);$i>=0;$i--){
        if($byte_size>=pow(1024,$i)){
            break;
        }
    }
    return number_format($byte_size/pow(1024,$i),$decimal,'.',',').$power[$i];
}
function checkPlayerCapacity(&$players,$player_alive_time=PLAYER_ALIVE_TIME,$player_capacity=PLAYER_CAPACITY){
/*-------------------------------------------------------------------
-- 機能 --
サーバーの収容人数をチェックし、入場できる場合はtrueを返す
$playersには現在の収容人数を返す

-- バージョン --
v1.00

-- 更新履歴 --
2018.02.17 制作
-------------------------------------------------------------------*/
    $players=0;
    $room_list=new classRoomList;
    $room_list->load();
    foreach($room_list->room as $room_data){
        if(($room_list->stime-(int)$room_data['update_time'])<=$player_alive_time){
            $players=$players+(int)$room_data['parties']+(int)$room_data['visitors'];
        }
    }
    if($player_capacity>$players){
        return true;
    }
    return false;
}
function checkLoadAvg($core_num){
/*-------------------------------------------------------------------
-- 機能 --
システムの平均負荷(%)を取得する

-- バージョン --
v1.00

-- 更新履歴 --
2018.02.17 制作
-------------------------------------------------------------------*/
    $load_avg=array(0,0,0);
    try{
        if((!is_numeric($core_num))||(empty($core_num))){
            $send_command='grep "processor" /proc/cpuinfo | wc -l';
            $core_num=`$send_command`;
            if(empty($core_num)){
                $core_num=2;
            }
        }
        $core_num=(int)$core_num;
        if(empty($core_num)){
            $core_num=1;
        }
        $sys_load_array=sys_getloadavg();
        for($i=0;$i<3;$i++){
            $load_avg[$i]=round($sys_load_array[$i]/$core_num*100,2);
            if($load_avg[$i]>100){
                $load_avg[$i]=100;
            }
        }
        return $load_avg;
    }catch(Exception $e){
        return $load_avg;
    }
}
function getChatLogArray($room_id){
/*-------------------------------------------------------------------
-- 機能 --
チャットログ配列を取得する

-- バージョン --
v1.00

-- 更新履歴 --
2018.02.17 制作
-------------------------------------------------------------------*/
    if(empty($room_id)){
        return false;
    }
    $total_log_array=array();
    $log_file_list=glob(DIR_ROOT.'r/n/'.basename($room_id).'/log/*.php');
    if(isset($log_file_list[0])){
        rsort($log_file_list);
        foreach($log_file_list as $log_file){
            $log_array=array();
            if(include($log_file)){
                if(memory_get_usage()>=DOWNLOAD_LOG_LIMIT_SIZE){
                    break;
                }else{
                    $total_log_array=array_merge($log_array,$total_log_array);
                }
            }
            unset($log_array);
        }
    }else{
        return false;
    }
    return $total_log_array;
}
function changeLogStringFormLogRecord($log_record,$principal_id,&$log_time,$output_flag=0){
/*-------------------------------------------------------------------
-- 機能 --
チャットログ配列レコードから出力用ログ文字列を作成する

-- バージョン --
v1.00

-- 更新履歴 --
2018.03.05 制作
-------------------------------------------------------------------*/
    $log_string='';
    if(!empty($log_record[3])){
        $tag_name=$log_record[3];
    }else{
        $tag_name='メイン';
    }
    if(($output_flag==1)&&($tag_name!='メイン')){
        return $log_string;
    }
    $said_player_name=str_replace('"','”',str_replace(',','、',$log_record[1]));
    $said_player_text=str_replace('"','”',str_replace(',','、',$log_record[2]));
    if($output_flag==1){
        $said_player_text=str_replace('<br>',"\n",$said_player_text);
    }else{
        $said_player_text=str_replace(array("/n","/r"),'',$said_player_text);
    }
    if(!empty($principal_id)){
        $said_player_text=preg_replace('/sd&'.$principal_id.'/','(シークレットダイス)',$said_player_text);
        $said_player_text=preg_replace('/^@'.$principal_id.'&([a-zA-Z0-9]+?) /','(ウィスパー受信:$1)',$said_player_text);
        $said_player_text=preg_replace('/^@([a-zA-Z0-9]+?)&'.$principal_id.' /','(ウィスパー送信:$1)',$said_player_text);
    }
    $said_player_text=preg_replace('/sd&[a-zA-Z0-9]+ .+/','(シークレットダイス)',$said_player_text);
    if(preg_match('/^@[a-zA-Z0-9]+?&[a-zA-Z0-9]+? /',$said_player_text)!==1){
        if($log_time<(int)$log_record[0]){
            $log_time=(int)$log_record[0];
        }
        if($output_flag==1){
            $log_string=$said_player_name.'：'.
                        $said_player_text."\n";
        }else{
            $log_string=date('Y-m-d H:i:s',$log_record[0]).','.
                        $tag_name.','.
                        $said_player_name.','.
                        $said_player_text."\r\n";
        }
    }
    return $log_string;
}