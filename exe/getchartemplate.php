<?php
require('../s/common/core.php');
$json_base_data=array();
$game_type='';
if(!empty($_POST['gt'])) $game_type=$_POST['gt'];
if(!empty($game_type)){
	$file=DIR_ROOT.'s/list/trpg_sys_list.php';
	$global_trpg_name=array('g99'=>'その他');
	if($file) @include($file);
	$file=DIR_ROOT.'s/list/templatechar.php';
	$template_chara=array();
	if($file) @include($file);
	if(isset($global_trpg_name[$game_type])) $json_base_data['type']=$global_trpg_name[$game_type];
	if(isset($template_chara[$game_type])){
		if(isset($template_chara[$game_type][1])) $json_base_data['da']=$template_chara[$game_type][1];
		if(isset($template_chara[$game_type][2])) $json_base_data['db']=$template_chara[$game_type][2];
		if(isset($template_chara[$game_type][3])) $json_base_data['dc']=$template_chara[$game_type][3];
	}
}
if(empty($json_base_data)){
	$json_base_data=array(
		'error'=>'not_exist_char_template',
		'error_description'=>'キャラクターテンプレートはありません。'
	);
}
echo json_encode($json_base_data);