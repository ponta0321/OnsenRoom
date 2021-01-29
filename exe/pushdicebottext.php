<?php
require('../s/common/core.php');
$game_key='g99';
$result='';
if(!empty($_POST['game_key'])) $game_key=$_POST['game_key'];
// BCDice
if(strpos($game_key,'bac_')!==false && !empty(BAC_ENDPOINT)){
	$bac_game_dicebot=substr($game_key,4);
	require(DIR_ROOT.'s/common/bcdiceapiv2class.php');
	require(DIR_ROOT.'s/list/bac_gamelist.php');
	$result=$default_preset_comment;
	if(isset($bac_gamelist[$game_key]) && $bac_game_dicebot!='DiceBot'){
		$result.="\n【".$bac_gamelist[$game_key][0]."用】\n";
		$result_dhm=classBCDiceAPIv2::info(BAC_ENDPOINT,$bac_game_dicebot);
		// 出力された説明文を表示する
		if(isset($result_dhm['help_message'])){
			$result.=$result_dhm['help_message'];
		// 出力されたエラーを表示する
		}else{
			$result.='<span style="color:#F66;">ダイスボット説明の取得時にエラーが発生しました。'.(isset($result_dhm['reason'])?'｛'.$result_dhm['reason'].'｝':'').'</span>';
		}
	}
// オンセンdb
}else{
	$dicebot_textlist=array();
	require(DIR_ROOT.'s/list/dicebot_textlist.php');
	if(isset($dicebot_textlist[$game_key])){
		$result=$dicebot_textlist[$game_key][1];
	}else{
		$result=$dicebot_textlist['g99'][1];
	}
}
echo $result;