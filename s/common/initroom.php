<?php 
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT');
session_start();
require('core.php');
require('function.php');
require('exefunction.php');
// ゲームタイプリストの読み込み
$global_trpg_name=array();
if(file_exists(DIR_ROOT.'s/list/trpg_sys_list.php')) @include(DIR_ROOT.'s/list/trpg_sys_list.php');
$global_trpg_name['g99']='その他';
$now_time=time();
$lobby_url=LOBBY_URL_ROOT;
if(isset($_SESSION['lobby_url'])){
	$lobby_url=$_SESSION['lobby_url'];
}
$room_type='pc';
if(strpos(basename($_SERVER["REQUEST_URI"]),'room-sp.php')!==false){
	$room_type='sp';
}
$player_ip=getClientIP();
// ルームファイルチェック
$base_room_file='';
if(isset($_POST['room_file'])){
	$base_room_file=basename($_POST['room_file']);
}elseif(isset($_SESSION['room_name'])){
	$base_room_file=basename($_SESSION['room_name']);
}else{
	header('Location: '.$lobby_url.'roomout.php?err=4') ;
	exit;
}
// ルームファイル存在チェック
$room_dir=DIR_ROOT.'r/n/'.$base_room_file.'/';
$room_file=$room_dir.'data.xml';
$room_mirror_file=$room_dir.'data-mirror.xml';
if(!file_exists($room_file)){
	header('Location: '.$lobby_url.'roomout.php?err=1') ;
	exit;
}
// プレイヤーIDチェック
$principal_id='';
if(isset($_SESSION['principal_id'])){
	$principal_id=$_SESSION['principal_id'];
}
if(empty($principal_id)){
	$login_flag=false;
}else{
	$login_flag=true;
}
// ニックネームチェック
$nick_name='';
if(!empty($_SESSION['nick_name'])){
	$nick_name=$_SESSION['nick_name'];
}else{
	$nick_name=$principal_id;
}
// 参加者、見学者チェック
$observer_flag=1;
if(isset($_SESSION['observer_flag'])){
	$observer_flag=$_SESSION['observer_flag'];
}
if($observer_flag!=1){
	$observer_flag=0;
}
// ログインチェック
$playerid_for_count=$player_ip;
if($login_flag==true){
	$playerid_for_count=$principal_id;
}elseif($observer_flag!=1){
	header('Location: '.$lobby_url.'roomout.php?err=2') ;
	exit;
}
// パスワードチェック
$room_pass='';
if(isset($_POST['room_pass'])){
	$room_pass=$_POST['room_pass'];
}elseif(isset($_SESSION['room_pass'])){
	$room_pass=$_SESSION['room_pass'];
}
// ルームファイルのロード
$xml=autoloadXmlFile($room_file,$room_mirror_file);
if(($xml->head->password!=$room_pass)&&($observer_flag!=1)){
	header('Location: '.$lobby_url.'roomout.php?err=3') ;
	exit;
}
// post/cookie情報の取得
if(isset($_POST['music_volume'])){
	$music_volume=$_POST['music_volume'];
}elseif(isset($_COOKIE['music_volume'])){
	$music_volume=$_COOKIE['music_volume'];
}else{
	$music_volume=10;
}
if(isset($_COOKIE['chat_font_size'])){
	$chat_font_size=$_COOKIE['chat_font_size'];
}else{
	$chat_font_size='10px';
}
if(isset($_COOKIE['chat_sound'])){
	$chat_sound=$_COOKIE['chat_sound'];
}else{
	$chat_sound='false';
}
if(isset($_COOKIE['chat_sound_vol'])){
	$chat_sound_vol=$_COOKIE['chat_sound_vol'];
}else{
	$chat_sound_vol=20;
}
if(isset($_COOKIE['chat_color'])){
	$chat_color=$_COOKIE['chat_color'];
}else{
	$chat_color='#000000';
}
if(isset($_COOKIE['bn_notdisplay'])){
	$bn_notdisplay=$_COOKIE['bn_notdisplay'];
}else{
	$bn_notdisplay=0;
}
// 見学者書き込み不可フラグ
$observer_write=0;
if(isset($xml->head->tour)){
	if($xml->head->tour!=0){
		$observer_write=1;
	}elseif(isset($xml->head->observe_write)){
		$observer_write=(int)$xml->head->observe_write;
	}
}elseif(isset($xml->head->observe_write)){
	$observer_write=(int)$xml->head->observe_write;
}
$prin_memo=array('','','','','','');
for($i=1;$i<6;$i++){
	if(isset($_COOKIE['pri_memo'.$i.'_'.$base_room_file])){
		$prin_memo[$i]=$_COOKIE['pri_memo'.$i.'_'.$base_room_file];
	}else{
		$prin_memo[$i]='';
	}
}
$count_participant=0;
$count_observer=0;
if(isset($xml->head->name)){
	$room_name=(string)$xml->head->name;
}else{
	$room_name='無名のルーム';
}
if(isset($xml->head->game_boardwidth)){
	$game_boardwidth=(int)$xml->head->game_boardwidth;
}else{
	$game_boardwidth=17;
}
if(isset($xml->head->game_boardheight)){
	$game_boardheight=(int)$xml->head->game_boardheight;
}else{
	$game_boardheight=20;
}
$game_board_bgimg_width=$game_boardwidth*32;
$game_board_bgimg_height=$game_boardheight*32;
$voice_invite_code=-1;
if(isset($xml->head->voice_invite_code)){
	$voice_invite_code=(string)$xml->head->voice_invite_code;
}
$game_syncboardsize=0;
if(isset($xml->head->game_syncboardsize)){
	$game_syncboardsize=(int)$xml->head->game_syncboardsize;
	if($game_syncboardsize!=0){
		$game_board_bgimg_width='auto';
		$game_board_bgimg_height='auto';
	}
}
if($game_boardwidth<17){
	$game_boardwidth=17;
}
if($game_boardheight<20){
	$game_boardheight=20;
}
$first_play_music='';
if(isset($xml->head->game_music)){
	$first_play_music=(string)$xml->head->game_music;
}
if(!empty($first_play_music)){
	$first_play_music=TRANSFER_PROTOCOL.preg_replace('/^https?:/i','',$first_play_music);
}else{
	if(file_exists(DIR_ROOT.'sounds/se02.mp3')){
		$first_play_music=URL_ROOT.'sounds/se02.mp3';
	}
}
// 参加者、見学者のカウント
if(isset($xml->head->login_players)){
	$login_player_string='';
	$login_player_list_exist=false;
	$login_player_array=array();
	$login_player_list=explode('^',(string)$xml->head->login_players);
	foreach((array)$login_player_list as $login_player_record){
		$login_player_data=explode('|',$login_player_record);
		if(!empty($login_player_data[0])){
			$login_player_array[]=$login_player_data;
			$login_player_list_exist=true;
		}
	}
	if($login_player_list_exist==true){
		$login_player_data=array();
		foreach($login_player_array as $lpa_key => $login_player_data){
			if($login_player_data[0]==$playerid_for_count){
				// 自分をカウントしない
			}elseif(($now_time-(int)$login_player_data[1])<=3600){
				if($login_player_data[2]==1){
					$count_observer++;
				}else{
					$count_participant++;
				}
			}
		}
	}
}
if($observer_flag==1){
	$count_observer++;
}else{
	$count_participant++;
}
// ダイスボット説明リストの読み込み
if(!isset($xml->head->game_dicebot)){
	$xml->head->addChild('game_dicebot','g99');
}
$default_preset_comment='';
$dicebot_textlist=array();
if(file_exists(DIR_ROOT.'s/list/dicebot_textlist.php')){
	@include(DIR_ROOT.'s/list/dicebot_textlist.php');
}
if(file_exists(BAC_ROOT.'src/bcdiceCore.rb')){
	@include(DIR_ROOT.'s/list/bac_gamelist.php');
}else{
	if(strpos((string)$xml->head->game_dicebot,'bac_')!==false){
		$xml->head->game_dicebot='g99';
	}
}
if(empty($xml->head->game_mapchip)){
	$game_mapchip_img=URL_DEFAULT_MAPCHIP;
}else{
	$game_mapchip_img=(string)$xml->head->game_mapchip;
}
$c_character=new classCharacter;
if($observer_flag==0){
	// TRPGシステムリスト及びキャラシテンプレートリストの更新チェック
	getTrpgSystemInfoFromLobbyServer($last_update_global_trpg_name,$global_trpg_name);
	// 持ちキャラのロード
	if(($characterlist_array=$c_character->loadCharacterList($base_room_file,$principal_id))===false){
		$characterlist_array=$c_character->putCharListFLSInCharArray($base_room_file,$principal_id,getCharListFromLobbyServer($principal_id,$player_ip));
	}
	// シナリオセットリストのロード
	$scenariosetlist_array=makeSSlistArrayFromJson(getSSListFromLobbyServer($principal_id,$player_ip));
}else{
	$characterlist_array=array();
	$scenariosetlist_array=array();
}
// カードセットリストのロード
$cardset_list_array=load2ColumsCsvFile(DIR_ROOT.'s/list/cardset_list.txt','xml');
// 背景画像リストのロード
$backimage_list_array=load2ColumsCsvFile(DIR_ROOT.'s/list/backimage_list.txt',array('gif','jpg','jpeg','png','bmp'));
// マップチップリストのロード
$mapchip_list_array=load2ColumsCsvFile(DIR_ROOT.'s/list/mapchip_list.txt',array('gif','jpg','jpeg','png','bmp'),false);
array_unshift($mapchip_list_array,array(URL_DEFAULT_MAPCHIP,'デフォルト')); // デフォルト・マップチップ
// BGMリストのロード
$music_list_array=load2ColumsCsvFile(DIR_ROOT.'s/list/music_list.txt',array('wav','webm','mp3','ogg','aac','flac'));
// コマリストのロード
$chessman_list=loadChessmanFile();
?>
<script>
const CONST_URL_ROOT='<?=rtrim(URL_ROOT,'/').'/';?>'; // 各ページ参照用URL
const CONST_ROBBY_URL_ROOT='<?=rtrim(LOBBY_URL_ROOT,'/').'/';?>'; // ホーム参照用URL
const CONST_DEFAULT_URL_MAPCHIP='<?=URL_DEFAULT_MAPCHIP;?>'; // マップチップ参照用URL
const CONST_ROOM_ID='<?=$base_room_file;?>'; // ルームID
const CONST_PRINCIPAL_ID='<?=$principal_id;?>'; // プレイヤーID
const CONST_NICKNAME='<?=$nick_name;?>'; // プレイヤーニックネーム
const CONST_ROOM_TYPE='<?=$room_type;?>'; // ルームタイプ
const CONST_VOICE_CODE='<?=$voice_invite_code;?>'; // ボイスコード
</script>