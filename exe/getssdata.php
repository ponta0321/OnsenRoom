<?php
    require('../s/common/core.php');
    
    $principal_id='';
    if(isset($_POST['principal_id'])){
        $principal_id=$_POST['principal_id'];
    }else{
        echo 'ERR=あなたはシナリオセットを取得できません。';
        exit;
    }
    $player_ip='';
    if(isset($_POST['player_ip'])){
        $player_ip=$_POST['player_ip'];
    }else{
        echo 'ERR=あなたはシナリオセットを取得できません。';
        exit;
    }
    $sso='';
    if(isset($_POST['sso'])){
        $sso=$_POST['sso'];
    }else{
        echo 'ERR=シナリオNoが指定されていません。';
        exit;
    }
    require(DIR_ROOT.'s/common/function.php');
    if($ss_data_json=getSSFromLobbyServer($principal_id,$player_ip,$sso)){
        if(isset($ss_data_json['error_description'])){
            echo 'ERR='.$ss_data_json['error_description'];
            exit;
        }elseif(isset($ss_data_json['ss_data'])){
            echo $ss_data_json['ss_data'];
            exit;
        }
    }
    echo 'ERR=ロビーサーバー読み込みエラー'.$test;