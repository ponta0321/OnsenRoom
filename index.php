<?php 
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT');
session_start();
require('./s/common/core.php');
require(DIR_ROOT.'s/common/function.php');
require(DIR_ROOT.'s/list/dicebot_textlist.php');
require(DIR_ROOT.'s/list/bac_gamelist.php');
function getRemainTime($l_time){
	if(1<=($l_time/86400)){
		$result=floor($l_time/86400).'日';
	}elseif(1<=($l_time/3600)){
		$result=floor($l_time/3600).'時間';
	}elseif(1<=($l_time/60)){
		$result=floor($l_time/60).'分';
	}elseif(0>$l_time){
		$result='期限切れ';
	}else{
		$result=$l_time.'秒';
	}
	return $result;
}
function changeAvailableWord($word){
	$ob_word_list=array(
		array('<','＜'),
		array('>','＞'),
		array('"','”'),
		array("'",'’'),
		array('&','＆'),
		array('/','／'),
		array('.','．'),
		array(',','，'),
		array('_','＿'));
	foreach($ob_word_list as $ob_word){
		$word=str_replace($ob_word[0],$ob_word[1],$word);
	}
	$word=preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/','',$word);
	return trim(mb_convert_kana($word,'s'));
}
function checkAvailablePassString($pass_string,$pass_min_lenght=0,$pass_man_lenght=100){
	if(preg_match('/^[0-9a-zA-Z]+$/',$pass_string)){
		$pass_lenght=mb_strlen($pass_string);
		if($pass_min_lenght<=$pass_lenght){
			if($pass_man_lenght>=$pass_lenght){
				return true;
			}
		}
	}
	return false;
}
$principal_id='';
if(isset($_POST['principal_id'])){
	$principal_id=$_POST['principal_id'];
}elseif(isset($_GET['pr'])){
	$principal_id=$_GET['pr'];
}elseif(isset($_SESSION['principal_id'])){
	$principal_id=$_SESSION['principal_id'];
}
$room_type='';
if(isset($_POST['room_type'])){
	$room_type=$_POST['room_type'];
}elseif(isset($_GET['rt'])){
	$room_type=$_GET['rt'];
}
if(($room_type!='pc')&&($room_type!='sp')){
	$room_type='pc';
	if(!empty($_SERVER['HTTP_USER_AGENT'])){
		$user_agent=$_SERVER['HTTP_USER_AGENT'];
		if((strpos($user_agent,'iPhone')!==false)||
		   (strpos($user_agent,'iPod')!==false)||
		   (strpos($user_agent,'Android')!==false)){
			$room_type='sp';
		}
	}
}
$nick_name='';
if(isset($_POST['nick_name'])){
	$nick_name=$_POST['nick_name'];
}elseif(isset($_GET['nm'])){
	$nick_name=$_GET['nm'];
}elseif(isset($_SESSION['nick_name'])){
	$nick_name=$_SESSION['nick_name'];
}
if(empty($nick_name)) $nick_name=$principal_id;
$lobby_url=URL_ROOT;
if(isset($_POST['lobby'])){
	$lobby_url=$_POST['lobby'];
}elseif(isset($_GET['lu'])){
	$lobby_url=$_GET['lu'];
}elseif(isset($_SESSION['lobby_url'])){
	$lobby_url=$_SESSION['lobby_url'];
}
$err=0;
if(isset($_GET['err'])) $err=$_GET['err'];
$state='';
if(isset($_POST['f_state'])) $state=$_POST['f_state'];
if(empty($err) && $state=='create_room'){
	if(empty($_POST['room_name'])){
		$err=101;
	}else{
		$room_name=changeAvailableWord($_POST['room_name']);
		if(empty($room_name)){
			$err=102;
		}
	}
	if(empty($principal_id)) $err=103;
	if(preg_match('/[^0-9a-zA-Z]/',$principal_id)) $err=105;
	$room_pass='';
	if(isset($_POST['room_pass'])){
		$room_pass=$_POST['room_pass'];
		if(empty($room_pass)){
		}elseif(checkAvailablePassString($room_pass)===false){
			$err=104;
		}
	}
	$principal_ip=getClientIP();
	$game_type='g99';
	if(isset($_POST['game_type'])) $game_type=$_POST['game_type'];
	$tao_flag=0; // 0=見学可(書込み可) 1=見学可(書込み不) 2=見学不可
	if(isset($_POST['tao_flag'])) $tao_flag=$_POST['tao_flag'];
	if($tao_flag==1){
		$tour_flag=0;
		$observe_write=1;
	}elseif($tao_flag==2){
		$tour_flag=1;
		$observe_write=1;
	}else{
		$tour_flag=0;
		$observe_write=0;
	}
	if(empty($err)){
		require(DIR_ROOT.'s/common/exefunction.php');
		if(createSessionRoom($room_name,$room_pass,$principal_id,$principal_ip,86400,$tour_flag,$error_msg,$game_type,$observe_write)!==false){
			header('Location:'.URL_ROOT.'roomin.php'.
				   '?rn='.urlencode(changeIDformRoomName($room_name)).
				   '&rp='.urlencode($room_pass).
				   '&rt='.urlencode($room_type).
				   '&lu='.urlencode($lobby_url).
				   '&pr='.urlencode($principal_id).
				   '&nm='.urlencode($nick_name).
				   '&ro=0'
			);
			exit;
		}else{
			$err=110;
		}
	}
}
$ra_load_flag=true;
$now_time=time();
$ra=array();
if(file_exists(DIR_ROOT.'r/roomlist.php')){
	if(include(DIR_ROOT.'r/roomlist.php')){
		if(1<count($ra)){
			foreach($ra as $sort_key => $sort_value){
				$sort_timestamp[$sort_key]=(int)$sort_value[7];
			}
			@array_multisort($sort_timestamp,SORT_DESC,SORT_NUMERIC,$ra);
			unset($sort_timestamp);
			unset($sort_key);
		}
	}else{
		$ra_load_flag=false;
	}
}
if(empty(BAC_ENDPOINT)){
	$bac_gamelist=$dicebot_textlist;
}else{
	$bac_gamelist=array_merge($bac_gamelist,$dicebot_textlist);
}
checkPlayerCapacity($serv_delay_facter);
list($load_avg_1min,$load_avg_5min,$load_avg_15min)=checkLoadAvg(CPU_CORE);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
	<meta name="keywords" content="<?=$global_page_keywords;?>">
	<meta name="description" content="<?=$global_page_description;?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$global_page_title;?></title>
	<meta name="description" content="<?=$global_page_description;?>">
	<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script type="text/javascript" charset="UTF-8" src="//code.jquery.com/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" charset="UTF-8" src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<style>
body{
	padding:0;
	margin:0;
}
h1,h2,h3{
	color:#FE7F00;
	margin:0;
}
table{
	border:1px #E3E3E3 solid;
	border-collapse:collapse;
	border-spacing:0;
}
tr:hover{
	background-color:#FF0;
	cursor:pointer;
}
th{
	padding:5px;
	border:#E3E3E3 solid;
	border-width:0 0 1px 1px;
	background:#F5F5F5;
	font-weight:bold;
	line-height:120%;
	text-align:center;
}
td{
	padding:5px;
	border:1px #E3E3E3 solid;
	border-width:0 0 1px 1px;
	text-align:center;
}
</style>
<body>
    <header>
        <div style="background-color:#FE7F00;border-bottom:1px solid #E54500;">
            <div style="width:90%;max-width:900px;min-width:160px;margin:0 auto;padding:4px 0 2px 0;">
                <a href="<?=($room_type=='pc'?LOBBY_URL_ROOT:LOBBY_URL_SP_ROOT);?>"><img src="<?=URL_ROOT;?>images/title_logo3.png" width="120" height="33" border="0" /></a>
			</div>
        </div>
    </header>
    <main>
        <div style="max-width:900px;min-width:160px;margin:0 auto;padding:10px;">
			<h2><?=SITE_TITLE;?></h2>
			<p><?=SITE_DESCRIPTION;?></p>
			<p style="text-align:right;font-weight:bold;"><a href="<?=($room_type=='pc'?LOBBY_URL_ROOT:LOBBY_URL_SP_ROOT);?>">ロビーサーバーに戻る</a></p>
            <h3>サーバー稼働状況</h3>
			<p>収容人数（<?=$serv_delay_facter.' / '.PLAYER_CAPACITY;?>人）</p>
			<div style="width:300px;height:10px;border:1px #AAA solid;margin:5px 0;"><?php
				$serv_delay_facter=($serv_delay_facter/PLAYER_CAPACITY);
				if($serv_delay_facter>1){
					$serv_delay_facter=1;
				}
				$clf_color='#00FF41';
				if($serv_delay_facter>0.8){
					$clf_color='#F80606';
				}elseif($serv_delay_facter>0.5){
					$clf_color='#FFF10F';
				}
				echo '<div style="width:'.($serv_delay_facter*100).'%;top:0;left:0;height:10px;background-color:'.$clf_color.';"></div>';
			?></div>
			<p>CPU負荷（<?=$load_avg_5min;?>%）</p>
			<div style="width:300px;height:10px;border:1px #AAA solid;margin:5px 0 20px 0;"><?php
				$cpu_load_facter=$load_avg_5min;
				if($cpu_load_facter>100){
					$cpu_load_facter=100;
				}
				$clf_color='#00FF41';
				if($cpu_load_facter>80){
					$clf_color='#F80606';
				}elseif($cpu_load_facter>50){
					$clf_color='#FFF10F';
				}
				echo '<div style="width:'.$cpu_load_facter.'%;top:0;left:0;height:10px;background-color:'.$clf_color.';"></div>';
			?></div>
			<h3>ルーム作成</h3>
			<p><input type="button" onClick="open_cr_dialog();" value="ルームを作成する"></p>
			<h3>ルーム一覧</h3>
			<table>
				<tr>
					<th>ルーム名</th>
					<th>ダイスボット</th>
					<th>見学</th>
					<th>パス</th>
					<th>人数</th>
					<th>残り時間</th>
					<th>作成日時</th>
				</tr>
				<?php
					$html_v='';
					foreach($ra as $room_value){
						if($room_value[9]>$now_time){
							$html_v.='<tr onClick="open_in_dialog(\''.$room_value[1].'\',\''.$room_value[2].'\',\''.$room_value[11].'\',\''.$room_value[12].'\');">';
							$html_v.='<td>'.$room_value[2].'</td>'; // ルーム名
							$html_v.='<td>'; // ダイスボット
							if(isset($bac_gamelist[$room_value[15]])){
								$html_v.=$bac_gamelist[$room_value[15]][0];
							}else{
								$html_v.='指定なし';
							}
							$html_v.='</td>';
							$html_v.='<td><img src="'.URL_ROOT.'images/m_icon'.($room_value[12]==0?'83':'82').'.png" width="20" height="20" border="0"/></td>'; // 見学
							$html_v.='<td><img src="'.URL_ROOT.'images/m_icon'.($room_value[11]==0?'85':'84').'.png" width="20" height="20" border="0"/></td>'; // パス
							if(($now_time-$room_value[8])<=3600){ // 人数
								$html_v.='<td>'.$room_value[13].'/'.$room_value[14].'</td>';
							}else{
								$html_v.='<td>0/0</td>';
							}
							$html_v.='<td>'.getRemainTime($room_value[9]-$now_time).'</td>'; // 残り時間
							$html_v.='<td>'.date('Y-m-d H:i',$room_value[7]).'</td>'; // 作成日時
							$html_v.='</tr>';
						}
					}
					if($html_v===''){
						echo '<tr><td colspan="7" style="color:#AAA;">ルームはありません。</td></tr>';
					}else{
						echo $html_v;
					}
				?>
			</table>
			<p style="text-align:right;font-weight:bold;"><a href="<?=($room_type=='pc'?LOBBY_URL_ROOT:LOBBY_URL_SP_ROOT);?>">ロビーサーバーに戻る</a></p>
			<div id="in_dialog" title="入場チェック">
				<form name="frm_roomin" action="<?=URL_ROOT;?>roomin.php" method="get">
					<p style="font-weight:bold;">ルーム名：<br><span id="span_rn"></span></p>
					<div><input type="radio" name="ro" id="input_obs_off" value="0" />参加する&nbsp;
						 <input type="radio" name="ro" id="input_obs_on" value="1" />見学する</div>
					<p id="div_password">ルームパスワード：<span style="font-size:small;color:blue;">（英数半角）</span><br><input type="text" name="rp" id="input_rp" maxlength="30" value="" /></p>
					<div style="display:<?=(empty($principal_id)?'block':'none');?>;bottom:0;border-top:#CCC 1px solid;">
						<p>プレイヤーID：<span style="font-size:small;color:blue;">（英数半角）</span><br><input type="text" name="pr" id="input_pr" style="ime-mode:disabled;" value="<?=$principal_id;?>" /></p>
						<p>ニックネーム：<br><input type="text" name="nm" id="input_nm" value="<?=$nick_name;?>" /></p>
					</div>
					<input type="hidden" name="rn" id="hidden_rn" value="" />
					<input type="hidden" name="rt" value="<?=$room_type;?>" />
					<input type="hidden" name="lu" value="<?=$lobby_url;?>" />
				</form>
			</div>
			<div id="cr_dialog" title="新しいルームを作る">
				<form name="frm_crroom" action="<?=URL_ROOT;?>index.php" method="post">
					<p>ダイスボット：<br><select name="game_type" style="max-width:200px;padding:3px 0;"><?php
						foreach((array)$bac_gamelist as $bacgl_key => $bacgl_value){
							echo '<option value="'.$bacgl_key.'" >'.$bacgl_value[0].'：';
							if(strpos($bacgl_key,'bac_')===false){
								echo 'ｵﾝｾﾝdb';
							}else{
								echo 'bac';
							}
							echo '</option>';
						}
					?></select></p>
					<p>ルーム名：<br><input type="text" name="room_name" value="" /></p>
					<p>パスワード：<span style="font-size:small;color:blue;">（英数半角／省略可）</span><br><input type="text" name="room_pass" style="ime-mode:disabled;" value="" /></p>
					<div>見学：<br>
						<input type="radio" name="ro" value="0" checked="checked" />可(書込み可&nbsp;
						<input type="radio" name="ro" value="0" />可(書込み不可<br>
						<input type="radio" name="ro" value="1" />不可</div>
					<div style="display:<?=(empty($principal_id)?'block':'none');?>;bottom:0;border-top:#CCC 1px solid;">
						<p>プレイヤーID：<span style="font-size:small;color:blue;">（英数半角）</span><br><input type="text" name="principal_id" style="ime-mode:disabled;" value="<?=$principal_id;?>" /></p>
						<p>ニックネーム：<br><input type="text" name="tao_flag" value="<?=$nick_name;?>" /></p>
					</div>
					<input type="hidden" name="room_type" value="<?=$room_type;?>" />
					<input type="hidden" name="lobby" value="<?=$lobby_url;?>" />
					<input type="hidden" name="f_state" value="create_room" />
				</form>
			</div>
			<div id="err_dialog" title="エラー">
				<p style="text-align:center;color:red;"><?php
					switch($err){
						case 1:
							echo 'そのルームは存在しません。';
							break;
						case 2:
							echo 'プレイヤーIDがないまたは無効なため、入場できません。';
							break;
						case 3:
							echo 'ルームパスワードが違います。';
							break;
						case 4:
							echo 'ルーム名を指定されていません。';
							break;
						case 5:
							echo '正規の入り口からルームに入ってください。';
							break;
						case 6:
							echo 'このルームは見学で入ることができません。';
							break;
						case 7:
							echo 'ルームとの通信が途絶えました。';
							break;
						case 8:
							echo 'ルームの読み込みに失敗しました。';
							break;
						case 9:
							echo 'サーバーの収容人数を超えるため、入場できませんでした。';
							break;
						case 10:
							echo 'サーバーが高負荷のため、入場できませんでした。';
							break;
						case 101:
							echo 'ルーム作成失敗:<br>ルーム名が入力されていません。';
							break;
						case 102:
							echo 'ルーム作成失敗:<br>無効なルーム名です。';
							break;
						case 103:
							echo 'ルーム作成失敗:<br>プレイヤーIDが入力されていません。';
							break;
						case 104:
							echo 'ルーム作成失敗:<br>パスワードに使用できない文字（半角英数字以外の文字、記号など）が入力されています。';
							break;
						case 105:
							echo 'ルーム作成失敗:<br>プレイヤーIDに使用できない文字（半角英数字以外の文字、記号など）が入力されています。';
							break;
						case 106:
							echo 'プレイヤーIDの入力は英字と数字を合わせて使用してください。';
							break;
						case 110:
							echo 'ルーム作成失敗:<br>'.$error_msg;
							break;
					}
				?></p>
			</div>
		</div>
		<div style="bottom:0;border-top:#000 1px solid;margin-top:2em;">
			<div style="max-width:900px;min-width:160px;margin:0 auto;padding:10px;">
				オンセンルーム Ver.<?=APP_VERSION;?><br>
				MIT License<br>
				Copyright (c) 2018 ぽん太＠番頭
			</div>
		</div>
    <main>
</body>
</html>
<script>
$(function(){
	$("#in_dialog").dialog({
		resizable:false,
		modal:true,
		autoOpen:false,
		buttons:{
			"入場する":function(){
				document.frm_roomin.submit();
			}
		}
	});
	$("#cr_dialog").dialog({
		resizable:false,
		modal:true,
		autoOpen:false,
		buttons:{
			"作成する":function(){
				document.frm_crroom.submit();
			}
		}
	});
	$("#err_dialog").dialog({
		resizable:false,
		modal:true,
		autoOpen:false,
		buttons:{
			"OK":function(){
				$("#err_dialog").dialog("close");
			}
		}
	});
	<?=($err>0?'$("#err_dialog").dialog("open");':'');?>
});
function open_cr_dialog(){
	document.getElementById('input_rp').value='';
	$("#cr_dialog").dialog("open");
}
function open_in_dialog(room_id,room_name,pass_flag,obs_flag){
	document.getElementById('span_rn').innerHTML=room_name;
	document.frm_roomin.ro[0].checked=true;
	if(obs_flag!=0){
		document.getElementById('input_obs_on').disabled=true;
	}else{
		document.getElementById('input_obs_on').disabled=false;
	}
	document.getElementById('input_rp').value='';
	document.getElementById('hidden_rn').value=room_id;
	if(pass_flag!=0){
		document.getElementById('div_password').style.display='block';
	}else{
		document.getElementById('div_password').style.display='none';
	}
	$("#in_dialog").dialog("open");
}
</script>