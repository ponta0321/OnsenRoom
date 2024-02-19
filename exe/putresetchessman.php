<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/exefunction.php');
    
    $principal='';
    if(!empty($_POST['principal'])){
        $principal=$_POST['principal'];
    }else{
        exit;
    }
    $nick_name='';
    if(!empty($_POST['nick_name'])){
        $nick_name=$_POST['nick_name'];
    }else{
        $nick_name=$principal;
    }
    
    $reset_type=0;
    if(!empty($_POST['type'])){
        $reset_type=$_POST['type'];
    }
    $room_id='';
    $room_dir='';
    $room_file='';
    $room_mirror_file='';
    if(!empty($_POST['xml'])){
        $room_id=basename($_POST['xml']);
        $room_dir=DIR_ROOT.'r/n/'.$room_id.'/';
        $room_file=$room_dir.'data.xml';
        $room_mirror_file=$room_dir.'data-mirror.xml';
        if(!file_exists($room_file)){
            exit;
        }
    }
    $exfilelock=new classFileLock($room_dir,$room_id.'_lockfile',5);
    if($exfilelock->flock($room_dir)){
        // ルームファイルの読み込み
        if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
            $exfilelock->unflock($room_dir);
            exit;
        }
        $map_data=(string)$room_xml->head->map_data;
        if(!empty($map_data)){
            $columns=explode('^',$map_data);
            $count_column=couting($columns);
            $map_data='';
            if($reset_type==0){
                /* 何もしない */
            }elseif($reset_type==1){
                for($i=0;$i<$count_column;$i++){
                    if(1===preg_match('/chesscharOtB/',$columns[$i])){
                        unset($columns[$i]);
                    }elseif(1===preg_match('/chessmanOtB/',$columns[$i])){
                        unset($columns[$i]);
                    }
                }
                if(isset($columns)){
                    foreach($columns as $c_value){
                        if(!empty($c_value)){
                            if($map_data!=''){
                                $map_data.='^';
                            }
                            $map_data.=$c_value;
                        }
                    }
                }
            }elseif($reset_type==2){
                for($i=0;$i<$count_column;$i++){
                    if(1===preg_match('/rangeOtB/',$columns[$i])){
                        unset($columns[$i]);
                    }
                }
                if(isset($columns)){
                    foreach($columns as $c_value){
                        if(!empty($c_value)){
                            if($map_data!=''){
                                $map_data.='^';
                            }
                            $map_data.=$c_value;
                        }
                    }
                }
            }
            $room_xml->head->map_data=$map_data;
        }
        // ルームの保存
        if(saveRoomXmlFile($room_xml,$room_file,$principal,$nick_name,0)){
            // phpログ保存
            @include(DIR_ROOT.'s/common/exewritelog.php');
        }else{
            echo 'ERR=情報を更新できませんでした。';
        }
    }else{
        echo 'ERR=アクセスが集中したため送信に失敗しました。';
    }
    $exfilelock->unflock($room_dir);
    exit;