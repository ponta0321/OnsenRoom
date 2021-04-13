<?php
require('../s/common/core.php');
require(DIR_ROOT.'s/common/exefunction.php');
// コメントの取得
function getDetoxifiedComment(){
	$cmt='';
	if(!empty($_POST['comment'])){
		$cmt=$_POST['comment'];
		foreach(array(array('<','＜'),array('>','＞'),array('&','＆')) as $value){
			$cmt=str_replace($value[0],$value[1],$cmt);
		}
		$cmt=preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '',$cmt);
	}else{
		return false;
	}
	return $cmt;
}
$comment='';
$dice_roll_flag=false;
$system_comment_flag=false;
$secret_dice_flag=false;
$nonroll_dice_flag=false;
if(($origin_comment=getDetoxifiedComment())===false) exit;
$principal='';
if(!empty($_POST['principal'])){
	$principal=$_POST['principal'];
}
$nick_name='';
if(!empty($_POST['nick_name'])){
	$nick_name=$_POST['nick_name'];
}else{
	$nick_name=$principal;
}
$observer_flag=0; // 0=参加者 1=見学者
if(!empty($_POST['observer_flag'])){
	$observer_flag=$_POST['observer_flag'];
}
$chat_color='#000000';
if(!empty($_POST['chat_color'])){
	$chat_color=$_POST['chat_color'];
}
$call_name='ななし';
if(!empty($_POST['call_name'])){
	$call_name=$_POST['call_name'];
}
$chat_type=1; // 1=メイン 2=雑談 3=見学用
if(!empty($_POST['chat_type'])){
	$chat_type=$_POST['chat_type'];
	if($observer_flag==1) $chat_type=3;
}
$stand_img=-1;
if(isset($_POST['stand_img'])){
	$stand_img=$_POST['stand_img'];
}
$stand_pos=0;
if(isset($_POST['stand_pos'])){
	$stand_pos=$_POST['stand_pos'];
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
	if(!file_exists($room_file)) exit;
}else{
	exit;
}
$characterlist_array=array();
$c_character=new classCharacter;
$character_dir=$c_character->getCharDir($room_id,$principal);
$characterlist_file=$character_dir.'characterlist.php';
$exfilelock=new classFileLock($room_dir,$room_id.'_lockfile',5);
if($exfilelock->flock($room_dir)){
	$save_xml_flag=true;
	if(file_exists($characterlist_file)){
		$load_char_name=$call_name;
		if(preg_match('/\$[\S]+(@[\S]+)/',$origin_comment,$match)){
			$origin_comment=str_replace($match[1],'',$origin_comment);
			$load_char_name=str_replace('@','',$match[1]);
		}
		if(include($characterlist_file)){
			$char_key=null;
			foreach($characterlist_array as $cla_key => $cla_value){
				if($cla_value[2]==$load_char_name){
					$char_key=$cla_key;
					break;
				}
			}
			if($char_key!==null){
				if(file_exists($character_dir.$char_key.'.xml')){
					$character_xml=simplexml_load_file($character_dir.$char_key.'.xml');
					$tag=(string)$character_xml->tag;
					$macro=(string)$character_xml->macro;
					unset($character_xml);
					if(strpos($origin_comment,'$')!==false || strpos($origin_comment,'＄')!==false){
						$array_no=0;
						$macro_array=array();
						$macro_rowarray=explode('^',$macro);
						$sort_array=array();
						foreach($macro_rowarray as $mra_value){
							$macro_colarray=explode('|',$mra_value);
							$macro_c=isset($macro_colarray[0])?$macro_colarray[0]:'';
							$macro_e=isset($macro_colarray[1])?$macro_colarray[1]:'';
							if(isset($macro_c) && isset($macro_e)){
								$macro_array[$array_no]=array($macro_c,$macro_e);
								$sort_array[$array_no]=$macro_c;
								$array_no++;
							}
						}
						@array_multisort($sort_array,SORT_DESC,SORT_STRING,$macro_array);
						unset($sort_array);
						unset($array_no);
						foreach($macro_array as $macro_recode){
							if(empty($macro_recode[0])) continue;
							if(stripos($origin_comment,'$'.$macro_recode[0])!==false){
								$origin_comment=str_ireplace('$'.$macro_recode[0],$macro_recode[1],$origin_comment);
								break;
							}elseif(stripos($origin_comment,'＄'.$macro_recode[0])!==false){
								$origin_comment=str_ireplace('＄'.$macro_recode[0],$macro_recode[1],$origin_comment);
								break;
							}
						}
					}
					if(strpos($origin_comment,'#')!==false || strpos($origin_comment,'＃')!==false){
						$array_no=0;
						$tag_array=array();
						$tag_rowarray=explode('^',$tag);
						$sort_array=array();
						foreach($tag_rowarray as $tra_value){
							$tag_colarray=explode('|',$tra_value);
							$tag_name=isset($tag_colarray[0])?$tag_colarray[0]:'';
							$tag_value=isset($tag_colarray[1])?$tag_colarray[1]:'';
							if(isset($tag_name) && isset($tag_value)){
								$tag_array[$array_no]=array($tag_name,$tag_value);
								$sort_array[$array_no]=$tag_name;
								$array_no++;
							}
						}
						@array_multisort($sort_array,SORT_DESC,SORT_STRING,$tag_array);
						unset($sort_array);
						unset($array_no);
						foreach($tag_array as $tag_recode){
							if(empty($tag_recode[0])) continue;
							if(stripos($origin_comment,'#'.$tag_recode[0])!==false){
								$origin_comment=str_ireplace('#'.$tag_recode[0],$tag_recode[1],$origin_comment);
							}elseif(stripos($origin_comment,'＃'.$tag_recode[0])!==false){
								$origin_comment=str_ireplace('＃'.$tag_recode[0],$tag_recode[1],$origin_comment);
							}
						}
					}
				}
			}
		}
	}
	if(empty(CHAR_SET)){
		$match_comment=mb_convert_kana($origin_comment,'a');
	}else{
		$match_comment=mb_convert_kana($origin_comment,'a',CHAR_SET);
	}
	$match_comment=trim(str_replace('　',' ',$match_comment));
	if($system_comment_flag==false){
		$comment=htmlspecialchars($origin_comment);
	}
	// ルームファイルの読み込み
	if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
		$exfilelock->unflock($room_dir);
		exit;
	}
	// 立ち絵フラグ初期
	$stand_flg=false;
	if($stand_img>-1){
		$stand_flg=true;
	}
	// 使用ダイスボットの決定
	$call_dicebot=(string)$room_xml->head->game_dicebot;
	if(!empty($_POST['use_dicebot'])){
		$call_dicebot=$_POST['use_dicebot'];
	}
	$used_slash_command=false;
	$roll_process=false;
	$repeat_flag=0; // 繰り返し数
	$command_error_mes='';
	$chipcomment='';
	$match=array();
	// タグ変更コマンドの反映
	if(preg_match('/^\/(m|main) (.+)$/i',$match_comment,$match)){
		$match_comment=$match[2];
		$comment=$match[2];
		if($observer_flag==1){
			$chat_type=3;
		}else{
			$chat_type=1;
		}
		$used_slash_command=true;
	}elseif(preg_match('/^\/(z|zats?udan) (.+)$/i',$match_comment,$match)){
		$match_comment=$match[2];
		$comment=$match[2];
		if($observer_flag==1){
			$chat_type=3;
		}else{
			$chat_type=2;
		}
		$used_slash_command=true;
	}elseif(preg_match('/^\/(k|kengaku) (.+)$/i',$match_comment,$match)){
		$match_comment=$match[2];
		$comment=$match[2];
		$chat_type=3;
		$used_slash_command=true;
	}
	// シークレット or プロット公開処理
	if(($used_slash_command==false)&&(preg_match('/^\/(ol|openlastplot)$/i',$match_comment,$match))){
		if(isset($room_xml->body->content)){
			$s_comment_id='';
			$s_comment=getSecretCommentByPID($s_comment_id,$principal,$room_xml->body->content,$room_dir);
			if($s_comment===-1){
				echo 'ERR=コメントの読み込みに失敗しました。少し時間をおいて再度お試しください。';
				$save_xml_flag=false;
			}elseif($s_comment===0){
				echo 'ERR=秘匿コメントはありません。';
				$save_xml_flag=false;
			}else{
				$comment='(公開:#'.$s_comment_id.' 発言者ID:'.$principal.') '.$s_comment;
			}
		}else{
			unset($room_xml);
			$exfilelock->unflock($room_dir);
			exit;
		}
		$stand_flg=false;
	// シークレット or プロット公開処理
	}elseif(($used_slash_command==false)&&(preg_match('/^\/(o|openplot) #(.+)$/i',$match_comment,$match))){
		if(isset($room_xml->body->content)){
			$s_comment=getSecretCommentByCID($match[2],$room_xml->body->content,$room_dir);
			if($s_comment===-1){
				echo 'ERR=コメント#'.$match[2].'の読み込みに失敗しました。少し時間をおいて再度お試しください。';
				$save_xml_flag=false;
			}elseif($s_comment===0){
				echo 'ERR=コメント#'.$match[2].'はありません。';
				$save_xml_flag=false;
			}else{
				$op_success_flag=-1; // -1=失敗 0=権限なし 1=成功
				if(preg_match('/^(pd|sd)&([a-zA-Z0-9]+) (.+)$/',$s_comment,$cbt_match)){ // シークレットコメントチェック
					$op_success_flag=0;
					if('sd'==$cbt_match[1]){ // タイプチェック（プロットorシークレット）
						if($principal==$cbt_match[2]){ // 本人チェック
							$op_success_flag=1;
						}
					}else{
						$op_success_flag=1;
					}
				}
				if($op_success_flag==1){
					$comment='(公開:#'.$match[2].' 発言者ID:'.$cbt_match[2].') '.$cbt_match[3];
				}elseif($op_success_flag==0){
					echo 'ERR='.$call_name.'さんには公開権限がありません。';
					$save_xml_flag=false;
				}else{
					echo 'ERR=コメント#'.$match[2].'は非公開属性を持っていません。';
					$save_xml_flag=false;
				}
			}
		}else{
			unset($room_xml);
			$exfilelock->unflock($room_dir);
			exit;
		}
		$stand_flg=false;
	// プロット処理
	}elseif(($used_slash_command==false)&&(preg_match('/^\/(p|plot) (.+)$/i',$match_comment,$match))){
		if(empty($match[2])){
			unset($room_xml);
			$exfilelock->unflock($room_dir);
			exit;
		}
		$comment='pd&'.$principal.' '.$match[2];
		$stand_flg=false;
	// ウィスパー処理
	}elseif(preg_match('/^@([a-zA-Z0-9]+)$/i',$match_comment,$match)){
		unset($room_xml);
		$exfilelock->unflock($room_dir);
		exit;
	}elseif(preg_match('/^@([a-zA-Z0-9]+?) (.*)$/i',$match_comment,$match)){
		if(empty($match[1])||empty($match[2])){
			unset($room_xml);
			$exfilelock->unflock($room_dir);
			exit;
		}
		$comment='@'.$match[1].'&'.$principal.' '.htmlspecialchars($match[2]);
		$dice_roll_flag=false;
		$stand_flg=false;
	// 設定変更処理
	}elseif(($used_slash_command==false)&&(preg_match('/^\/set (.+)$/i',$match_comment,$match))){
		runSetCommand($match[1],$room_xml,$room_file,$principal,$nick_name,$observer_flag);
		unset($room_xml);
		$exfilelock->unflock($room_dir);
		exit;
	// ダイスボット処理
	}else{
		// BCDice
		if(strpos($call_dicebot,'bac_')!==false && !empty(BAC_ENDPOINT)){
			$bac_game_dicebot=substr($call_dicebot,4);
			@include(DIR_ROOT.'s/common/exeboneandcars.php');
		// オンセンdb
		}else{
			@include(DIR_ROOT.'s/common/exeonsendb.php');
		}
	}
	$BContent=$room_xml->body->addChild('content');
	$say_time=time();
	$comment_id=creatChatMsgId($principal,$say_time);
	$BContent->addAttribute('id',$comment_id);
	if(($dice_roll_flag==true)&&($secret_dice_flag==false)&&($nonroll_dice_flag==false)){
		if($say_time>(int)$room_xml->head->dice_roll->dr_count){
			// ロール中
			$str_surface='';
			$str_daice='';
			$i=0;
			foreach($dice as $value){
				if($i!=0){
					$str_surface.=',';
					$str_daice.=',';
				}
				$str_surface.=$value[0];
				$str_daice.=$value[1];
				$i++;
			}
			$room_xml->head->dice->d_surface=$str_surface;
			$room_xml->head->dice->d_number=$str_daice;
			$room_xml->head->dice_roll->dr_count=$say_time+2;
			$room_xml->head->dice_roll->dr_surface=0; //2016.7.8 未使用
			$room_xml->head->dice->d_result=$dice_result;
			$BContent->addChild('date',$say_time+2);
		}else{
			echo 'ERR=現在ロール中のため、'.$call_name.'さんのロールは行われませんでした。';
			$save_xml_flag=false;
		}
		$stand_flg=false;
	}elseif($secret_dice_flag==true){
		$comment='sd&'.$principal.' '.$comment;
		$BContent->addChild('date',$say_time);
		$stand_flg=false;
	}else{
		$BContent->addChild('date',$say_time);
	}
	$BContent->addChild('text',htmlentities($comment,ENT_XML1));
	if($system_comment_flag==false){
		$say_man=$call_name;
	}else{
		$say_man='システム';
	}
	$BContent->addChild('chat_color',$chat_color);
	$BContent->addChild('author',$say_man);
	$BContent->addChild('ctyp',$chat_type);
	// 立ち絵処理
	if($stand_flg==true){
		if(isset($characterlist_array[$char_key])){
			$str_stand_list=$characterlist_array[$char_key][11];
			if(!empty($str_stand_list)){
				$stand_list_record=explode('^',$str_stand_list);
				if(isset($stand_list_record[$stand_img])){
					$stand_list_column=explode('|',$stand_list_record[$stand_img]);
					if((@$stand_list_column[1]!='')&&
					   (@$stand_list_column[2]>0)&&
					   (@$stand_list_column[3]>0)){
						// 画像処理
						$base_image_name=$room_dir.$stand_list_column[1];
						$copy_image_name='char_'.$principal.'_'.basename($base_image_name);
						if(!file_exists($room_dir.$copy_image_name)){
							if(file_exists($base_image_name)){
								if(!copy($base_image_name,$room_dir.$copy_image_name)){
									$stand_flg=false;
								}
							}else{
								$stand_flg=false;
							}
						}
						if($stand_flg==true){
							$stand_p_name='stand'.$stand_pos;
							if(isset($room_xml->head->{$stand_p_name})){
								$room_xml->head->{$stand_p_name}->eimg=$copy_image_name;
								$room_xml->head->{$stand_p_name}->ew=$stand_list_column[2];
								$room_xml->head->{$stand_p_name}->eh=$stand_list_column[3];
								$room_xml->head->{$stand_p_name}->enm=$say_man;
								$room_xml->head->{$stand_p_name}->etm=time();
							}else{
								$BStand=$room_xml->head->addChild($stand_p_name);
								$BStand->addChild('eimg',$copy_image_name);
								$BStand->addChild('ew',$stand_list_column[2]);
								$BStand->addChild('eh',$stand_list_column[3]);
								$BStand->addChild('enm',$say_man);
								$BStand->addChild('etm',time());
							}
						}
					}
				}
			}
		}
	}
	if($save_xml_flag!=false){
		// ルームの保存
		if(saveRoomXmlFile($room_xml,$room_file,$principal,$nick_name,$observer_flag)){
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