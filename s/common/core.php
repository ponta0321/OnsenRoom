<?php 
/* timezone */
date_default_timezone_set('Asia/Tokyo');
require('const.php');
if(!empty(CHAR_SET)) mb_internal_encoding(CHAR_SET);
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