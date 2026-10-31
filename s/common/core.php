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
GLOBAL VARIABLE
========================================================================*/
$global_page_url=URL_ROOT;
$global_page_title=SITE_TITLE;
$global_page_description=SITE_DESCRIPTION;
$global_page_keywords='';
$global_meta_robots='index,follow,archive';
require('basicfunction.php');
require('class.php');