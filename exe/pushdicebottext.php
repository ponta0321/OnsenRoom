<?php
    require('../s/common/core.php');
    $result='チャットコマンドの説明を読み込めませんでした。';
    $game_key='g99';
    if(!empty($_POST['game_key'])){
        $game_key=$_POST['game_key'];
    }
    // Bone&Cars 使用前準備
    $use_bac_flag=false;
    if(strpos($game_key,'bac_')!==false){
        if(file_exists(BAC_ROOT.'src/bcdiceCore.rb')){
            $use_bac_flag=true;
            $bac_game_dicebot=substr($game_key,4);
            /* ============================== */
            /* Bone&Cars の仕様によってはコメントアウトする */
            /*            ここから                */
            if(!file_exists(BAC_ROOT.'src/diceBot/'.$bac_game_dicebot.'.rb')){
                $bac_game_dicebot='DiceBot';
            }
            /*            ここまで               */
            /* ============================= */
        }
    }
    if($use_bac_flag==true){ // Bone&Cars
        $default_preset_comment='';
        if(file_exists(BAC_ROOT.'src/bcdiceCore.rb')){
            @include(DIR_ROOT.'s/list/bac_gamelist.php');
        }
        if((isset($bac_gamelist[$game_key]))&&($game_key!='bac_DiceBot')){
			if(!empty(BAC_FRONT_CMD)){
				$send_command=
					escapeshellcmd(BAC_FRONT_CMD.' '.
					DIR_ROOT.'rb/onsendhm.rb /'.trim(BAC_ROOT,'/').'/'.' '.
					$bac_game_dicebot.
					BAC_REAR_CMD);
				$result_send_command=`$send_command`;
			}else{
				$post_data=http_build_query(
					array(
						'flag'=>1,
						'bclocation'=>'/'.trim(BAC_ROOT,'/').'/',
						'dicebot_name'=>$bac_game_dicebot
					),"","&");
				$header=array(
					'Content-Type: application/x-www-form-urlencoded',
					'Content-Length: '.strlen($post_data)
				);
				$context=array(
					'http'=>array(
						'method'=>'POST',
						'header'=>implode("\r\n",$header),
						'content'=>$post_data
					),
					'ssl'=>array(
						'verify_peer'=>false,
						'verify_peer_name'=>false
					)
				);
				$result_send_command=file_get_contents(URL_ROOT.'rb/plug.rb',false,stream_context_create($context));
			}
			if($result_send_command===false){
				$result_dhm['ght']='<span style="color:#DDD;">ダイスボット説明の取得に失敗しました。</span>';
			}else{
				$result_dhm=json_decode($result_send_command,true);
			}
            if(!empty($result_dhm['error'])){
                // Bone&Carsから出力されたエラーを表示する
                $result=$default_preset_comment."\n【".$bac_gamelist[$game_key][0]."用】\n".'<span style="color:#DDD;">ダイスボット説明の取得時にエラーが発生しました。｛'.$result_dhm['error'].'｝</span>';
            }elseif(!empty($result_dhm['ght'])){
                // Bone&Carsから出力された説明文を表示する
                $result=$default_preset_comment."\n【".$bac_gamelist[$game_key][0]."用】\n".$result_dhm['ght'];
            }else{
                // Bone&Carsから出力される説明文がないため、$bac_gamelistの説明文を表示する
                $result=$default_preset_comment."\n【".$bac_gamelist[$game_key][0]."用】\n".$bac_gamelist[$game_key][1];
            }
        }else{
            $result=$default_preset_comment;
        }
    }else{ // オンセンdb
        $default_preset_comment='';
        $dicebot_textlist=array();
        if(file_exists(DIR_ROOT.'s/list/dicebot_textlist.php')){
            @include(DIR_ROOT.'s/list/dicebot_textlist.php');
        }
        if(isset($dicebot_textlist[$game_key])){
            $result=$dicebot_textlist[$game_key][1];
        }else{
            $result=$dicebot_textlist['g99'][1];
        }
    }
    echo $result;