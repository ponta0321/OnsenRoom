<?php
/* timezone */
date_default_timezone_set('Asia/Tokyo');
require('const.php');
if(!empty(CHAR_SET)) mb_internal_encoding(CHAR_SET);
/*========================================================================
DEBUG MODE
=========================================================================
DEBUG_MODE=true の時だけ有効化。
- display_errors は 0 のまま (JSON応答などに混入させない)
- log_errors=1 で log/debug.log に書き込み
- set_error_handler で @ 抑制されたエラーも [SUPPRESSED] 付きで記録
========================================================================*/
if(defined('DEBUG_MODE') && DEBUG_MODE){
	error_reporting(E_ALL);
	ini_set('display_errors','0');
	ini_set('log_errors','1');
	ini_set('error_log',DIR_ROOT.'log/debug.log');
	// @ 抑制の判定: PHP 7 は error_reporting()===0、PHP 8 は !(error_reporting() & $severity)
	// 両方の条件で [SUPPRESSED] マーク付きログに残す
	set_error_handler(function($severity,$message,$file,$line){
		$er=error_reporting();
		$suppressed=($er===0||!($er & $severity));
		$prefix=$suppressed?'[SUPPRESSED]':'[NORMAL]';
		error_log(sprintf('%s %s in %s:%d',$prefix,$message,$file,$line));
		return false; // false を返すと PHP 本来のエラー処理 (@ 抑制 含む) も続行
	});
}
/*========================================================================
DEBUG TRACE (関数の引数・返り値をログに記録)
=========================================================================
使い方: 対象関数の冒頭と return 直前に以下を挿入
    debug_trace_enter(__FUNCTION__, func_get_args());
    ...
    debug_trace_exit(__FUNCTION__, $return_value);
    return $return_value;

DEBUG_MODE=false 時は即座に return して何もしない (no-op)。
本番にコードが残っても DEBUG_MODE=false なら性能影響ゼロ。

log/debug.log に [TRACE ENTER] / [TRACE EXIT] タグ付きで出力される。
========================================================================*/
function debug_trace_enter($name,$args){
	if(!(defined('DEBUG_MODE') && DEBUG_MODE)) return;
	error_log(sprintf('[TRACE ENTER] %s(%s)',$name,debug_trace_dump($args)));
}
function debug_trace_exit($name,$retval){
	if(!(defined('DEBUG_MODE') && DEBUG_MODE)) return;
	error_log(sprintf('[TRACE EXIT ] %s = %s',$name,debug_trace_dump($retval)));
}
function debug_trace_dump($v,$depth=0){
	if($depth>3) return '...';
	if(is_null($v)) return 'null';
	if(is_bool($v)) return $v?'true':'false';
	if(is_int($v)||is_float($v)) return (string)$v;
	if(is_string($v)){
		if(strlen($v)>200) return var_export(substr($v,0,200).'...('.strlen($v).'bytes)',true);
		return var_export($v,true);
	}
	if(is_array($v)){
		$parts=array();
		$i=0;
		foreach($v as $k=>$x){
			if($i++>=20){ $parts[]='...('.count($v).'items)'; break; }
			$parts[]=var_export($k,true).'=>'.debug_trace_dump($x,$depth+1);
		}
		return '['.implode(',',$parts).']';
	}
	if(is_object($v)){
		if($v instanceof SimpleXMLElement){
			$name=$v->getName();
			$str=(string)$v;
			if(strlen($str)>80) $str=substr($str,0,80).'...';
			return 'SimpleXML<'.$name.'>('.var_export($str,true).')';
		}
		return get_class($v).'@'.spl_object_id($v);
	}
	if(is_resource($v)) return 'resource('.get_resource_type($v).')';
	return gettype($v);
}
/*========================================================================
GLOBAL VARIABLE
========================================================================*/
$global_page_url=URL_ROOT;
$global_page_title=SITE_TITLE;
$global_page_description=SITE_DESCRIPTION;
$global_page_keywords='';
$global_meta_robots='index,follow,archive';
require('basicfunction.php');
require('class.php');