<?php
$result_comment=''; // ボイスボット結果コメント
$command_error_mes=''; // エラー原因コメント
if(preg_match('/^([0-9]+) ([\-+<>=!@0-9adkxs]+) (.+)$/i',$match_comment,$match)){
	// 繰り返し判定
	$repeat_flag=(int)$match[1];
	if($repeat_flag==1){
		$repeat_flag=0;
	}elseif($repeat_flag<=0){
		$repeat_flag=0;
		$result_comment='エラー';
		$command_error_mes='(繰り返す数は2～'.REPEAT_ROLL_LIMIT.'で設定してください)';
	}elseif($repeat_flag>REPEAT_ROLL_LIMIT){
		$repeat_flag=0;
		$result_comment='エラー';
		$command_error_mes='(繰り返す数は2～'.REPEAT_ROLL_LIMIT.'で設定してください)';
	}
	//　ダイスコマンド記憶
	$chipcommand=(string)$match[2]; //処理別コマンド一部
	$typecommand=(int)$match[1].' '.$chipcommand; // ダイスコマンド
	$chipcomment=(string)$match[3];
	$roll_process=true;
}elseif(preg_match('/^([0-9]+) ([\-+<>=!@0-9adkxs]+)$/i',$match_comment,$match)){
	// 繰り返し判定
	$repeat_flag=(int)$match[1];
	if($repeat_flag==1){
		$repeat_flag=0;
	}elseif($repeat_flag<=0){
		$repeat_flag=0;
		$result_comment='エラー';
		$command_error_mes='(繰り返す数は2～'.REPEAT_ROLL_LIMIT.'で設定してください)';
	}elseif($repeat_flag>REPEAT_ROLL_LIMIT){
		$repeat_flag=0;
		$result_comment='エラー';
		$command_error_mes='(繰り返す数は2～'.REPEAT_ROLL_LIMIT.'で設定してください)';
	}
	//　ダイスコマンド記憶
	$chipcommand=(string)$match[2]; //処理別コマンド一部
	$typecommand=(int)$match[1].' '.$chipcommand; // ダイスコマンド
	$roll_process=true;
}elseif(preg_match('/^([\-+<>=!@0-9adkxs]+) (.+)$/i',$match_comment,$match)){
	//　ダイスコマンド記憶
	$typecommand=(string)$match[1]; // ダイスコマンド
	$chipcommand=$typecommand; //処理別コマンド一部
	$chipcomment=(string)$match[2];
	$roll_process=true;
}elseif(preg_match('/^([\-+<>=!@0-9adkxs]+)$/i',$match_comment,$match)){
	//　ダイスコマンド記憶
	$typecommand=(string)$match[1]; // ダイスコマンド
	$chipcommand=$typecommand; //処理別コマンド一部
	$roll_process=true;
}
if($roll_process==true){
	// 初期値
	$rollcommand=0; // 0=ダイスコマンドなし 1=汎用ダイス 2=個別仕様
	$success_or_failure_flag=0; // 1:>= 2:<= 3:<> 4:> 5:< 6:=
	$success_or_failure_value=0; // 合否の値
	// シークレットダイス判定
	$match=array();
	if(preg_match('/^s(.+)/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$secret_dice_flag=true;
	}
	// 合否判定
	$match=array();
	if(preg_match('/(.+)>=([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=1;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)=>([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=1;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)<=([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=2;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)=<([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=2;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)<>([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=3;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)><([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=3;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)!=([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=3;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)>([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=4;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)<([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=5;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)==([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=6;
		$success_or_failure_value=(int)$match[2];
	}elseif(preg_match('/(.+)=([0-9]+)$/i',$chipcommand,$match)){
		$chipcommand=(string)$match[1];
		$success_or_failure_flag=6;
		$success_or_failure_value=(int)$match[2];
	}
	// ダイス、加減算処理
	// 個別仕様ダイス処理
	if(file_exists(DIR_ROOT.'s/db/dicebot_'.basename($call_dicebot).'.php')){
		require(DIR_ROOT.'s/db/dicebot_'.basename($call_dicebot).'.php');
	}
	// 汎用ダイス処理
	if($rollcommand==0){
		require(DIR_ROOT.'s/db/dicebot_basic.php');
	}
	if($rollcommand==0){
		$secret_dice_flag=false;
		$comment=htmlspecialchars($origin_comment);
	}
}