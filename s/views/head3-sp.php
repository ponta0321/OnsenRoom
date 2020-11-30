<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#  article: http://ogp.me/ns/article#">
<meta charset="UTF-8">
<meta name="robots" content="<?=$global_meta_robots;?>">
<meta name="keywords" content="<?=$global_page_keywords;?>">
<meta name="description" content="<?=$global_page_description;?>">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title><?=$global_page_title;?></title>
<link rel="stylesheet" type="text/css" href="<?=URL_ROOT;?>css/common.css?2018070101">
<link rel="stylesheet" type="text/css" href="<?=URL_ROOT;?>css/sp.css?2018070101">
<!-- script -->
<script type="text/javascript" charset="UTF-8" src="<?=URL_ROOT;?>js/jquery-3.1.0.min.js"></script>
<script type="text/javascript" charset="UTF-8" src="<?=URL_ROOT;?>js/const.js?2020060901"></script>
<script>
    var server_access_start_time=new Date();
</script>
<script type="text/javascript" charset="UTF-8" src="<?=URL_ROOT;?>exe/synctime.php?2020112901"></script>
<script>
    var server_access_end_time=new Date();
    if(typeof(original_saver_time)!="undefined"){
        var time_revised_value=original_saver_time-Math.floor((server_access_end_time-server_access_start_time)/2)-server_access_end_time;
    }else{
        var time_revised_value=-Math.floor((server_access_end_time-server_access_start_time)/2);
    }
</script>
</head>