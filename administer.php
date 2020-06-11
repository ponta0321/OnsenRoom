<?php 
    session_start();
    require('./s/common/core.php');

    $error_msg='';
    $success_msg='';
    $state='';
    if(isset($_POST['state'])){
        $state=$_POST['state'];
    }
    $admini_login=0;
    if($state=='logout'){
        unset($_SESSION['admini_login']);
        $success_msg='ログアウトしました。';
    }elseif(isset($_SESSION['admini_login'])){
        $admini_login=$_SESSION['admini_login'];
    }elseif($state=='login'){
        if($_POST['password']===ADMINISTRATOR_PASSWORD){
            $_SESSION['admini_login']=1;
			$admini_login=1;
            $success_msg='ログインしました。';
        }else{
            $error_msg='パスワードが違います。';
        }
    }
    if($admini_login===1){
        require(DIR_ROOT.'s/common/function.php');
        if($state=='deleteold'){
            if(deleteOldRoom()){
                $success_msg='有効期限切れルーム一括削除が完了しました。';
            }else{
                $error_msg='有効期限切れルーム一括削除に失敗しました。';
            }
        }elseif($state=='delete'){
            if(isset($_POST['roomid'])){
                if(deleteTargetRoom($_POST['roomid'])){
                    $success_msg='対象のルームの削除が完了しました。';
                }else{
                    $error_msg='有効期限切れルーム一括削除に失敗しました。';
                }
            }else{
                $error_msg='削除対象のルームが指定されていません。';
            }
        }
        $now_time=time();
        $ra=array();
        if(file_exists(DIR_ROOT.'r/roomlist.php')){
            eval(changeEvalDataPhpFile(DIR_ROOT.'r/roomlist.php'));
        }
        checkPlayerCapacity($serv_delay_facter);
        list($load_avg_1min,$load_avg_5min,$load_avg_15min)=checkLoadAvg(CPU_CORE);
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<style>
    table{
        border:1px #E3E3E3 solid;
        border-collapse:collapse;
        border-spacing:0;
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
    <h1 style="border-bottom:#000 1px solid;">管理者ページ</h1>
<?php
    if(!empty($success_msg)){
        echo '<p style="color:green;margin-top:1em;">成功： '.$success_msg.'</p>';
    }
    if(!empty($error_msg)){
        echo '<p style="color:red;margin-top:1em;">失敗： '.$error_msg.'</p>';
    }
    if($admini_login!==1){ // ログイン画面
?>
    <h2>ログイン</h2>
    <form method="post">
        <input type="hidden" name="state" value="login">
        <div>
            パスワード：
            <input type="password" name="password" style="padding:3px 4px;" value=""> 
            <input type="submit" style="padding:3px 4px;" value="ログイン">
        </div>
    </form>
<?php
    }else{ // 管理画面
?>
    <h2>サーバ情報</h2>
    <h3>ストレージ</h3>
    <table>
        <tr>
            <th>全体サイズ</th>
            <th>空き容量</th>
            <th>使用容量</th>
            <th>使用率</th>
        </tr>
        <tr>
<?php
        $power=array('B','KB','MB','GB','TB','EB','ZB','YB');
        $total_bytes=disk_total_space('/');
        echo '<td>'.displaySimpleByte($total_bytes,$power,2).'</td>';
        $free_bytes=disk_free_space('/');
        echo '<td>'.displaySimpleByte($free_bytes,$power,2).'</td>';
        $used_bytes=$total_bytes-$free_bytes;
        echo '<td>'.displaySimpleByte($used_bytes,$power,2).'</td>';
        echo '<td>'.round($used_bytes/$total_bytes*100,2).'%</td>';
?>
        </tr>
    </table>
    <h3>稼働状況</h3>
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
    <p>CPU負荷（1分平均：<?=$load_avg_1min;?>%）</p>
    <div style="width:300px;height:10px;border:1px #AAA solid;margin:5px 0;"><?php
        $cpu_load_facter=$load_avg_1min;
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
    <p>CPU負荷（5分平均：<?=$load_avg_5min;?>%）</p>
    <div style="width:300px;height:10px;border:1px #AAA solid;margin:5px 0;"><?php
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
    <p>CPU負荷（15分平均：<?=$load_avg_15min;?>%）</p>
    <div style="width:300px;height:10px;border:1px #AAA solid;margin:5px 0;"><?php
        $cpu_load_facter=$load_avg_15min;
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
    <h2>操作</h2>
    <form action="<?=LOBBY_URL_ROOT;?>api/resist_roomserver.php" method="post" style="margin-bottom:1em;">
        <input type="hidden" name="transfer_protocol" value="<?=TRANSFER_PROTOCOL;?>">
        <input type="hidden" name="domain" value="<?=THIS_DOMAIN;?>">
        <input type="hidden" name="name" value="<?=SITE_TITLE;?>">
        <input type="hidden" name="auth_code" value="<?=AUTHENTICATION_CODE;?>">
        <input type="hidden" name="state" value="resist">
        <div>
            <input type="submit" style="padding:3px 4px;" value="ロビーサーバーへ登録申請する">
            申請オプション：
            <select name="voice" style="padding:3px 4px;">
                <option value="0">DICORDギルド(ボイスチャ用チャットルーム)を作成しない</option>
                <option value="1">DICORDギルド(ボイスチャ用チャットルーム)を作成する</option>
            </select> 
        </div>
    </form>
    <form action="<?=LOBBY_URL_ROOT;?>api/resist_roomserver.php" method="post" style="margin-bottom:3em;">
        <input type="hidden" name="transfer_protocol" value="<?=TRANSFER_PROTOCOL;?>">
        <input type="hidden" name="domain" value="<?=THIS_DOMAIN;?>">
        <input type="hidden" name="name" value="<?=SITE_TITLE;?>">
        <input type="hidden" name="auth_code" value="<?=AUTHENTICATION_CODE;?>">
        <input type="hidden" name="voice" value="0">
        <input type="hidden" name="state" value="delete">
        <p><input type="submit" style="padding:3px 4px;" value="ロビーサーバーへ登録を解除する"></p>
    </form>
    <form method="post" style="margin-bottom:1em;">
        <input type="hidden" name="state" value="logout">
        <input type="submit" style="padding:3px 4px;" value="ログアウト">
    </form>
    <form method="post" style="margin-bottom:1em;">
        <input type="hidden" name="state" value="deleteold">
        <input type="submit" style="padding:3px 4px;" value="有効期限切れルーム一括削除">
    </form>
    <h2>ルーム一覧</h2>
    <table>
        <tr>
            <th>ルーム名</th>
            <th>作成者ID</th>
            <th>作成者IP</th>
            <th>作成日時</th>
            <th>最終更新</th>
            <th>有効期限</th>
            <th>操作</th>
        </tr>
        <?php
            if(0<count($ra)){
                foreach($ra as $room_value){
                    echo '<tr>';
                    echo '<td><a href="'.URL_ROOT.'r/n/'.$room_value[1].'/data.xml" target="_blank">'.$room_value[2].'</a></td>'; // ルーム名
                    echo '<td><a href="'.LOBBY_URL_ROOT.'player-detail.php?c='.$room_value[3].'" target="_blank">'.$room_value[3].'</a></td>'; // 作成者ID
                    echo '<td>'.$room_value[4].'</td>'; // 作成者IP
                    echo '<td>'.date('Y-m-d H:i',$room_value[7]).'</td>'; // 作成日時
                    echo '<td>'.date('Y-m-d H:i',$room_value[8]).'</td>'; // 最終更新
                    if($room_value[9]>=$now_time){ // 有効期限
                        echo '<td>';
                    }else{
                        echo '<td style="color:red;">';
                    }
                    echo date('Y-m-d H:i',$room_value[9]).'</td>';
                    echo '<td>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="state" value="delete">';
                    echo '<input type="hidden" name="roomid" value="'.$room_value[1].'">';
                    echo '<input type="submit" style="padding:3px 4px;" value="削除">';
                    echo '</form>';
                    echo '</td>'; // 操作
                    echo '</tr>';
                }
            }else{
                echo '<tr><td colspan="7" style="color:#AAA;">ルームはありません。</td></tr>';
            }
        ?>
    </table>
<?php
    }
?>
</body>
</html>