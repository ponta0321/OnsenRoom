<?php
    require('./s/common/core.php');
	$redirect_page=URL_ROOT;
	if(!empty($_SERVER['QUERY_STRING'])){
		$redirect_page=$redirect_page.'?'.$_SERVER['QUERY_STRING'];
	}
	header('location: '.$redirect_page);
	exit;