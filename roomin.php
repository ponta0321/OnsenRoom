<?php 
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT');
session_start();
require('./s/common/core.php');
require(DIR_ROOT.'s/common/function.php');
require(DIR_ROOT.'s/common/exefunction.php');
function checkAvailablePassString($pass_string,$pass_min_lenght=0,$pass_man_lenght=100){
	if(preg_match('/[0-9a-zA-Z]+/',$pass_string)){
		$pass_lenght=mb_strlen($pass_string);
		if($pass_min_lenght<=$pass_lenght){
			if($pass_man_lenght>=$pass_lenght){
				return true;
			}
		}
	}
	return false;
}
$login_flag=false;
$principal_id='';
if(!empty($_GET['pr'])){
	$principal_id=$_GET['pr'];
	$login_flag=true;
}elseif(!empty($_POST['principal_id'])){
	$principal_id=$_POST['principal_id'];
	$login_flag=true;
}
if(!empty($_POST['room_token'])){
	setcookie('room_token',$_POST['room_token'],time()+86400);
}
if($login_flag===true){
	if(checkAvailablePassString($principal_id)===false){
		header('Location: '.$lobby_url.'roomout.php?err=2') ;
		exit;
	}
}
$room_type='pc';
if(!empty($_GET['rt'])){
	$room_type=$_GET['rt'];
}elseif(!empty($_POST['room_type'])){
	$room_type=$_POST['room_type'];
}
$nick_name=$principal_id;
if(!empty($_GET['nm'])){
	$nick_name=$_GET['nm'];
}elseif(!empty($_POST['nick_name'])){
	$nick_name=$_POST['nick_name'];
}
$lobby_url=URL_ROOT;
if(!empty($_GET['lu'])){
	$lobby_url=$_GET['lu'];
}elseif(!empty($_POST['lobby'])){
	$lobby_url=$_POST['lobby'];
}
$lobby_url=rtrim($lobby_url,'/').'/';
$room_name='';
if(!empty($_GET['rn'])){
	$room_name=basename($_GET['rn']);
}elseif(!empty($_POST['room_name'])){
	$room_name=basename($_POST['room_name']);
}else{
	header('Location: '.$lobby_url.'roomout.php?err=4') ;
	exit;
}
$room_dir=DIR_ROOT.'r/n/'.$room_name.'/';
$room_file=$room_dir.'data.xml';
$room_mirror_file=$room_dir.'data-mirror.xml';
if(!file_exists($room_file)){
	header('Location: '.$lobby_url.'roomout.php?err=1') ;
	exit;
}
if(!checkPlayerCapacity($serv_delay_facter)){
	header('Location: '.$lobby_url.'roomout.php?err=9') ;
	exit;
}
$load_avg=checkLoadAvg(CPU_CORE);
if($load_avg[0]>ALLOWABLE_LOAD_LIMIT){
	header('Location: '.$lobby_url.'roomout.php?err=10') ;
	exit;
}
$observer_flag=0; // 0=参加者 1=見学者
if(!empty($_GET['ro'])){
	$observer_flag=$_GET['ro'];
}elseif(!empty($_POST['room_observer'])){
	$observer_flag=$_POST['room_observer'];
}
if($observer_flag!=1){
	$observer_flag=0;
}
$exfilelock=new classFileLock($room_dir,$room_name.'_lockfile',5);
if($exfilelock->flock($room_dir)){
	if(($xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
		$exfilelock->unflock($room_dir);
		header('Location: '.$lobby_url.'roomout.php?err=8') ;
		exit;
	}
	// ルームデータ補完
	complementRoomData($xml);
	// ルームデータ旧→新コンバート
	convertRoomData($xml);
	// 公式鯖のみの処理
	if(file_exists(DIR_ROOT.'s/common/kousiki.php')){
		@include(DIR_ROOT.'s/common/kousiki.php');
	}
	if(((int)$xml->head->tour==1)&&($observer_flag==1)){
		$exfilelock->unflock($room_dir);
		header('Location: '.$lobby_url.'roomout.php?err=6') ;
		exit;
	}
	// 本人の情報を取得
	$playerid_for_count='_guest';
	if(!empty($principal_id)){
		$playerid_for_count=$principal_id;
	}else{
		if($observer_flag!=1){
			$exfilelock->unflock($room_dir);
			header('Location: '.$lobby_url.'roomout.php?err=2') ;
			exit;
		}else{
			$playerid_for_count=getClientIP();
			if(empty($nick_name)){
				$nick_name='ゲスト';
			}
		}
	}
	$room_pass='';
	if(!empty($_GET['rp'])){
		$room_pass=$_GET['rp'];
	}elseif(!empty($_POST['room_pass'])){
		$room_pass=$_POST['room_pass'];
	}
	if(($xml->head->password!=$room_pass)&&($observer_flag!=1)){
		$exfilelock->unflock($room_dir);
		header('Location: '.$lobby_url.'roomout.php?err=3') ;
		exit;
	}
	if($observer_flag!=1){
		$say_time=time();
		$say_man='システム';
		$contentComment=$xml->body->addChild('content');
		$contentComment->addAttribute('id',creatChatMsgId());
		$contentComment->addChild('date',$say_time);
		$comment=$nick_name.'さんが、入室しました。';
		$contentComment->addChild('text',htmlentities($comment,ENT_XML1));
		$contentComment->addChild('chat_color','#909090');
		$contentComment->addChild('ctyp',1);
		$contentComment->addChild('author',htmlentities($say_man));
	}
	// ルームの保存
	if(saveRoomXmlFile($xml,$room_file,$playerid_for_count,$nick_name,$observer_flag)){
		// phpログ保存
		@include(DIR_ROOT.'s/common/exewritelog.php');
	}else{
		$exfilelock->unflock($room_dir);
		header('Location: '.$lobby_url.'roomout.php?err=8') ;
		exit;
	}
	// 人数確認ファイルの保存 add.2016.11.07
	$room_list=new classRoomList;
	$room_list->update($room_name,(string)$xml->head->login_players,(string)$xml->head->game_dicebot);
}else{
	$exfilelock->unflock($room_dir);
	header('Location: '.$lobby_url.'roomout.php?err=8') ;
	exit;
}
$exfilelock->unflock($room_dir);

// ルームへプレイヤー情報を転送
$_SESSION['room_name']=$room_name;
$_SESSION['room_pass']=$room_pass;
$_SESSION['principal_id']=$principal_id;
$_SESSION['lobby_url']=$lobby_url;
$_SESSION['nick_name']=$nick_name;
$_SESSION['observer_flag']=$observer_flag;
if($room_type=='sp'){
	header('Location: '.URL_ROOT.'room-sp.php');
}else{
	header('Location: '.URL_ROOT.'room.php');
}
exit;