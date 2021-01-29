<?php
require('../s/common/core.php');
require(DIR_ROOT.'s/common/function.php');
require(DIR_ROOT.'s/common/exefunction.php');

$room_id='';
$room_dir='';
$room_file='';
$room_mirror_file='';
if(!empty($_POST['room_id'])){
	$room_id=basename($_POST['room_id']);
	$room_dir=DIR_ROOT.'r/n/'.$room_id.'/';
	$room_file=$room_dir.'data.xml';
	$room_mirror_file=$room_dir.'data-mirror.xml';
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
}elseif(!empty($_SERVER["REMOTE_ADDR"])){
	$principal_ip=$_SERVER["REMOTE_ADDR"];
}

$characterlist_array=array();
$json_data=getCharListFromLobbyServer($principal_id,$principal_ip,(empty($_COOKIE['room_token'])?'':$_COOKIE['room_token']));
if(!isset($json_data['error'])){
	$c_character=new classCharacter;
	$characterlist_array=$c_character->putCharListFLSInCharArray(
		$room_id,
		$principal_id,
		$json_data
	);
	// ルームファイルの読み込み
	if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
		echo json_encode(array(
			'error'=>'failed_to_load_room',
			'error_description'=>'ルームの読み込みに失敗しました。'
		));
		exit;
	}
	// ロビーから取得したキャラ一覧のキャラで現在ルームのキャラ一覧に表示されたキャラデータだけ再取得する
	if(isset($room_xml->body->character)){
		foreach($room_xml->body->character as $room_chara_column){
			if($room_chara_column->char_owner==$principal_id){
				foreach($characterlist_array as $load_chara_column){
					if($room_chara_column->char_id==$load_chara_column[0]){
						if($c_character->saveDataFromLobby(
							$room_id,
							getCharDataFromLobbyServer(
								$room_chara_column->char_id,
								$principal_id,
								$principal_ip,
								(empty($_COOKIE['room_token'])?'':$_COOKIE['room_token'])
							)
						)){
							// キャラクター画像データの読み込み
							$image_list=array();
							if(!empty($c_character->image)){
								$image_list[]=$c_character->image;
							}
							foreach($c_character->stand as $value){
								if(!empty($value[1])){
									$image_list[]=$value[1];
								}
							}
							foreach($image_list as $value){
								$c_character->getImageFromLobby($value,$room_id,$principal_id);
							}
						}
						break;
					}
				}
			}
		}
	}
}else{
	if(isset($json_data['error_description'])){
		echo json_encode(array(
			'error'=>$json_data['error'],
			'error_description'=>'キャラ一覧の取得に失敗しました。（'.$json_data['error_description'].'）'
		));
	}else{
		echo json_encode(array(
			'error'=>'failed_to_get_character_list',
			'error_description'=>'ロビーからキャラ一覧の取得ができませんでした。'
		));
	}
	exit;
}
echo $c_character->convertCharListJsonFromArray($characterlist_array);