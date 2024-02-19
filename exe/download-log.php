<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/function.php');
    $result=false;
    $room_id='';
    if(!empty($_GET['i'])){
        $room_id=$_GET['i'];
    }else{
        echo '<p>ルームが指定されていません。</p>';
        exit;
    }
    $principal='';
    if(!empty($_GET['p'])){
        $principal=$_GET['p'];
    }
    $last_time=0;
    $v_html='';
    $total_log_array=getChatLogArray($room_id);
    if(isset($total_log_array[0][0])){
        $last_count=couting($total_log_array);
        for($i=0;$i<$last_count;$i++){
            $v_html.=changeLogStringFormLogRecord($total_log_array[$i],$principal,$last_time);
        }
    }else{
        echo '<p>指定されたルームのログはありませんでした。</p>';
        exit;
    }
	if(empty(CHAR_SET)){
		$v_html=mb_convert_encoding(html_entity_decode($v_html),'SJIS');
	}else{
		$v_html=mb_convert_encoding(html_entity_decode($v_html),'SJIS',CHAR_SET);
	}
    $logs_file=DIR_ROOT.'r/n/'.$room_id.'/log.csv';
    if(file_put_contents($logs_file,$v_html)){
        if($last_time==0){
            $log_filename='log'.date('Ymd_His').'.csv';
        }else{
            $log_filename='log'.date('Ymd_His',$last_time).'.csv';
        }
        header("Content-Type: application/x-download");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$log_filename");
        header("Content-Length: ".strlen($v_html));
        readfile($logs_file);
    }else{
        echo '<p>ログのダウンロードは失敗しました。</p>';
        exit;
    }
exit;