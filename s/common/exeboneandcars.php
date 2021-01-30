<?php
require(DIR_ROOT.'s/common/bcdiceapiv2class.php');
function getDiceSR($ro_res_rand,&$dammy_dice){
	foreach((array)$ro_res_rand as $key => $rail_value){
		if(is_array($rail_value['value'])){
			getDiceSR($rail_value,$dammy_dice);
		}else{
			$result_rand=0;
			if(isset($rail_value['value'])){
				$result_rand=$rail_value['value'];
			}
			$result_rmax=100;
			if(isset($rail_value['sides'])){
				$result_rmax=$rail_value['sides'];
			}
			$dammy_dice[]=array($result_rmax,$result_rand);
		}
	}
}

$typecommand='';
$repeat_flag=0;
$dice=array();
$dice_result=0;
$chipcomment='';
$m_comment_array=explode(' ',$match_comment);
$mca_count=count($m_comment_array);

if(1<$mca_count){
	// 配列一つ目を繰り返しかチェック
	$command_key=0;
	if(preg_match('/^([0-9]+)$/i',$m_comment_array[0],$match)){
		$repeat_flag=(int)$match[1];
		if($repeat_flag<=0){
			$repeat_flag=0;
			$command_error_mes='(繰り返す数は2～'.REPEAT_ROLL_LIMIT.'で設定してください)';
		}elseif($repeat_flag>REPEAT_ROLL_LIMIT){
			$repeat_flag=0;
			$command_error_mes='(繰り返す数は2～'.REPEAT_ROLL_LIMIT.'で設定してください)';
		}
		$command_key++;
	}
	$chipcommand=(string)$m_comment_array[$command_key];
	for($i=($command_key+1);$i<$mca_count;$i++){
		if($i>($command_key+1)){
			$chipcomment.=' ';
		}
		$chipcomment.=(string)$m_comment_array[$i];
	}
}elseif(0<$mca_count){
	$chipcommand=(string)$m_comment_array[0];
	$chipcomment='';
}
$start_count=0;
$total_repet=0;
$result_onsenbac=array();
$result_onsenbac_msg=array();
$result_onsenbac_array=array();
$result_rand_msg=array();
if(empty($bac_game_dicebot)){
	$bac_game_dicebot='DiceBot';
}
// 安全処理
$chipcommand=htmlspecialchars_decode($chipcommand);
if(strpos($chipcommand,'\\')!==false){
	$chipcommand='エラー';
}elseif(strpos($chipcommand,'|')!==false){
	$chipcommand='エラー';
}elseif(strpos($chipcommand,';')!==false){
	$chipcommand='エラー';
}elseif(strpos($chipcommand,'"')!==false){
	$chipcommand='エラー';
}elseif(strpos($chipcommand,'`')!==false){
	$chipcommand='エラー';
}elseif(substr($chipcommand,0,1)=='$'){
	$chipcommand='エラー';
}elseif($start_count=preg_match_all('/\[([0-9]+)\.\.\.([0-9]+)\]/i',$chipcommand,$sc_macth)){
	if(0<$start_count){
		for($i=0;$i<$start_count;$i++){
			$scope_error_flag=false;
			$scope_min=1;
			if(isset($sc_macth[1][$i])){
				$scope_min=$sc_macth[1][$i];
			}
			$scope_max=2;
			if(isset($sc_macth[2][$i])){
				$scope_max=$sc_macth[2][$i];
			}
			if($scope_min<1){
				$scope_error_flag=true;
				$command_error_mes='（ランダム数値の埋め込み[Min...Max]のMinに0以下の数値は使えません）'; // 出力されません
			}elseif($scope_min>=$scope_max){
				$scope_error_flag=true;
				$command_error_mes='（ランダム数値の埋め込み[Min...Max]のMaxにMin以下の数値は使えません）'; // 出力されません
			}
			if($scope_error_flag==true){
				$chipcommand='エラー';
				break;
			}
		}
	}
}
if($start_count===false){
	$start_count=0;
}
do{
	$result_onsenbac[$total_repet]=null;
	if(!empty($chipcommand)){
		$run_ruby_flag=true;
		if(preg_match('/^choice\[.+\]$/i',$chipcommand)){
			// 何もしない
		}elseif(strlen($chipcommand)!==mb_strlen($chipcommand)){
			$run_ruby_flag=false;
		}
		if($run_ruby_flag==true){
			$result_onsenbac[$total_repet]=classBCDiceAPIv2::diceRoll(BAC_ENDPOINT,$bac_game_dicebot,$chipcommand);
		}
	}
	$result_onsenbac_msg[$total_repet]='';
	$result_rand_msg[$total_repet]='';
	$result_onsenbac_array[$total_repet]=array();
	if(isset($result_onsenbac[$total_repet]['secret'])&&
	   isset($result_onsenbac[$total_repet]['rands'])&&
	   isset($result_onsenbac[$total_repet]['text'])){
		   
		$result_onsenbac[$total_repet]['text']=str_replace(array("\r\n","\n","\r"),'<br>',$result_onsenbac[$total_repet]['text']);
		
		if($result_onsenbac[$total_repet]['secret']==true){
			$secret_dice_flag=true;
		}
		if($result_onsenbac[$total_repet]['text']!='1'){
			$nonroll_dice_flag=true;
			$result_onsenbac_msg[$total_repet]=$result_onsenbac[$total_repet]['text'];
			// 結果文字列を分割
			if(strpos($result_onsenbac_msg[$total_repet],'＞')!==false){
				$result_onsenbac_array[$total_repet]=explode('＞',$result_onsenbac_msg[$total_repet]);
			}else{
				$result_onsenbac_array[$total_repet]=explode('→',$result_onsenbac_msg[$total_repet]);
			}
			// リール用の結果を抽出
			$roll_result=false;
			$roa_count=count($result_onsenbac_array[$total_repet]);
			$result_area=(($roa_count-1)<0?0:($roa_count-1));
			$result_onsenbac_array[$total_repet][$result_area]=trim($result_onsenbac_array[$total_repet][$result_area]);
			if(preg_match('/^-?[0-9]+$/',$result_onsenbac_array[$total_repet][$result_area])){ //加算ロール1Dのみ
				$roll_result=abs($result_onsenbac_array[$total_repet][$result_area]);
			}else{
				$get_result_flag=false;
				for($i=$result_area;$i>1;$i--){
					$result_onsenbac_array[$total_repet][$i]=trim($result_onsenbac_array[$total_repet][$i]);
					if(preg_match('/-?([0-9]+)/',$result_onsenbac_array[$total_repet][$i],$dr_match)){
						$roll_result=$dr_match[1];
						$get_result_flag=true;
						break;
					}
					unset($dr_match);
				}
			}
			// ダイス別の結果を抽出
			$dammy_dice=array();
			getDiceSR($result_onsenbac[$total_repet]['rands'],$dammy_dice);
			if(0<count($dammy_dice)){
				$i=0;
				foreach($dammy_dice as $dd_key => $dd_value){
					if($i>=$start_count){
						$dice[]=$dd_value;
						$nonroll_dice_flag=false;
					}
					$i++;
				}
			}
			if($total_repet==0){ // 初回のみの処理
				if($roll_result===false){
					$first_roll_result=0;
					if(isset($dice[0][0])){
						foreach($dice as $dice_key => $dice_value){
							if(isset($dice_value[1])){
								$first_roll_result=$first_roll_result+(int)$dice_value[1];
							}
						}
					}
					$dice_result=$first_roll_result;
				}else{
					$dice_result=$roll_result;
				}
			}
			$dice_roll_flag=true;
		}else{
			$result_onsenbac_msg[$total_repet]='';
			$dice_roll_flag=false;
		}
	}else{
		$dice_roll_flag=false;
	}
	unset($match);
	if($command_error_mes!=''){
		break;
	}
	$total_repet++;
}while($total_repet<$repeat_flag);
if($dice_roll_flag==true){
	if($command_error_mes==''){
		// 複数回コマンド
		if(1<count($result_onsenbac_array)){
			$comment=$call_name.'さんの'.$chipcomment.'ロール('.$repeat_flag.' '.$chipcommand.')';
			for($j=0;$j<count($result_onsenbac_array);$j++){
				for($i=0;$i<count($result_onsenbac_array[$j]);$i++){
					if($i==0){
						$comment.='<br>→ '.($j+1).'回目： '.$bac_game_dicebot.' '.$result_onsenbac_array[$j][$i];
					}else{
						$comment.=' → '.$result_onsenbac_array[$j][$i];
					}
				}
			}
		// 1コマンド
		}else{
			$comment=$call_name.'さんの'.$chipcomment.'ロール('.$chipcommand.')';
			for($i=0;$i<count($result_onsenbac_array[0]);$i++){
				if($i==0){
					$comment.='<br>→ '.$bac_game_dicebot.' '.$result_onsenbac_array[0][$i];
				}else{
					$comment.=' → '.$result_onsenbac_array[0][$i];
				}
			}
		}
	}else{
		$comment=$call_name.'さんの'.$chipcomment.'ロール('.$chipcommand.') → エラー'.$command_error_mes;
		$dice_roll_flag=false;
	}
	$system_comment_flag=true;
}