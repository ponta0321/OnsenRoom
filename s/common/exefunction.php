<?php 
/*===============================
 ルーム内で何度も呼び出して使用する関数
 主にexeディレクトリー内ファイルが呼び出すもの
===============================*/
function replaceObWord($origin_comment){
/*-------------------------------------------------------------------
-- 機能 --
使用不可の文字を使用可能な文字に置き換える

-- バージョン --
v1.01

-- 更新履歴 --
2016.03.25 制作
2016.10.22 更新
-------------------------------------------------------------------*/
    //$ob_word_list=array(array('<','＜'),array('>','＞'),array('&','＆'));
    $ob_word_list=array(array('<','＜'),array('>','＞'),array('&','＆'),array("'",'’'),array('"','”'));
    foreach($ob_word_list as $value){
        $origin_comment=str_replace($value[0],$value[1],$origin_comment);
    }
    $origin_comment=preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '',$origin_comment); // add. 2016.10.22
    return $origin_comment;
}
function rollDice($count_number,$dice_surface,&$dice,$max_number=100,$max_surface=100){
/*-------------------------------------------------------------------
-- 機能 --
ダイスロールをし、結果を返す

-- バージョン --
v1.00

-- 更新履歴 --
2016.03.25 制作
-------------------------------------------------------------------*/
// $count_number :ロール数
// $dice_surface :面の数
    $result=false;
    $total_roll=0;
    $dice=array();
    $count_number=floor($count_number);
    $dice_surface=floor($dice_surface);
    if(($count_number>0)&&($count_number<=$max_number)&&($dice_surface>1)&&($dice_surface<=$max_surface)){
        for($i=0;$i<$count_number;$i++){
            $dice[$i]=array($dice_surface,mt_rand(1,$dice_surface));
            $total_roll=$total_roll+$dice[$i][1];
        }
        $result=$total_roll;
    }
    return $result;
}
function checkPlayerInRoom($flag,$playerid,$str_base_lp,$observer_flag,&$participant_array,$time_limit=PLAYER_ALIVE_TIME){
/*-------------------------------------------------------------------
-- 機能 --
ルームインしているプレイヤーをチェックし、ルームxml用のルームインプレイヤーリスト文字列を排出する

-- バージョン --
v1.00

-- 引数 --
$flag   :0=roomout 1=roomin

-- 更新履歴 --
2016.07.17 制作
-------------------------------------------------------------------*/
    $str_result_lp='';
    $participant_array=array();
    $now_time=time();
    $login_player_list_exist=false;
    $login_player_array=array();
    if(!empty($str_base_lp)){
        $login_player_list=explode('^',$str_base_lp);
        foreach((array)$login_player_list as $login_player_record){
            $login_player_data=explode('|',$login_player_record);
            if(!empty($login_player_data[0])){
                $login_player_array[]=$login_player_data;
                $login_player_list_exist=true;
            }
        }
    }
    $rewrite_flag=false;
    // 本人以外をstr化
    if($login_player_list_exist==true){
        foreach($login_player_array as $lpa_key => $lpa_value){
            if($lpa_value[0]==$playerid){
                // 本人
                if($flag==1){
                    if($str_result_lp!=''){
                        $str_result_lp.='^';
                    }
                    $str_result_lp.=$playerid.'|'.$now_time.'|'.$observer_flag;
                    if($observer_flag!=1){
                        $participant_array[]=array($lpa_value[0],$lpa_value[1]);
                    }
                }
                $rewrite_flag=true;
            }elseif(($now_time-(int)$lpa_value[1])<=$time_limit){
                // 本人以外
                if($str_result_lp!=''){
                    $str_result_lp.='^';
                }
                $str_result_lp.=$lpa_value[0].'|'.$lpa_value[1].'|'.$lpa_value[2];
                if($lpa_value[2]!=1){
                    $participant_array[]=array($lpa_value[0],$lpa_value[1]);
                }
            }
        }
    }
    if($rewrite_flag==false){
        if((!empty($playerid))&&($flag==1)){
            // 本人をstr化
            if($str_result_lp!=''){
                $str_result_lp.='^';
            }
            $str_result_lp.=$playerid.'|'.$now_time.'|'.$observer_flag;
            if($observer_flag!=1){
                $participant_array[]=array($playerid,$now_time);
            }
        }
    }
    return $str_result_lp;
}
function autoloadXmlFile($xml_file,$shadow_file='',$max_retry=1){
/*-------------------------------------------------------------------
-- 機能 --
XMLファイルの読み込み処理

-- バージョン --
v1.01

-- 更新履歴 --
2016.10.30 制作
2018.03.24 更新 
-------------------------------------------------------------------*/
    if($max_retry<0){
        $max_retry=0;
    }elseif($max_retry>10){
        $max_retry=10;
    }
    $load_files=array();
    if(file_exists($xml_file)){
        $load_files[]=$xml_file;
    }
    if(file_exists($shadow_file)){
        $load_files[]=$shadow_file;
    }
    if(count($load_files)==0){
        return false;
    }
    libxml_use_internal_errors(true);
    $error_level=0;
    foreach($load_files as $file_name){
        for($retry=0;$retry<=$max_retry;$retry++){
            if(($xml_data=simplexml_load_file($file_name))!==false){
                // バックアップファイルの作成
                if($file_name===$xml_file){
                    if(!empty($shadow_file)){
                        copy($xml_file,$shadow_file);
                    }
                }
                autoloadXmlFile_ErrorLogger($error_level,'loaded',$xml_file);
                return $xml_data;
            }else{
                $error_level++;
            }
        }
    }
    // バックアップファイルまでが壊れていると判断し、修復を試みる
    foreach($load_files as $file_name){
        if(($broken_data=file_get_contents($file_name))!==false){
            $dom=new DomDocument('1.0');
            $dom->encoding="UTF-8";
            $dom->appendChild($dom->createElement('data'));
            $sxml=simplexml_import_dom($dom);
            $sxml_head=$sxml->addChild('head');
            autoloadXmlFile_LoopProcess($broken_data,$sxml_head,getRoomHeadDataArray(),1);
            $cardset_base_array=array(
                'id'=>'',
                'title'=>'',
                'url'=>'',
                'imagesheet'=>'',
                'card'=>array(
                    'nm'=>'',
                    'sx'=>'0',
                    'sy'=>'0',
                    'dt'=>'',
                    'l'=>'0',
                    'd'=>'0',
                    'v'=>'0',
                    'z'=>'0',
                ),
            );
            $cardset_array=array();
            $cardset_array['cardset1']=$cardset_base_array;
            $cardset_array['cardset2']=$cardset_base_array;
            $cardset_array['cardset3']=$cardset_base_array;
            autoloadXmlFile_LoopProcess($broken_data,$sxml_head,$cardset_array);
            $sxml_body=$sxml->addChild('body');
            $room_body_array=array(
                'content'=>array(
                    'date'=>'0',
                    'text'=>'0',
                    'chat_color'=>'0',
                    'ctyp'=>'',
                    'author'=>'0',
                ),
                'participant'=>array(
                    'participant_id'=>'',
                    'participant_nm'=>'',
                    'participant_ut'=>'0',
                ),
                'character'=>array(
                    'char_owner'=>'',
                    'char_id'=>'',
                    'char_name'=>'',
                    'char_hp'=>'',
                    'char_mhp'=>'',
                    'char_mp'=>'',
                    'char_mmp'=>'',
                    'char_outer_url'=>'',
                    'char_memo'=>'',
                    'char_image'=>'',
                ),
            );
            autoloadXmlFile_LoopProcess($broken_data,$sxml_body,$room_body_array);
            autoloadXmlFile_ErrorLogger($error_level,'recovered',$xml_file);
            return $sxml;
        }else{
            $error_level++;
        }
    }
    autoloadXmlFile_ErrorLogger($error_level,'broken',$xml_file);
    return false;
}
function autoloadXmlFile_LoopProcess($text_data,$root_elm,$room_part_array,$forced_add=0){
/*-------------------------------------------------------------------
-- 機能 --
autoloadXmlFileの反復処理

-- バージョン --
v1.00

-- 引数 --

-- 更新履歴 --
2018.03.24 制作
-------------------------------------------------------------------*/
    foreach($room_part_array as $rp_key => $rp_value){
        if(!empty($res_preg=preg_match_all('/<'.$rp_key.'>([\s\S]*?)<\/'.$rp_key.'>/',$text_data,$matched_group))){
            $matched_number=count($matched_group[1]);
            for($i=0;$i<$matched_number;$i++){
                if(is_array($rp_value)){
                    $branch_elm=$root_elm->addChild($rp_key);
                    if(count($rp_value)>0){
                        autoloadXmlFile_LoopProcess($matched_group[1][$i],$branch_elm,$rp_value,1);
                    }
                }else{
                    if(strpos($matched_group[1][$i],array('>','<'))===false){
                        $root_elm->addChild($rp_key,$matched_group[1][$i]);
                    }else{
                        $root_elm->addChild($rp_key,$rp_value);
                    }
                }
            }
        }elseif(!empty($res_preg=preg_match_all('/<'.$rp_key.' ([0-9a-zA-Z]+?)="(.*?)">([\s\S]*?)<\/'.$rp_key.'>/',$text_data,$matched_group))){
            $matched_number=count($matched_group[1]);
            for($i=0;$i<$matched_number;$i++){
                if(is_array($rp_value)){
                    $branch_elm=$root_elm->addChild($rp_key);
                    $branch_elm->addAttribute($matched_group[1][$i],$matched_group[2][$i]);
                    if(count($rp_value)>0){
                        autoloadXmlFile_LoopProcess($matched_group[3][$i],$branch_elm,$rp_value,1);
                    }
                }else{
                    if(strpos($matched_group[3][$i],array('>','<'))===false){
                        $branch_elm=$root_elm->addChild($rp_key,$matched_group[3][$i]);
                    }else{
                        $branch_elm=$root_elm->addChild($rp_key,$rp_value);
                    }
                    $branch_elm->addAttribute($matched_group[1][$i],$matched_group[2][$i]);
                }
            }
        }elseif(!empty($res_preg=preg_match_all('/<'.$rp_key.'\/>/',$text_data,$matched_group))){
            $matched_number=count($matched_group[0]);
            for($i=0;$i<$matched_number;$i++){
                $root_elm->addChild($rp_key,$rp_value);
            }
        }elseif($forced_add!=0){
            if(is_array($rp_value)){
                $branch_elm=$root_elm->addChild($rp_key);
                if(count($rp_value)>0){
                    autoloadXmlFile_LoopProcess('',$branch_elm,$rp_value,1);
                }
            }else{
                $root_elm->addChild($rp_key,$rp_value);
            }
        }
    }
}
function autoloadXmlFile_ErrorLogger($error_level,$error_msg,$xml_file,$errorlog_file=DIR_ROOT.'log/errloadxml.txt'){
/*-------------------------------------------------------------------
-- 機能 --
autoloadXmlFileのエラーテキストを返す

-- バージョン --
v1.00

-- 引数 --

-- 更新履歴 --
2018.03.24 制作
-------------------------------------------------------------------*/
    if($error_level>0){
        file_put_contents($errorlog_file,date('Y-m-d H:i:s').' ERR_LV:'.$error_level.' :'.$error_msg.': '.basename(dirname($xml_file))."\n",FILE_APPEND|LOCK_EX);
    }
}
function filterProhiChar($target_str,$flag=0){
/*-------------------------------------------------------------------
-- 機能 --
対象の文字列からXMLとして使用できない不正な文字を削除もしくは変換し、文字列を返す

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.23 制作
-------------------------------------------------------------------*/
    $result=$target_str;
    if($flag==0){
        $change_word=array(
			array('<','＜'),
			array('>','＞'),
			array("'",'’'),
			array('"','”'),
			array('\\','￥'),
			array('，',','),
			array('!','！'),
			array('?','？'),
			array('{','｛'),
			array('}','｝'),
			array('(','（'),
			array(')','）'),
			array('[','［'),
			array(']','］'),
			array('|','｜'),
			array('.','．'),
			array('+','＋'),
			array('-','－'),
			array('*','＊'),
			array('=','＝'),
			array('^','＾'),
			array('/','／'));
    }else{
        $change_word=array(
			array('<','＜'),
			array('>','＞'),
			array('"','”'),
			array('\\','￥'),
			array('{','｛'),
			array('}','｝'),
			array('[','［'),
			array(']','］'),
			array('*','＊'),
			array('=','＝'));
    
    }
    foreach($change_word as $cw_value){
        $result=str_replace($cw_value[0],$cw_value[1],$result);
    }
    $result=preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/','',$result);
    return $result;
}
function filterProhiACLChar($target_str){
/*-------------------------------------------------------------------
-- 機能 --
対象の文字列からXMLとして使用できない不正な文字を削除+改行コードも削除、もしくは変換し、文字列を返す

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.23 制作
-------------------------------------------------------------------*/
    $result=filterProhiChar($target_str);
    $result=str_replace('&#10;','',$result);
    $result=str_replace('&#13;','',$result);
    $result=str_replace('&#x0D;','',$result);
    $result=str_replace('&#x0A;','',$result);
    return $result;
}
function loadCardset(&$msg,$cardset_no,$obj_room,$obj_cardset,$url_cardset){
/*-------------------------------------------------------------------
-- 機能 --
カードセットを読み込む

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.14 制作
-------------------------------------------------------------------*/
    $msg='';
    $result=false;
    // 必要データのチェック
    // IDのチェック
    $cardset_id='';
    if(!empty($obj_cardset->id)){
        if(preg_match('/[a-zA-Z0-9_]+/',(string)$obj_cardset->id)){
            $cardset_id=filterProhiChar((string)$obj_cardset->id);
        }else{
            $msg='カードセットのIDに使用できない文字が含まれています。'."\n".'（使用できる文字は半角英数字と半角アンダーバーのみです。）';
            return $result;
        }
    }else{
        $msg='カードセットのIDがありません。';
        return $result;
    }
    // タイトルのチェック
    $cardset_title='';
    if(!empty($obj_cardset->title)){
        $cardset_title=filterProhiACLChar((string)$obj_cardset->title);
    }else{
        $msg='カードセットのタイトルがありません。';
        return $result;
    }
    // カードのチェック
    if(!isset($obj_cardset->card[0]['id'])){
        $msg='使用できるカードデータがありません。';
        return $result;
    }
    if((!empty($cardset_id))&&(!empty($cardset_title))){
        $tag_cardset='cardset'.$cardset_no;
        // カードセットの解放
        unset($obj_room->head->{$tag_cardset});
        // カードセットの作成
        $obj_room->head->addChild($tag_cardset);
        $obj_room->head->{$tag_cardset}->addChild('id',$cardset_id);
        $obj_room->head->{$tag_cardset}->addChild('title',$cardset_title);
        $obj_room->head->{$tag_cardset}->addChild('url',$url_cardset);
        $obj_room->head->{$tag_cardset}->addChild('imagesheet');
        $image_width=-1;
        $image_height=-1;
        if(!empty($obj_cardset->imagesheet)){
			$context=array(
				'ssl'=>array(
					'verify_peer'=>false,
					'verify_peer_name'=>false
				)
			);
			if($image_data=file_get_contents((string)$obj_cardset->imagesheet,FILE_BINARY,stream_context_create($context))){
				if($imagesize=getimagesizefromstring($image_data)){
					$obj_room->head->{$tag_cardset}->imagesheet=(string)$obj_cardset->imagesheet;
					$image_width=$imagesize[0];
					$image_height=$imagesize[1];
				}
			}
        }
        $i=0;
        foreach($obj_cardset->card as $card_value){
            // カードのチェック
            if((!empty($card_value['id']))&&(!empty($card_value->nm))){
                if(preg_match('/[a-zA-Z0-9_]+/',(string)$card_value['id'])){
                    $new_card=$obj_room->head->{$tag_cardset}->addChild('card');
                    $new_card->addAttribute('id',filterProhiACLChar((string)$card_value['id']));
                    $new_card->addChild('nm',filterProhiACLChar((string)$card_value->nm));
                    $card_sx=-1;
                    if(isset($card_value->sx)){
                        if($image_width>=(int)$card_value->sx){
                            $card_sx=(int)$card_value->sx;
                        }
                    }
                    $new_card->addChild('sx',$card_sx);
                    $card_sy=-1;
                    if(isset($card_value->sy)){
                        if($image_height>=(int)$card_value->sy){
                            $card_sy=(int)$card_value->sy;
                        }
                    }
                    $new_card->addChild('sy',$card_sy);
                    $card_dt='';
                    if(!empty($card_value->dt)){
                        $card_dt=filterProhiChar((string)$card_value->dt);
                        $card_dt=str_replace(array('&#13;&#10;','&#13;&#10;','&#13;','&#x0D;&#x0A;','&#x0D;','&#x0A;',"\r\n","\r","\n"),'&#10;',$card_dt);
                        $card_dt=str_replace(array(' ',"\t"),'',$card_dt);
                        //$card_dt=str_replace('　','',$card_dt);
                    }
                    $new_card->addChild('dt',$card_dt);
                    $new_card->addChild('l','_0'); // _0=山札 _1=捨て札 _2=場 プレイヤーID=手札
                    $new_card->addChild('d',0); // 方向 0=上 90=右 180=下 270=左
                    $new_card->addChild('v',0); // 公開 _0=全員に非公開 _1=全員に公開 プレイヤーID=プレイヤーにのみ公開
                    $new_card->addChild('z',$i); // 順番 小さい数字 上 大きな数字 下 0=一番上
                    $i++;
                }
            }
        }
        $comment='カードセット'.$cardset_no.'に「'.$cardset_title.'」をセットし、初期化を行いました。';
        $BContent=$obj_room->body->addChild('content');
        $BContent->addAttribute('id',creatChatMsgId());
        $BContent->addChild('date',time());
        $BContent->addChild('text',htmlentities($comment,ENT_XML1));
        $BContent->addChild('chat_color','#000000');
        $BContent->addChild('author','システム');
        // 100を超えたコメントを削除
        $overflow_comment=count($obj_room->body->content)-100;
        if($overflow_comment>0){
            for($i=0;$i<$overflow_comment;$i++){
                unset($obj_room->body->content[$i]);
            }
        }
        $result=true;
    }else{
        $msg='データが不正です。';
    }
    return $result;
}
function initCardset($cardset_no,$obj_room,$l_flag='_0',$d_flag=0,$v_flag=0,$z_flag=1){
/*-------------------------------------------------------------------
-- 機能 --
カードの初期化（山札、捨て札、手札）
全てのカードを山札に戻し、シャッフル、全員非公開にする

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.14 制作
-------------------------------------------------------------------*/
    $result=false;
    $tag_cardset='cardset'.$cardset_no;
    // 必要データのチェック
    if(isset($obj_room->head->{$tag_cardset}->id)){
        $card_count=count($obj_room->head->{$tag_cardset}->card);
        $card_numbering_array=array();
        for($i=0;$i<$card_count;$i++){
            $card_numbering_array[$i]=$i;
        }
        shuffle($card_numbering_array);
        $i=0;
        foreach($obj_room->head->{$tag_cardset}->card as $card_value){
            $card_value->l=$l_flag;
            if($d_flag==0){ // 方向 すべて上
                $card_value->d=0;
            }else{ // 方向 ランダム
                $card_value->d=mt_rand(0,1)*180;
            }
            if($v_flag==0){ // すべて非公開
                $card_value->v='_0';
            }elseif($v_flag==1){ // すべて公開
                $card_value->v='_1';
            }else{ // 公開 ランダム
                $card_value->v='_'.mt_rand(0,1);
            }
            if($z_flag==0){ // Z 整列
                $card_value->z=$i;
            }else{ // Z シャッフル
                $card_value->z=$card_numbering_array[$i];
            }
            $i++;
        }
        $result=true;
    }
    return $result;
}
function shuffleCard($cardset_no,$obj_room,$l_flag='_0',$d_flag=0,$v_flag=0){
/*-------------------------------------------------------------------
-- 機能 --
カードをシャッフルする（山札、捨て札）

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.20 制作
-------------------------------------------------------------------*/
    $result=false;
    $tag_cardset='cardset'.$cardset_no;
    // 必要データのチェック
    if(isset($obj_room->head->{$tag_cardset}->id)){
        $card_numbering_array=array();
        foreach($obj_room->head->{$tag_cardset}->card as $card_value){
            if($card_value->l==$l_flag){
                $card_numbering_array[]=$card_value['id'];
            }
        }
        shuffle($card_numbering_array);
        $i=0;
        foreach($card_numbering_array as $card_na_id){
            foreach($obj_room->head->{$tag_cardset}->card as $card_value){
                if($card_value['id']==$card_na_id){
                    if($d_flag==0){ // 方向 すべて上
                        $card_value->d=0;
                    }else{ // 方向 ランダム
                        $card_value->d=mt_rand(0,1)*180;
                    }
                    if($v_flag==0){ // すべて非公開
                        $card_value->v='_0';
                    }elseif($v_flag==1){ // すべて公開
                        $card_value->v='_1';
                    }else{ // 公開 ランダム
                        $card_value->v='_'.mt_rand(0,1);
                    }
                    $card_value->z=$i;
                    break;
                }
            }
            $i++;
        }
        $result=true;
    }
    return $result;
}
function alignmentCard($cardset_no,$obj_room,$card_id,$l_flag,$z_flag=0){
/*-------------------------------------------------------------------
-- 機能 --
カードの順番を整理する

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.14 制作
-------------------------------------------------------------------*/
    $result=false;
    $tag_cardset='cardset'.$cardset_no;
    // 必要データのチェック
    if(isset($obj_room->head->{$tag_cardset}->id)){
        $alignment_array=array();
        $max_no=0;
        $target_card='';
        foreach($obj_room->head->{$tag_cardset}->card as $card_value){
            if($card_value['id']==$card_id){
                $target_card=array($card_value['id'],(int)$card_value->z);
            }
            if($card_value->l==$l_flag){
                if($card_value['id']!=$card_id){
                    if($z_flag==0){ // 一番上にターゲットを移動
                        $alignment_array[]=array($card_value['id'],((int)$card_value->z+1));
                    }else{ // 一番下にターゲットを移動
                        $alignment_array[]=array($card_value['id'],$card_value->z);
                    }
                }
                if(((int)$card_value->z)>$max_no){
                    $max_no=(int)$card_value->z;
                }
            }
        }
        if(!empty($card_id)){
            if($z_flag==0){ // 一番上にターゲットを移動
                $alignment_array[]=array($target_card[0],0);
            }else{ // 一番下にターゲットを移動
                $alignment_array[]=array($target_card[0],($max_no+1));
            }
        }
        if(1<count($alignment_array)){
            foreach($alignment_array as $sort_key => $sort_value){
                $sort_z[$sort_key]=(int)$sort_value[1];
            }
            @array_multisort($sort_z,SORT_ASC,SORT_NUMERIC,$alignment_array);
        }
        for($i=0;$i<count($alignment_array);$i++){
            foreach($obj_room->head->{$tag_cardset}->card as $card_value){
                if($card_value['id']==$alignment_array[$i][0]){
                    $card_value->z=$i;
                    break;
                }
            }
        }
        $result=true;
    }
    return $result;
}
function changeCardLocation(&$base_card,$cardset_no,$obj_room,$card_id,$l_flag,$v_flag,$z_flag=0){
/*-------------------------------------------------------------------
-- 機能 --
カードのロケーションを変更する

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.14 制作
-------------------------------------------------------------------*/
    $base_card['nm']='';
    $base_card['l']='';
    $base_card['d']='';
    $base_card['v']='';
    $result=false;
    $tag_cardset='cardset'.$cardset_no;
    // 必要データのチェック
    if(isset($obj_room->head->{$tag_cardset}->id)){
        $exlocation='';
        if(($card_id=='card_stock')||
           ($card_id=='discard_stock')){ // 山札／捨て札から変更
            $min_no_array=array('false',999999);
            $max_no_array=array('false',0);
            $target_card='';
            if($card_id=='card_stock'){
                $target_location='_0';
                if('_0'!=$l_flag){
                    $exlocation='_0';
                }
            }elseif($card_id=='discard_stock'){
                $target_location='_1';
                if('_1'!=$l_flag){
                    $exlocation='_1';
                }
            }
            for($i=0;$i<count($obj_room->head->{$tag_cardset}->card);$i++){
                if($obj_room->head->{$tag_cardset}->card[$i]->l==$target_location){
                    if(((int)$obj_room->head->{$tag_cardset}->card[$i]->z)<$min_no_array[1]){
                        $min_no_array=array($i,(int)$obj_room->head->{$tag_cardset}->card[$i]->z);
                        $result=true;
                    }
                    if(((int)$obj_room->head->{$tag_cardset}->card[$i]->z)>$max_no_array[1]){
                        $max_no_array=array($i,(int)$obj_room->head->{$tag_cardset}->card[$i]->z);
                        $result=true;
                    }
                }
            }
            if($result==true){
                if($card_id=='card_stock'){ // 一番上のカードを移動
                    $card_id=$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]['id'];
                    $base_card['nm']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->nm;
                    $base_card['l']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->l;
                    $base_card['d']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d;
                    $base_card['v']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->v;
                    $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->l=$l_flag;
                    if($l_flag!='_2'){
                        $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d=0;
                    }
                    $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->v=$v_flag;
                }elseif($card_id=='discard_stock'){ // 一番上のカードを移動
                    $card_id=$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]['id'];
                    $base_card['nm']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->nm;
                    $base_card['l']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->l;
                    $base_card['d']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d;
                    $base_card['v']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->v;
                    $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->l=$l_flag;
                    if($l_flag!='_2'){
                        if($obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d==90){
                            $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d=0;
                        }elseif($obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d==270){
                            $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d=180;
                        }
                    }
                    $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->v=$v_flag;
                }
            }
        }else{ // 場／手札から変更
            foreach($obj_room->head->{$tag_cardset}->card as $card_value){
                if($card_value['id']==$card_id){
                    // $l_flag _0=山札 _1=捨て札 _2=場 プレイヤーID=手札
                    $base_card['nm']=(string)$card_value->nm;
                    $base_card['l']=(string)$card_value->l;
                    $base_card['d']=(string)$card_value->d;
                    $base_card['v']=(string)$card_value->v;
                    if($card_value->l!=$l_flag){
                        $exlocation=(string)$card_value->l;
                    }
                    $card_value->l=$l_flag;
                    if($l_flag!='_2'){
                        if($card_value->d==90){
                            $card_value->d=0;
                        }elseif($card_value->d==270){
                            $card_value->d=180;
                        }
                    }
                    $card_value->v=$v_flag;
                    $result=true;
                    break;
                }
            }
        }
        if($result==true){
            // $z_flag 0=一番上に対象のカードを移動 1=一番下に対象のカードを移動
            alignmentCard($cardset_no,$obj_room,$card_id,$l_flag,$z_flag);
            if(!empty($exlocation)){
                alignmentCard($cardset_no,$obj_room,'',$exlocation,1);
            }
        }
    }
    return $result;
}
function checkCardLocationAndCange(&$base_card,$cardset_no,$obj_room,$card_id,$bl_flag,$l_flag,$v_flag,$z_flag=0){
/*-------------------------------------------------------------------
-- 機能 --
カードの元ロケーションをチェック後、変更がなければロケーションを変更する

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.21 制作
-------------------------------------------------------------------*/
    $base_card['nm']='';
    $base_card['l']='';
    $base_card['d']='';
    $base_card['v']='';
    $result=false;
    $tag_cardset='cardset'.$cardset_no;
    // 必要データのチェック
    if(isset($obj_room->head->{$tag_cardset}->id)){
        $check_flag=false;
        foreach($obj_room->head->{$tag_cardset}->card as $card_value){
            if($card_value['id']==$card_id){
                if($card_value->l==$bl_flag){
                    $check_flag=true;
                }
                break;
            }
        }
        if($check_flag==true){
            $result=changeCardLocation($base_card,$cardset_no,$obj_room,$card_id,$l_flag,$v_flag,$z_flag);
        }
    }
    return $result;
}
function changeAllCardLocation(&$base_card,$cardset_no,$obj_room,$card_id,$l_flag,$v_flag,$z_flag=0){
/*-------------------------------------------------------------------
-- 機能 --
指定したロケーションの全カードのロケーションを変更する

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.14 制作
-------------------------------------------------------------------*/
    $base_card['nm']='';
    $base_card['l']='';
    $base_card['d']='';
    $base_card['v']='';
    $tag_cardset='cardset'.$cardset_no;
    $result=false;
    $bl_flag='';
    if($card_id=='card_stock'){
        $bl_flag='_0';
    }elseif($card_id=='discard_stock'){
        $bl_flag='_1';
    }else{
        foreach($obj_room->head->{$tag_cardset}->card as $card_value){
            if($card_value['id']==$card_id){
                $bl_flag=(string)$card_value->l;
                break;
            }
        }
    }
    if(($bl_flag==$l_flag)||(empty($bl_flag))||(empty($l_flag))){
        return $result;
    }
    // 必要データのチェック
    if(isset($obj_room->head->{$tag_cardset}->id)){
        $reiteration_array=array();
        for($i=0;$i<count($obj_room->head->{$tag_cardset}->card);$i++){
            if($obj_room->head->{$tag_cardset}->card[$i]->l==$bl_flag){
                $reiteration_array[]=(string)$obj_room->head->{$tag_cardset}->card[$i]['id'];
            }
        }
        if(count($reiteration_array)>0){
            foreach($reiteration_array as $r_value){
                $result=changeCardLocation($base_card,$cardset_no,$obj_room,$r_value,$l_flag,$v_flag,$z_flag);
            }
        }
    }
    return $result;
}
function changeCardVisible(&$base_card,$cardset_no,$obj_room,$card_id,$s_flag,$s_value){
/*-------------------------------------------------------------------
-- 機能 --
カードの内部値を変更する（d=方向、v=公開設定）

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.20 制作
-------------------------------------------------------------------*/
    $base_card['nm']='';
    $base_card['l']='';
    $base_card['d']='';
    $base_card['v']='';
    $result=false;
    $tag_cardset='cardset'.$cardset_no;
    // 必要データのチェック
    if(isset($obj_room->head->{$tag_cardset}->id)){
        if(($card_id=='card_stock')||
           ($card_id=='discard_stock')){ // 山札／捨て札から変更
            $min_no_array=array('false',999999);
            $max_no_array=array('false',0);
            if($card_id=='card_stock'){
                $target_location='_0';
            }elseif($card_id=='discard_stock'){
                $target_location='_1';
            }
            for($i=0;$i<count($obj_room->head->{$tag_cardset}->card);$i++){
                if($obj_room->head->{$tag_cardset}->card[$i]->l==$target_location){
                    if(((int)$obj_room->head->{$tag_cardset}->card[$i]->z)<$min_no_array[1]){
                        $min_no_array=array($i,(int)$obj_room->head->{$tag_cardset}->card[$i]->z);
                        $result=true;
                    }
                    if(((int)$obj_room->head->{$tag_cardset}->card[$i]->z)>$max_no_array[1]){
                        $max_no_array=array($i,(int)$obj_room->head->{$tag_cardset}->card[$i]->z);
                        $result=true;
                    }
                }
            }
            if($result==true){
                $base_card['nm']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->nm;
                $base_card['l']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->l;
                $base_card['d']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d;
                $base_card['v']=(string)$obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->v;
                if($card_id=='card_stock'){ // 一番上のカードを移動
                    if($s_flag=='v'){ // 公開設定の変更
                        $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->v=$s_value;
                    }elseif($s_flag=='d'){ // 方向の変更
                        $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d=$s_value;
                    }
                }elseif($card_id=='discard_stock'){ // 一番上のカードを移動
                    if($s_flag=='v'){ // 公開設定の変更
                        $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->v=$s_value;
                    }elseif($s_flag=='d'){ // 方向の変更
                        $obj_room->head->{$tag_cardset}->card[$min_no_array[0]]->d=$s_value;
                    }
                }
            }
        }else{ // 場／手札から変更
            foreach($obj_room->head->{$tag_cardset}->card as $card_value){
                if($card_value['id']==$card_id){
                    $base_card['nm']=(string)$card_value->nm;
                    $base_card['l']=(string)$card_value->l;
                    $base_card['d']=(string)$card_value->d;
                    $base_card['v']=(string)$card_value->v;
                    if($s_flag=='v'){ // 公開設定の変更
                        $card_value->v=$s_value;
                    }elseif($s_flag=='d'){ // 方向の変更
                        $card_value->d=$s_value;
                    }
                    $result=true;
                    break;
                }
            }
        }
    }
    return $result;
}
function strNickName($target_id,$obj_room){
/*-------------------------------------------------------------------
-- 機能 --
ルームファイルに記憶されているニックネームで返す（存在しない場合はプレイヤーIDを返す）

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.22 制作
-------------------------------------------------------------------*/
    $result=$target_id;
    if(isset($obj_room->body->participant)){
        foreach($obj_room->body->participant as $p_value){
            if((string)$p_value->participant_id==$target_id){
                $result=(string)$p_value->participant_nm;
                break;
            }
        }
    }
    return $result;
}
function strLocationName($l_flag,$obj_room){
/*-------------------------------------------------------------------
-- 機能 --
ロケーションフラグからロケーション名を返す

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.22 制作
-------------------------------------------------------------------*/
    $result='不明な場所';
    if($l_flag=='_0'){
        $result='山札';
    }elseif($l_flag=='_1'){
        $result='捨て札';
    }elseif($l_flag=='_2'){
        $result='場';
    }else{
        $result=strNickName($l_flag,$obj_room);
    }
    return $result;
}
function strDirectionName($d_flag){
/*-------------------------------------------------------------------
-- 機能 --
方向フラグから方向名を返す

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.14 制作
-------------------------------------------------------------------*/
    $result=$d_flag.'度の方向';
    if($d_flag==0){
        $result='正面';
    }elseif($d_flag==90){
        $result='右向き';
    }elseif($d_flag==270){
        $result='左向き';
    }elseif($d_flag==180){
        $result='逆向き';
    }
    return $result;
}
function strVisibleName($v_flag){
/*-------------------------------------------------------------------
-- 機能 --
公開フラグから公開名を返す

-- バージョン --
v1.00

-- 更新履歴 --
2016.12.22 制作
-------------------------------------------------------------------*/
    $result='非公開';
    if($v_flag=='_0'){
        $result='伏せた状態';
    }elseif($v_flag=='_1'){
        $result='全員に公開';
    }else{
        $result='本人のみ見える状態';
    }
    return $result;
}
function loadBoardData(&$msg,$room_id,$obj_room,$obj_boarddata,$url_root=URL_ROOT,$dir_root=DIR_ROOT){
/*-------------------------------------------------------------------
-- 機能 --
ボード情報ファイルを読み込む

-- バージョン --
v1.01

-- 更新履歴 --
2017.01.11 制作
2017.04.12 更新 画像の復元追加
-------------------------------------------------------------------*/
    $msg='';
    $result=false;
    // 必要データのチェック
    $game_grid=5;
    if(isset($obj_boarddata->game_grid)){
        if(is_numeric((int)$obj_boarddata->game_grid)){
            $game_grid=(int)$obj_boarddata->game_grid;
            if($game_grid<0){
                $game_grid=0;
            }elseif($game_grid>10){
                $game_grid=10;
            }
        }else{
            $msg='グリッドの濃さが読み込めません。';
            return $result;
        }
    }else{
        $msg='データ「グリッドの濃さ」がありません。';
        return $result;
    }
    $game_boardwidth=17;
    if(isset($obj_boarddata->game_boardwidth)){
        if(is_numeric((int)$obj_boarddata->game_boardwidth)){
            $game_boardwidth=(int)$obj_boarddata->game_boardwidth;
            if($game_boardwidth<1){
                $game_boardwidth=1;
            }
        }else{
            $msg='ボードの幅が読み込めません。';
            return $result;
        }
    }else{
        $msg='データ「ボードの幅」がありません。';
        return $result;
    }
    $game_boardheight=20;
    if(isset($obj_boarddata->game_boardheight)){
        if(is_numeric((int)$obj_boarddata->game_boardheight)){
            $game_boardheight=(int)$obj_boarddata->game_boardheight;
            if($game_boardheight<1){
                $game_boardheight=1;
            }
        }else{
            $msg='ボードの高さが読み込めません。';
            return $result;
        }
    }else{
        $msg='データ「ボードの高さ」がありません。';
        return $result;
    }
    $map_data='';
    if(!empty($obj_boarddata->map_data)){
        $map_data=filterProhiChar((string)$obj_boarddata->map_data,1);
    }
    $game_mapping='';
    if(!empty($obj_boarddata->game_mapping)){
        if(preg_match('/[0-9,\-^]+/',(string)$obj_boarddata->game_mapping)){
            $game_mapping=filterProhiChar((string)$obj_boarddata->game_mapping,1);
        }else{
            $msg='マッピング情報に不正なデータが含まれています。';
            return $result;
        }
    }
    $game_syncboardsize=0;
    if(!empty($obj_boarddata->game_syncboardsize)){
        if(is_numeric((int)$obj_boarddata->game_syncboardsize)){
            $game_syncboardsize=(int)$obj_boarddata->game_syncboardsize;
            if($game_syncboardsize<0){
                $game_syncboardsize=0;
            }elseif($game_syncboardsize>1){
                $game_syncboardsize=1;
            }
        }else{
            $msg='背景画像原寸サイズ表示フラグが読み込めません。';
            return $result;
        }
    }
    $game_imagestrength=10;
    if(isset($obj_boarddata->game_imagestrength)){
        if(is_numeric((int)$obj_boarddata->game_imagestrength)){
            $game_imagestrength=(int)$obj_boarddata->game_imagestrength;
            if($game_imagestrength<0){
                $game_imagestrength=0;
            }elseif($game_imagestrength>10){
                $game_imagestrength=10;
            }
        }else{
            $msg='背景画像の濃さが読み込めません。';
            return $result;
        }
    }
    $game_backimage='__false__';
    if(isset($obj_boarddata->game_backimage)){
        $game_backimage=(string)$obj_boarddata->game_backimage;
    }
    $game_mapchip='';
    if(!empty($obj_boarddata->game_mapchip)){
        $game_mapchip=(string)$obj_boarddata->game_mapchip;
    }
    if(($map_data!='')||($game_mapping!='')||($game_backimage!='__false__')){
        // 画像データの復元
		$restored_images=array();
		$room_url=$url_root.'r/n/'.basename($room_id).'/';
        $room_dir=$dir_root.'r/n/'.basename($room_id).'/';
        if(isset($obj_boarddata->base64->image)){
            foreach($obj_boarddata->base64->image as $image_record){
                if((!empty($image_record->file))&&
                   (!empty($image_record->data))){
                    $image_file=$room_dir.(string)$image_record->file;
                    if(!file_exists($image_file)){
                        file_put_contents($image_file,base64_decode((string)$image_record->data));
                    }
					$restored_images[]=(string)$image_record->file;
                }
            }
        }
        if(isset($obj_room->head->game_grid)){
            $obj_room->head->game_grid=$game_grid;
        }else{
            $obj_room->head->addChild('game_grid',$game_grid);
        }
        if(isset($obj_room->head->game_boardwidth)){
            $obj_room->head->game_boardwidth=$game_boardwidth;
        }else{
            $obj_room->head->addChild('game_boardwidth',$game_boardwidth);
        }
        if(isset($obj_room->head->game_boardheight)){
            $obj_room->head->game_boardheight=$game_boardheight;
        }else{
            $obj_room->head->addChild('game_boardheight',$game_boardheight);
        }
        if($map_data!=''){
            $rows=explode('^',$map_data);
            $rebuild_map_data='';
			for($i=0;$i<count($rows);$i++){
				$columns=explode('|',$rows[$i]);
				if(!empty($columns[1])){
					foreach($restored_images as $r_image){
						if(basename($columns[1])==$r_image){
							$columns[1]=$room_url.$r_image;
							break;
						}
					}
				}
				$new_column_data='';
				foreach($columns as $c_value){
					if($new_column_data!=''){
						$new_column_data.='|';
					}
					$new_column_data.=$c_value;
				}
				if($rebuild_map_data!=''){
					$rebuild_map_data.='^';
				}
				$rebuild_map_data.=$new_column_data;
			}
			if(isset($obj_room->head->map_data)){
                $obj_room->head->map_data=$rebuild_map_data;
            }else{
                $obj_room->head->addChild('map_data',$rebuild_map_data);
            }
        }
        if($game_mapping!=''){
            if(isset($obj_room->head->game_mapping)){
                $obj_room->head->game_mapping=$game_mapping;
            }else{
                $obj_room->head->addChild('game_mapping',$game_mapping);
            }
        }
        if($game_backimage!='__false__'){
            $game_backimage=preg_replace('/(|https?:)\/\/'.preg_quote(THIS_DOMAIN,'/').'\/r\/n\/[0-9a-z]+\//',URL_ROOT.'r/n/'.$room_id.'/',$game_backimage);
            if(isset($obj_room->head->game_backimage)){
                $obj_room->head->game_backimage=$game_backimage;
            }else{
                $obj_room->head->addChild('game_backimage',$game_backimage);
            }
            if(isset($obj_room->head->game_syncboardsize)){
                $obj_room->head->game_syncboardsize=$game_syncboardsize;
            }else{
                $obj_room->head->addChild('game_syncboardsize',$game_syncboardsize);
            }
            if(isset($obj_room->head->game_imagestrength)){
                $obj_room->head->game_imagestrength=$game_imagestrength;
            }else{
                $obj_room->head->addChild('game_imagestrength',$game_imagestrength);
            }
        }
        if($game_mapchip!=''){
            if(isset($obj_room->head->game_mapchip)){
                $obj_room->head->game_mapchip=$game_mapchip;
            }else{
                $obj_room->head->addChild('game_mapchip',$game_mapchip);
            }
        }
        $comment='ボード情報を読み込みました。';
        $BContent=$obj_room->body->addChild('content');
        $BContent->addAttribute('id',creatChatMsgId());
        $BContent->addChild('date',time());
        $BContent->addChild('text',htmlentities($comment,ENT_XML1));
        $BContent->addChild('chat_color','#909090');
        $BContent->addChild('author','システム');
        // 100を超えたコメントを削除
        $overflow_comment=count($obj_room->body->content)-100;
        if($overflow_comment>0){
            for($i=0;$i<$overflow_comment;$i++){
                unset($obj_room->body->content[$i]);
            }
        }
        $result=true;
    }else{
        $msg='ボード情報が見つかりません。';
    }
    return $result;
}

function adjustMappingData($obj_room){
/*-------------------------------------------------------------------
-- 機能 --
マッピングデータエリアの調整

-- バージョン --
v1.00

-- 更新履歴 --
2017.01.12 制作
-------------------------------------------------------------------*/
    $game_boardwidth=(int)$obj_room->head->game_boardwidth;
    $game_boardheight=(int)$obj_room->head->game_boardheight;
    $game_mapping=(string)$obj_room->head->game_mapping;
    $max_width=99;
    if($max_width<$game_boardwidth){
        $max_width=$game_boardwidth;
    }
    $max_height=99;
    if($max_height<$game_boardheight){
        $max_height=$game_boardheight;
    }
    $mapping_data_array=array();
    for($i=0;$i<($max_height+1);$i++){
        for($j=0;$j<($max_width+1);$j++){
            if(($i<$game_boardheight)&&($j<$game_boardwidth)){
                $mapping_data_array[$i][$j]=0;
            }else{
                $mapping_data_array[$i][$j]=-1;
            }
        }
    }
    $mapping_list_y=explode('^',$game_mapping);
    for($i=0;$i<count($mapping_list_y);$i++){
        $mapping_list_x=explode(',',$mapping_list_y[$i]);
        for($j=0;$j<count($mapping_list_x);$j++){
            if(($i<$game_boardheight)&&($j<$game_boardwidth)){
                if($mapping_list_x[$j]>0){
                    $mapping_data_array[$i][$j]=$mapping_list_x[$j];
                }
            }
        }
    }
    $game_mapping='';
    for($i=0;$i<$game_boardheight;$i++){
        if($i!=0){
            $game_mapping.='^';
        }
        for($j=0;$j<$game_boardwidth;$j++){
            if($j!=0){
                $game_mapping.=',';
            }
            $game_mapping.=$mapping_data_array[$i][$j];
        }
    }
    $obj_room->head->game_mapping=$game_mapping;
    return true;
}
function creatChatMsgId($player_id='',$now_time=0){
/*-------------------------------------------------------------------
-- 機能 --
ルームのチャットメッセージIDの生成

-- バージョン --
v1.00

-- 更新履歴 --
2017.02.02 制作
-------------------------------------------------------------------*/
    if($player_id===''){
        $player_id='_s';
    }elseif(empty($player_id)){
        $player_id='aZ';
    }
    $chr_array=array('l','I','0','Z','o','C','z','G','H','2','i','3','w','D','V','k','m','n',
        'b','c','d','e','f','g','h','7','j','r','s','9','t','u','v','x','T','y','4','5','a','6',
        'A','B','q','E','F','1','J','K','L','M','N','P','8','p','Q','R','S','U','W','X','Y','O');
	if(strlen($now_time)<9){
		$now_time=time();
	}
    $r1=substr($now_time,0,1)+substr($now_time,3,1)+substr($now_time,6,1)+substr($now_time,9,1);
    $r2=substr($now_time,1,1)+substr($now_time,4,1)+substr($now_time,7,1)+mt_rand(0,34);
    $r3=substr($now_time,2,1)+substr($now_time,5,1)+substr($now_time,8,1)+mt_rand(0,34);
    return $chr_array[$r1].substr($player_id,0,1).$chr_array[$r2].substr($player_id,-1,1).$chr_array[$r3];
}
function saveRoomXmlFile($obj_room,$room_file,$principal,$nick_name,$observer_flag,$entry_flag=1,$player_alive_time=PLAYER_ALIVE_TIME){
/*-------------------------------------------------------------------
-- 機能 --
ルーム情報を各種チェック後、保存する。
  1. 100を超えたコメントを削除
  2. ログインプレイヤーチェック
-- バージョン --
v1.00

-- 更新履歴 --
2017.09.12 制作
-------------------------------------------------------------------*/
    // 100を超えたコメントを削除
    $overflow_comment=count($obj_room->body->content)-100;
    if($overflow_comment>0){
        for($i=0;$i<$overflow_comment;$i++){
            unset($obj_room->body->content[$i]);
        }
    }
    // ログインプレイヤーチェック
    $participant_array=array();
    if(isset($obj_room->head->login_players)){
        $obj_room->head->login_players=checkPlayerInRoom($entry_flag,$principal,(string)$obj_room->head->login_players,$observer_flag,$participant_array);
    }else{
        $obj_room->head->addChild('login_players',checkPlayerInRoom($entry_flag,$principal,'',$observer_flag,$participant_array));
    }
    $participant_update_flag=false;
    $now_time=time();
    if(isset($obj_room->body->participant)){
        for($i=0;$i<count($obj_room->body->participant);$i++){
            $alive_participant_flag=false;
            foreach($participant_array as $p_value){
                if((string)$obj_room->body->participant[$i]->participant_id==$p_value[0]){
                    $alive_participant_flag=true;
                    break;
                }
            }
            if($alive_participant_flag==true){
                if((string)$obj_room->body->participant[$i]->participant_id==$principal){
                    $obj_room->body->participant[$i]->participant_ut=$now_time;
                    $obj_room->body->participant[$i]->participant_nm=htmlentities($nick_name);
                    $participant_update_flag=true;
                }
            }else{
                unset($obj_room->body->participant[$i]);
            }
        }
    }
    if(($participant_update_flag==false)&&($observer_flag==0)&&($entry_flag==1)){
        $nodeCharacter=$obj_room->body->addChild('participant');
        $nodeCharacter->addChild('participant_id',$principal);
        $nodeCharacter->addChild('participant_nm',htmlentities($nick_name),ENT_XML1);
        $nodeCharacter->addChild('participant_ut',$now_time);
    }
    return $obj_room->asXML($room_file);
}
function initMappingData($borad_width=17,$borad_height=20){
/*-------------------------------------------------------------------
-- 機能 --
マップ情報の初期値を返す

-- バージョン --
v1.00

-- 引数 --

-- 更新履歴 --
2018.02.13 制作
-------------------------------------------------------------------*/
    $map_datas='';
    for($i=0;$i<$borad_height;$i++){
        if($map_datas!=''){
            $map_datas.='^';
        }
        $borad_width_datas='';
        for($j=0;$j<$borad_width;$j++){
            if($borad_width_datas!=''){
                $borad_width_datas.=',';
            }
            $borad_width_datas.='0';
        }
        $map_datas.=$borad_width_datas;
    }
    return $map_datas;
}
function createSessionRoom($room_name,$room_pass,$creator,$creator_ip,$expiration_time,$tour_flag,&$error_msg,$game_type='g99',$observe_write=0){
/*-------------------------------------------------------------------
-- 機能 --
セッション用ルームを作成する。

-- バージョン --
v1.04

-- 引数 --

-- 更新履歴 --
2016.05.17 制作
2016.10.06 更新 room_voice_room, room_voice_invite 追加
2017.06.01 更新 observe_write 追加
2017.12.15 更新 $error_msg 追加
2018.03.24 更新 getRoomHeadDataArray、complementRoomData_LoopProcessの追加
-------------------------------------------------------------------*/
    $error_msg='';
    $room_pass_flg='なし';
    if(!empty($room_pass)){
        $room_pass_flg='あり';
    }
    if($tour_flag!=0){
        $observe_write=1;
    }
    $bao_dicebot_file=DIR_ROOT.'s/list/bac_gamelist.php';
    @include($bao_dicebot_file);
    if(isset($bac_gamelist)){
        foreach((array)$bac_gamelist as $bacgl_key => $bacgl_value){
            if($game_type==$bacgl_value[0]){
                $game_type=$bacgl_key;
                break;
            }
        }
    }
    $now_time=time();
    $base_room_file=changeIDformRoomName($room_name);
    $room_dir=DIR_ROOT.'r/n/'.$base_room_file.'/';
    if(!is_dir($room_dir)){
        if(!mkdir($room_dir,0755,true)){
            $error_msg='ルーム作成時にパーミッションエラーが発生しました。';
            return false;
        }
    }
    $room_file=$room_dir.'data.xml';
    if(!file_exists($room_file)){
        // room　情報
        $dom=new DomDocument('1.0');
        $dom->encoding="UTF-8";
        $dom->appendChild($dom->createElement('data'));
        $sxml=simplexml_import_dom($dom);
        // head 情報
        $sxml_head=$sxml->addChild('head');
        complementRoomData_LoopProcess($sxml_head,getRoomHeadDataArray(17,20));
        $sxml->head->name=$room_name;
        $sxml->head->password=$room_pass;
        $sxml->head->tour=$tour_flag;
        $sxml->head->observe_write=$observe_write;
        $sxml->head->creator=$creator;
        $sxml->head->game_dicebot=$game_type;
		for($i=1;$i<6;$i++){
			$memo_node=$sxml->head->addChild('memo');
			$memo_node->addAttribute('id',$i);
			$memo_node->addChild('txt','');
			$memo_node->addChild('flag',0);
			$memo_node->addChild('limit',30);
		}
        // body 情報
        $sxml_body=$sxml->addChild('body');
        $sxml_b_c=$sxml_body->addChild('content');
        $sxml_b_c->addAttribute('id',creatChatMsgId());
        $sxml_b_c->addChild('date',$now_time);
        $sxml_b_c->addChild('text',htmlentities('ルーム「'.$room_name.'」が作成されました。',ENT_XML1));
        $sxml_b_c->addChild('chat_color','#909090');
        $sxml_b_c->addChild('ctyp',1);
        $sxml_b_c->addChild('author','システム');
        // ルームファイルの保存
        if($sxml->asXML($room_file)){
            // phpログ保存
            @include(DIR_ROOT.'s/common/exewritelog.php');
            // room list　情報
            // 人数確認ファイルの保存 add.2016.11.07
            if(empty($room_pass)){
                $password_flag=0;
            }else{
                $password_flag=1;
            }
            $room_list=new classRoomList;
            if($room_list->save(THIS_DOMAIN,
                                $base_room_file,
                                $room_name,
                                $creator,
                                $creator_ip,
                                $room_pass,
                                '-1',
                                $now_time,
                                $now_time,
                                ((int)$now_time+(int)$expiration_time),
                                TRANSFER_PROTOCOL,
                                $password_flag,
                                $tour_flag,
                                0,
                                0,
                                $game_type)){
            
                return $room_list->room;
            }
        }else{
            $error_msg='ルーム作成に失敗しました。';
            return false;
        }
    }
    $error_msg='指定されたルーム名は既に存在します。';
    return false;
}
function complementRoomData_LoopProcess($root_elm,$room_part_array){
/*-------------------------------------------------------------------
-- 機能 --
complementRoomDataの反復処理

-- バージョン --
v1.00

-- 引数 --

-- 更新履歴 --
2018.03.24 制作
-------------------------------------------------------------------*/
    foreach($room_part_array as $rp_key => $rp_value){
        if(!isset($root_elm->{$rp_key})){
            $branch_elm=$root_elm->addChild($rp_key);
            $exist_flag=false;
        }else{
            $branch_elm=$root_elm->{$rp_key};
            $exist_flag=true;
        }
        if(is_array($rp_value)){
            if(count($rp_value)>0){
                complementRoomData_LoopProcess($branch_elm,$rp_value);
            }
        }elseif($exist_flag===false){
            $root_elm->{$rp_key}=$rp_value;
        }
    }
}
function complementRoomData($room_xml){
/*-------------------------------------------------------------------
-- 機能 --
セッション用ルームのデータで欠落している箇所を補完する。

-- バージョン --
v1.01

-- 引数 --

-- 更新履歴 --
2018.02.13 制作
2018.03.24 更新 反復処理を別関数化
-------------------------------------------------------------------*/
    if(!isset($room_xml->head)){
        $room_xml->addChild('head');
    }
    $board_width=17;
    if(isset($room_xml->head->game_boardwidth)){
        $board_width=(int)$room_xml->head->game_boardwidth;
    }
    $board_height=20;
    if(isset($room_xml->head->game_boardheight)){
        $board_height=(int)$room_xml->head->game_boardheight;
    }
    $room_part_array=getRoomHeadDataArray($board_width,$board_height);
    complementRoomData_LoopProcess($room_xml->head,$room_part_array);
    if(!isset($room_xml->body)){
        $room_xml->addChild('body');
    }
    return true;
}
function getRoomHeadDataArray($board_width=17,$board_height=20){
/*-------------------------------------------------------------------
-- 機能 --
ルームデータ head構成配列を呼び出す

-- バージョン --
v1.01

-- 更新履歴 --
2018.03.24 制作
2018.05.22 更新 game_memo　2,3,4,5 popup_flag puw_limit_time の削除
-------------------------------------------------------------------*/
    $soundless_file=URL_ROOT.'sounds/se02.mp3';
    return array(
        'name'=>'無名のルーム',
        'password'=>'',
        'tour'=>'0',
        'observe_write'=>'0',
        'creator'=>'comp_sys',
        'dice'=>array(
            'd_surface'=>'6,6',
            'd_number'=>'6,6',
            'd_result'=>'12'
        ),
        'dice_roll'=>array(
            'dr_count'=>'0',
            'dr_surface'=>'6'
        ),
        'game_update'=>'0',
        'game_boardwidth'=>$board_width,
        'game_boardheight'=>$board_height,
        'map_data'=>'',
        'game_mapping'=>initMappingData($board_width,$board_height),
        'game_mapchip'=>URL_DEFAULT_MAPCHIP,
        'game_syncboardsize'=>'0',
        'game_grid'=>'5',
        'game_backimage'=>'',
        'game_backimagelist'=>'',
        'game_imagestrength'=>'10',
        'game_dicebot'=>'g99',
        'voice_room_id'=>'-1',
        'voice_invite_code'=>'-1',
        'game_music'=>(file_exists($soundless_file)?$soundless_file:''),
        'game_music_state'=>'stop',
        'game_timer'=>'|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1^|0|1',
        'login_players'=>'',
    );
}
function checkImgUrl($target_url){
/*-------------------------------------------------------------------
-- 機能 --
画像URLか判断し返す

-- バージョン --
v1.00

-- 更新履歴 --
2017.11.29 制作
-------------------------------------------------------------------*/
    $str_candidate_img=str_replace('＆','&',$target_url);
    // 画像拡張子を持っているかチェック
    $ext_format_img_flag=false;
    $format_array=array('.png','.jpg','.jpeg','.gif');
    foreach($format_array as $format){
        if(strpos($str_candidate_img,$format)!==false){
            $ext_format_img_flag=true;
            break;
        }
    }
    if($ext_format_img_flag!==false){
        if(preg_match('/(https?:\/\/[0-9a-z_.,:;+*=@#&$%?\/!()\'-]+\.(png|jpg|jpeg|gif))/i',$str_candidate_img,$match_result)){
            return $match_result[0];
        }else{
            return false;
        }
    // 本サイト内の画像かチェック
    }elseif(strpos($str_candidate_img,URL_ROOT)!==false){
        if(preg_match('/'.preg_quote(URL_ROOT,'/').'r\/n\/[0-9a-z]+\/((uploadimage[0-9m]+|char_[._0-9a-z]+)|[0-9a-z]+\/i\/chara[0-9]+)/i',$str_candidate_img,$match_result)){
            return $match_result[0];
        }else{
            return false;
        }
    // trpgsession.click内の画像かチェック
    }elseif(strpos($str_candidate_img,'//trpgsession.click')!==false){
        if(preg_match('/(|https?:)\/\/trpgsession.click\/[cp]\/[0-9a-z]+\/(i|image)\/[a-z]+[0-9]+/i',$str_candidate_img,$match_result)){
            return $match_result[0];
        }else{
            return false;
        }
    }
    return false;
}
function checkMusicUrl($target_url){
/*-------------------------------------------------------------------
-- 機能 --
楽曲URLか判断し返す

-- バージョン --
v1.01

-- 更新履歴 --
2017.11.30 制作
2018.03.12 更新 ドメインチェックを追加
-------------------------------------------------------------------*/
    $permission_url_list=array();
    $permission_url_list_file=DIR_ROOT.'s/list/permission_url_list.php';
    if(file_exists($permission_url_list_file)){
        include($permission_url_list_file);
    }
    $permission_url_list[]=THIS_DOMAIN;
    $str_candidate_music=str_replace('＆','&',$target_url);
    if(preg_match('/(https?:\/\/[0-9a-z_.,:;+*=@#&$%?\/!()\'-]+\.(mp3|mpeg|ogg|wav))/i',$str_candidate_music,$match_result)){
        foreach($permission_url_list as $p_url){
            if(strpos($match_result[0],$p_url)!==false){
                return $match_result[0];
            }
        }
    }
    return false;
}
function runSetCommand($setting_command,$room_xml,$room_file,$principal,$nick_name,$observer_flag){
/*-------------------------------------------------------------------
-- 機能 --
セットコマンドを実行する

-- バージョン --
v1.00

-- 更新履歴 --
2017.11.15 制作
-------------------------------------------------------------------*/
    $success_msg='';
    $error_msg='';
    if(empty($setting_command)){
        $error_msg='セットコマンドが未指定です。';
    }else{
        $save_setting_flag=false;
        // 背景画像の変更
        if(preg_match('/board image (.+)$/i',$setting_command,$match)){
            $sbi_st=explode(' ',$match[1]);
            $gbi_url=$sbi_st[0];
            if($game_backimage=checkImgUrl($gbi_url)){
                $room_xml->head->game_backimage=$game_backimage;
                $game_syncboardsize=0;
                if(@$sbi_st[1]==1){
                    $game_syncboardsize=1;
                }
                if(isset($room_xml->head->game_syncboardsize)){
                    $room_xml->head->game_syncboardsize=$game_syncboardsize;
                }else{
                    $room_xml->head->addChild('game_syncboardsize',$game_syncboardsize);
                }
                if(@$sbi_st[2]==1){
                    $room_xml->head->map_data='';
                }
                if((!empty($sbi_st[3]))&&(!empty($sbi_st[4]))){
                    $game_boardwidth=$sbi_st[3];
                    if($game_boardwidth<17){
                        $game_boardwidth=17;
                    }elseif($game_boardwidth>100){
                        $game_boardwidth=100;
                    }
                    $room_xml->head->game_boardwidth=$game_boardwidth;
                    $game_boardheight=$sbi_st[4];
                    if($game_boardheight<20){
                        $game_boardheight=20;
                    }elseif($game_boardheight>100){
                        $game_boardheight=100;
                    }
                    $room_xml->head->game_boardheight=$game_boardheight;
                    // マッピングデータエリアの調整
                    adjustMappingData($room_xml);
                }
                $save_setting_flag=true;
                $success_msg='背景画像を「'.basename($game_backimage).'」に変更しました。';
            }else{
                $error_msg='その画像は表示できません。';
            }
        // 背景画像の削除
        }elseif(preg_match('/^board noimage$/i',$setting_command,$match)){
            if(empty($room_xml->head->game_backimage)){
                $error_msg='消去する背景画像がありません。';
            }else{
                $room_xml->head->game_backimage='';
                $save_setting_flag=true;
                $success_msg='背景画像を消去しました。';
            }
        // 楽曲の変更・再生
        }elseif(preg_match('/^music play (.+)$/i',$setting_command,$match)){
            $sbi_st=explode(' ',$match[1]);
            $gbi_url=$sbi_st[0];
            if($game_music=checkMusicUrl($gbi_url)){
                if(isset($room_xml->head->game_music)){
                    $room_xml->head->game_music=$game_music;
                }else{
                    $room_xml->head->addChild('game_music',$game_music);
                }
                if(isset($room_xml->head->game_music_state)){
                    $room_xml->head->game_music_state='play';
                }else{
                    $room_xml->head->addChild('game_music_state','play');
                }
                $save_setting_flag=true;
                $success_msg='BGM「'.basename($game_music).'」を再生します。';
            }else{
                $error_msg='その楽曲は使用できません。';
            }
        // 楽曲の停止
        }elseif(preg_match('/^music stop/i',$setting_command,$match)){
            if(isset($room_xml->head->game_music_state)){
                if($room_xml->head->game_music_state!='stop'){
                    $room_xml->head->game_music_state='stop';
                    $save_setting_flag=true;
                    $success_msg='BGMを停止しました。';
                }else{
                    $error_msg='BGMは既に停止しています。';
                }
            }else{
                $room_xml->head->addChild('game_music_state','stop');
                $save_setting_flag=true;
                $success_msg='BGMを停止しました。';
            }
        // マッピングデータの更新
        }elseif(preg_match('/^map ([0-9,^]+)/i',$setting_command,$match)){
            $game_mapping=$match[1];
            if(isset($room_xml->head->game_mapping)){
                $room_xml->head->game_mapping=$game_mapping;
            }else{
                $room_xml->head->addChild('game_mapping',$game_mapping);
            }
            $mapping_list_x=array();
            $mapping_list_y=explode('^',$game_mapping);
            for($i=0;$i<count($mapping_list_y);$i++){
                $mapping_list_x=explode(',',$mapping_list_y[$i]);
                for($j=0;$j<count($mapping_list_x);$j++){
                    $mapping_data_array[$i][$j]=$mapping_list_x[$j];
                }
            }
            $game_boardwidth=count($mapping_list_x);
            if($game_boardwidth<17){
                $game_boardwidth=17;
            }elseif($game_boardwidth>100){
                $game_boardwidth=100;
            }
            $room_xml->head->game_boardwidth=$game_boardwidth;
            $game_boardheight=count($mapping_list_y);
            if($game_boardheight<20){
                $game_boardheight=20;
            }elseif($game_boardheight>100){
                $game_boardheight=100;
            }
            $room_xml->head->game_boardheight=$game_boardheight;
            // マッピングデータエリアの調整
            adjustMappingData($room_xml);
            $save_setting_flag=true;
            $success_msg='マップを変更しました。(サイズ：'.$game_boardwidth.'x'.$game_boardheight.')';
        // マッピングデータの削除
        }elseif(preg_match('/^map delete/i',$setting_command,$match)){
            if(isset($room_xml->head->game_mapping)){
                $room_xml->head->game_mapping='';
            }else{
                $room_xml->head->addChild('game_mapping','');
            }
            // マッピングデータエリアの調整
            adjustMappingData($room_xml);
            $save_setting_flag=true;
            $success_msg='マップを消去しました。';
        // マスクの配置
        }elseif(preg_match('/^mask ([bprw]+),([0-9]+),([0-9]+),([0-9]+)/i',$setting_command,$match)){
            if($match[1]=='w'){ // マスク画像
                $mk_pid='chessmanOtB902_';
                $mk_img=URL_ROOT.'images/mask/char_mk02.png';
            }elseif($match[1]=='r'){
                $mk_pid='chessmanOtB903_';
                $mk_img=URL_ROOT.'images/mask/char_mk03.png';
            }elseif($match[1]=='p'){
                $mk_pid='chessmanOtB904_';
                $mk_img=URL_ROOT.'images/mask/char_mk04.jpg';
            }else{
                $mk_pid='chessmanOtB901_';
                $mk_img=URL_ROOT.'images/mask/char_mk01.png';
            }
            $mk_mg=(int)$match[2]; // 倍率
            if($mk_mg<1){
                $mk_mg=1;
            }elseif($mk_mg>10){
                $mk_mg=10;
            }
            $mk_bw=(int)$match[3]; // x
            if($mk_bw<17){
                $mk_bw=17;
            }elseif($mk_bw>100){
                $mk_bw=100;
            }
            $room_xml->head->game_boardwidth=$mk_bw;
            $mk_bh=(int)$match[4]; // y
            if($mk_bh<20){
                $mk_bh=20;
            }elseif($mk_bh>100){
                $mk_bh=100;
            }
            $room_xml->head->game_boardheight=$mk_bh;
            $map_data=''; // マスク配置
            $map_data_count=1;
            for($i=0;$i<$mk_bh;$i+=$mk_mg){
                for($j=0;$j<$mk_bw;$j+=$mk_mg){
                    if($map_data!=''){
                        $map_data.='^';
                    }
                    $map_data.=$mk_pid.'1470000'.sprintf('%03d',$i).sprintf('%03d',$j).'|'.$mk_img.'|'.($i*32).'|'.($j*32).'|'.($mk_mg*32).'|'.($mk_mg*32).'|img||0|'.$map_data_count;
                    $map_data_count++;
                }
            }
            $room_xml->head->map_data=$map_data;
            // マッピングデータエリアの調整
            adjustMappingData($room_xml);
            $save_setting_flag=true;
            $success_msg='マスクを設置しました。(サイズ：'.$mk_bw.'x'.$mk_bh.')';
        // コマの全消去
        }elseif(preg_match('/^chessman delete/i',$setting_command,$match)){
            if(empty($room_xml->head->map_data)){
                $error_msg='消去するコマがありません。';
            }else{
                $room_xml->head->map_data='';
                $save_setting_flag=true;
                $success_msg='全てのコマを消去しました。';
            }
        // 不明なコマンド
        }else{
            $error_msg='実行できないセットコマンドです。';
        }
        // ルームの保存
        if($save_setting_flag==true){
            if(!saveRoomXmlFile($room_xml,$room_file,$principal,$nick_name,$observer_flag)){
                $error_msg='情報を更新できませんでした。';
            }
        }
    }
    if(!empty($error_msg)){
        echo 'ERR='.$error_msg;
        return false;
    }else{
        if(!empty($success_msg)){
            echo 'SWM='.$success_msg;
        }
        return true;
    }
}
function getSecretCommentByCID($comment_id,$content_array,$room_dir){
/*-------------------------------------------------------------------
-- 機能 --
秘密コメント（ﾌﾟﾛｯﾄ、ｼｰｸﾚｯﾄ）リストを読み込み、コメントIDに該当するコメントを返す

-- バージョン --
v1.00

-- 更新履歴 --
2018.04.27 制作
-------------------------------------------------------------------*/
	foreach((array)$content_array as $content_block){
		if(isset($content_block['id'])){
			if($content_block['id']==$comment_id){ // IDチェック
				return str_replace('&amp;','&',(string)$content_block->text);
			}
		}
	}
	$file=$room_dir.'secret_comment_list.php';
	if(!file_exists($file)){
		return 0;
	}
	$log_array=array();
	if(include($file)){
		$result=0;
		if(isset($log_array[$comment_id])){
			$result=$log_array[$comment_id][2];
		}
		return $result;
	}else{
		return -1;
	}
}
function getSecretCommentByPID(&$comment_id,$principal_id,$content_array,$room_dir){
/*-------------------------------------------------------------------
-- 機能 --
秘密コメント（ﾌﾟﾛｯﾄ、ｼｰｸﾚｯﾄ）リストを読み込み、プレイヤーIDに該当するもっとも最近のコメントを返す

-- バージョン --
v1.00

-- 更新履歴 --
2018.04.27 制作
-------------------------------------------------------------------*/
	$comment_id='';
	foreach((array)$content_array as $content_block){
		if((isset($content_block['id']))&&
		   (isset($content_block->text))){
			   
			$check_content_type_text=str_replace('&amp;','&',(string)$content_block->text);
			if(preg_match('/^(pd|sd)&([a-zA-Z0-9]+) (.+)$/',$check_content_type_text,$cbt_match)){ // シークレットコメントチェック
				if($principal_id==$cbt_match[2]){ // 本人チェック
					$comment_id=$content_block['id'];
					return $cbt_match[3];
				}
			}
		}
	}
	$file=$room_dir.'secret_comment_list.php';
	if(!file_exists($file)){
		return 0;
	}
	$log_array=array();
	if(include($file)){
		foreach(array_reverse($log_array) as $key => $value){
			if(preg_match('/^(pd|sd)&([a-zA-Z0-9]+) (.+)$/',$value[2],$cbt_match)){ // シークレットコメントチェック
				if($principal_id==$cbt_match[2]){ // 本人チェック
					$comment_id=$key;
					return $cbt_match[3];
				}
			}
		}
		return 0;
	}else{
		return -1;
	}
}
function convertRoomData($room_xml){
/*-------------------------------------------------------------------
-- 機能 --
セッション用ルームのデータで旧仕様(ver.1.00.05まで)のものを新仕様に変更する。

-- バージョン --
v1.00

-- 更新履歴 --
2018.05.22 制作
-------------------------------------------------------------------*/
	$new_memo_array=array();
	if(isset($room_xml->head->game_memo)){
		$new_memo_array[1]=array(
			'txt'=>(string)$room_xml->head->game_memo,
			'flag'=>0,
			'limit'=>30
		);
		unset($room_xml->head->game_memo);
	}
	for($i=2;$i<6;$i++){
		if(isset($room_xml->head->{'game_memo'.$i})){
			$new_memo_array[$i]=array(
				'txt'=>(string)$room_xml->head->{'game_memo'.$i},
				'flag'=>0,
				'limit'=>30
			);
		}
		unset($room_xml->head->{'game_memo'.$i});
	}
	if(isset($room_xml->head->popup_flag)){
		$record=explode('^',(string)$room_xml->head->popup_flag);
		for($i=0;$i<count($record);$i++){
			$new_memo_array[($i+1)]['flag']=(int)$record[$i];
		}
		unset($room_xml->head->popup_flag);
	}
	if(isset($room_xml->head->puw_limit_time)){
		$record=explode('^',(string)$room_xml->head->puw_limit_time);
		for($i=0;$i<count($record);$i++){
			$new_memo_array[($i+1)]['limit']=(int)$record[$i];
		}
		unset($room_xml->head->puw_limit_time);
	}
	if(count($new_memo_array)>0){
		unset($room_xml->head->memo);
		foreach($new_memo_array as $key => $value){
			$memo_node=$room_xml->head->addChild('memo');
			$memo_node->addAttribute('id',$key);
			$memo_node->addChild('txt',(isset($value['txt'])?$value['txt']:''));
			$memo_node->addChild('flag',(isset($value['flag'])?$value['flag']:0));
			$memo_node->addChild('limit',(isset($value['limit'])?$value['limit']:30));
		}
	}
    return true;
}