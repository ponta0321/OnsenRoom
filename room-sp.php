<?php 
    $local_page_url='room-sp.php';
    $_SESSION['room_type']='sp';
    require('./s/common/initroom.php');
    $global_page_title='ルーム「'.$room_name.'」｜'.SITE_TITLE;
    $global_page_url=URL_ROOT.$local_page_url;
?>
<!DOCTYPE html>
<html lang="ja">
<?php require(DIR_ROOT.'s/views/head3-sp.php');?>
<body>
<input id="id_room_id" type="hidden" name="xml" value="<?=$base_room_file;?>" />
<input id="id_player_id" type="hidden" name="principal" value="<?=$principal_id;?>" />
<audio id="game_audio" style="display:none;" src="<?=$first_play_music;?>"></audio>
<audio id="se_audio_1" style="display:none;" src="<?=URL_ROOT;?>sounds/se01.mp3"></audio>
<div id="now_loading-bg" style="position:fixed;top:0;left:0;width:100%;height:100%;background:#FF5E19;z-index:999;">
    <div id="now_loaded" style="position:fixed;top:0;left:0;width:204px;height:80px;text-align:center;display:none;">
        <input id="now_loaded_sok" type="button" value="音なし" style="width:68px;height:40px;padding:8px;margin:8px;border-radius:10px;" />
        <input id="now_loaded_ook" type="button" value="音あり" style="width:68px;height:40px;padding:8px;margin:8px;border-radius:10px;" />
    </div>
</div>
<div id="room_header-sp" class="room_header-sp">
    <a id="room_menu_button-sp" title="メニュー">
        <img id="room_menu_button_icon" src="<?=URL_ROOT;?>images/m_icon104.png" width="27" height="24" border="0" alt="メニュー" style="vertical-align:middle;" />
        <span class="header_info">参:<span id="id_c_participant"><?=$count_participant;?></span>見:<span id="id_c_observer"><?=$count_observer;?></span><span id="id_c_roomname"><?=$room_name;?></span>
    </a>
    <div id="room_menu-sp" class="room_menu-sp">
        <ul>
            <li><a href="javascript:void(0)" onClick="pushMenuTab('comment_box');" title="チャット"><img id="menu_li_comment_box" src="<?=URL_ROOT;?>images/m_icon94.png" width="27" height="24" border="0" alt="チャット" style="vertical-align:middle;" /></a></li>
            <li><a href="javascript:void(0)" onClick="pushMenuTab('dice_box');" title="ダイス"><img id="menu_li_dice_box" src="<?=URL_ROOT;?>images/m_icon106.png" width="27" height="24" border="0" alt="ダイス" style="vertical-align:middle;" /></a></li>
            <li><a href="javascript:void(0)" onClick="pushMenuTab('id_ltb0');" title="メモ"><img id="menu_li_id_ltb0" src="<?=URL_ROOT;?>images/m_icon95.png" width="27" height="24" border="0" alt="メモ" style="vertical-align:middle;" /></a></li>
            <li><a href="javascript:void(0)" onClick="pushMenuTab('id_ctb0');" title="ボード＆コマ"><img id="menu_li_id_ctb0" src="<?=URL_ROOT;?>images/m_icon108.png" width="27" height="24" border="0" alt="ボード＆コマ" style="vertical-align:middle;" /></a></li>
            <li><a href="javascript:void(0)" onClick="pushMenuTab('id_rtb0');" title="キャラクター一覧"><img id="menu_li_id_rtb0" src="<?=URL_ROOT;?>images/m_icon109.png" width="27" height="24" border="0" alt="キャラクター一覧" style="vertical-align:middle;" /></a></li>
            <?php
                if($observer_flag!=1){
                    echo '<li><a href="javascript:void(0)" onClick="pushMenuTab(\'id_ctb1\');" title="キャラクターシート"><img id="menu_li_id_ctb1" src="'.URL_ROOT.'images/m_icon97.png" width="27" height="24" border="0" alt="キャラクターシート" style="vertical-align:middle;" /></a></li>';
                }
            ?>
            <li><a href="javascript:void(0)" onClick="pushMenuTab('id_rtb1');" title="プレイヤー一覧"><img id="menu_li_id_rtb1" src="<?=URL_ROOT;?>images/m_icon101.png" width="27" height="24" border="0" alt="プレイヤー一覧" style="vertical-align:middle;" /></a></li>
            <?php
                if($observer_flag!=1){
                    echo '<li><a href="javascript:void(0)" onClick="pushMenuTab(\'id_ltb3\');" title="設定"><img id="menu_li_id_ltb3" src="'.URL_ROOT.'images/m_icon96.png" width="27" height="24" border="0" alt="設定" style="vertical-align:middle;" /></a></li>';
                }
            ?>
            <li><a href="<?=URL_ROOT;?>exit.php" title="退室する"><img id="menu_li_09" src="<?=URL_ROOT;?>images/m_icon72.png" width="27" height="24" border="0" alt="退室する" style="vertical-align:middle;" /></a></li>
        </ul>
    </div>
    <div id="comment_box">
        <input id="id_call_name" type="hidden" value="<?=$nick_name;?>">
        <input id="id_call_name_id" type="hidden" value="">
        <table id="comment_write"><tr>
            <td style="width:100px;background-color:#c0c0c0;overflow:hidden;">
                <select id="id_call_name_st" style="max-width:140px;" onChange="putCallName();" <?=$observer_flag!=1?'':'disabled';?> >
                    <?=setOptionCallName($principal_id,$nick_name,$characterlist_array,0);?></select>
            </td>
            <td>
                <input id="id_comment" type="text" maxlength="200" value="" onkeypress="checkPushKeyEnter(event.keyCode);" <?php
                    if(($login_flag!=true)||
                       (($observer_flag!=0)&&($observer_write!=0))){
                        echo 'disabled="disabled"';
                    }else{
                        echo 'autofocus';
                    }
                ?> />
            </td>
            <td style="width:40px;overflow:hidden;">
                <select id="id_chat_color" onChange="setCookieSettingData();" <?php
                    if(($login_flag!=true)||
                       (($observer_flag!=0)&&($observer_write!=0))){
                        echo 'disabled="disabled"';
                    }else{
                        echo 'autofocus';
                    }
                ?> >
                    <option value="#000000" style="color:#000000;font-weight:bold;" <?=($chat_color=='#000000'?'selected="selected"':'');?>>黒</option>
                    <option value="#606060" style="color:#606060;font-weight:bold;" <?=($chat_color=='#606060'?'selected="selected"':'');?>>灰</option>
                    <option value="#CC0000" style="color:#CC0000;font-weight:bold;" <?=($chat_color=='#CC0000'?'selected="selected"':'');?>>赤</option>
                    <option value="#0000CC" style="color:#0000CC;font-weight:bold;" <?=($chat_color=='#0000CC'?'selected="selected"':'');?>>青</option>
                    <option value="#00CC00" style="color:#00CC00;font-weight:bold;" <?=($chat_color=='#00CC00'?'selected="selected"':'');?>>緑</option>
                    <option value="#E0B000" style="color:#E0B000;font-weight:bold;" <?=($chat_color=='#E0B000'?'selected="selected"':'');?>>黄</option>
                    <option value="#FF8119" style="color:#FF8119;font-weight:bold;" <?=($chat_color=='#FF8119'?'selected="selected"':'');?>>柑</option>
                    <option value="#C400CC" style="color:#C400CC;font-weight:bold;" <?=($chat_color=='#C400CC'?'selected="selected"':'');?>>紫</option>
                    <option value="#272672" style="color:#272672;font-weight:bold;" <?=($chat_color=='#272672'?'selected="selected"':'');?>>藍</option>
                    <option value="#E54500" style="color:#E54500;font-weight:bold;" <?=($chat_color=='#E54500'?'selected="selected"':'');?>>蒲</option>
                    <option value="#6D9A4A" style="color:#6D9A4A;font-weight:bold;" <?=($chat_color=='#6D9A4A'?'selected="selected"':'');?>>鶯</option>
                    <option value="#7C6035" style="color:#7C6035;font-weight:bold;" <?=($chat_color=='#7C6035'?'selected="selected"':'');?>>茶</option>
                    <option value="#4E2F4F" style="color:#4E2F4F;font-weight:bold;" <?=($chat_color=='#4E2F4F'?'selected="selected"':'');?>>茄</option>
                    <option value="#EF7585" style="color:#EF7585;font-weight:bold;" <?=($chat_color=='#EF7585'?'selected="selected"':'');?>>桃</option>
                </select>
            </td>
        </tr></table>
        <span id="dicebot_text_box" style="display:none;">
            <img style="vertical-align:middle;margin-left:4px;" src="<?=URL_ROOT;?>images/m_icon18.png" width="18" height="24" border="0" />
            <pre id="dicebot_text"><?php
                $dicebot_text_key=(string)$xml->head->game_dicebot;
                if(isset($dicebot_textlist[$dicebot_text_key])){
                    echo $dicebot_textlist[$dicebot_text_key][1];
                }elseif(isset($bac_gamelist[$dicebot_text_key])){
                    echo $bac_gamelist[$dicebot_text_key][1];
                }else{
                    echo $dicebot_textlist['g99'][1];
                }
            ?></pre>
        </span>
        <div id="comment_space"></div>
    </div>
</div>
<div id="room_body-sp" class="room_main-sp">
    <div id="dice_box" class="rbox-sp sp_room_std">
        <p class="rbox_title-sp">ダイス</p>
        <p><select id="id_dice_count_number" <?=$observer_flag!=1?'':'disabled';?> >
            <option value="1">1</option>
            <option value="2" selected>2</option>
        <?php
            for($i=3;$i<=100;$i++){
                echo '<option value="'.$i.'">'.$i.'</option>';
            }
        ?>
        </select>
        D
        <select id="id_dice_surface" <?=$observer_flag!=1?'':'disabled';?> >
            <option value="2">2</option>
            <option value="4">4</option>
            <option value="6" selected>6</option>
            <option value="8">8</option>
            <option value="10">10</option>
            <option value="12">12</option>
            <option value="20">20</option>
            <option value="100">100</option>
        </select>
        <input type="button" id="id_roll_button" value="サイコロを振る" onClick="pushDiceRoll()" <?=$observer_flag!=1?'':'disabled';?> /></p>
        <div id="dice_space" class="clearfix"></div>
        <?=$observer_flag==1?'<div class="dont_touch_screen"></div>':'';?> 
    </div>
    <div id="id_ltb0" class="rbox-sp sp_room_std" style="display:none;">
        <p class="rbox_title-sp">メモ</p>
        <?php require(DIR_ROOT.'s/views/roommemo.php');?>
        <div id="id_acb_roomtimer" class="r_char_sheet_acb" onClick='putAcRoomTimer()'>カウンター&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
        <div id="id_acd_roomtimer" style="display:none;">
            <?php require(DIR_ROOT.'s/views/roomtimer.php');?>
        </div>
        <?=$observer_flag==1?'<div class="dont_touch_screen"></div>':'';?> 
    </div>    
    <div id="id_ctb0" class="rbox-sp sp_room_std">
        <p class="rbox_title-sp">ボード＆コマ</p>
        <div id="boardlabel">
            <span id="bl_tab_0" class="bl_buttom_actived" onClick="pushBoardLabelTab(0)">コマ操作</span>
            <span id="bl_tab_1" onClick="pushBoardLabelTab(1)">マッピング</span>
            <a id="bl_tab_2" href="<?=URL_ROOT;?>exe/download-board.php?i=<?=$base_room_file;?>" style="text-decoration:none;" target="_blank"><span>保存</span></a>&nbsp;
            <label id="bl_tab_3" for="load_board_data">読込<input type="file" id="load_board_data" accept="text/xml,application/xml" style="display:none;"></label>
        </div>
        <div id="id_mappingtool_box" class="chessman_box_slider" style="display:none;">
            <div class="mappingtool_box" style="width:3200px;">
                <div>
                    <img id="mappingtool_ic_d" src="<?=URL_ROOT;?>images/m_icon133.png" width="32" height="32" onClick="clearAllMapping()" border="0" /><span id="mt_bx_line"></span>
                </div>
                <div class="chessman_box">
                    <img id="mappingtool_ic_0" class="actived_mappingtool" src="<?=URL_ROOT;?>images/m_icon132.png" width="32" height="32" onClick="setMappingTool(0)" border="0" /><span id="mt_bx_deco"></span>
                </div>
            </div>
        </div>
        <div id="id_chessman_box">
            <?php require(DIR_ROOT.'s/views/roomchessman-sp.php');?>
            <div style="display:inline-block;">
                <input type="button" id="all_reset" value="全消去" onClick="putChessmanReset(0);" />
                <input type="button" id="chessman_reset" value="コマ全消" onClick="putChessmanReset(1);" />
                <input type="button" id="marking_reset" value="マーキング全消" onClick="putChessmanReset(2);" />
            </div>
            <img id="trashbox"
                 draggable="false"
                 ondrop="dropChessman(event);event.preventDefault();"
                 ondragenter="event.preventDefault();" 
                 ondragover="event.preventDefault();" 
                 src="<?=URL_ROOT?>/images/trashbox.png" 
                 width="64" height="64" />
        </div>
        <?php require(DIR_ROOT.'s/views/roomchessboard-sp.php');?>
        <?=$observer_flag==1?'<div class="dont_touch_screen"></div>':'';?> 
    </div>
    <div id="id_rtb0" class="rbox-sp sp_room_std">
        <p class="rbox_title-sp">キャラクター一覧</p>
        <div id="character_space"></div>
        <?=$observer_flag==1?'<div class="dont_touch_screen"></div>':'';?> 
    </div>
    <div id="id_ctb1" class="rbox-sp" style="display:none;">
        <p class="rbox_title-sp">キャラクターシート</p>
        <?php require(DIR_ROOT.'s/views/roomcharsheet.php');?>
        <?=$observer_flag==1?'<div class="dont_touch_screen"></div>':'';?> 
    </div>
    <div id="id_rtb1" class="rbox-sp sp_room_std" style="display:none;">
        <p class="rbox_title-sp">プレイヤー一覧</p>
        <div id="participant_space"></div>
    </div>
    <div id="id_ltb3" class="rbox-sp sp_room_std" style="display:none;">
        <p class="rbox_title-sp">設定</p>
        <?php 
            require(DIR_ROOT.'s/views/roomsetting.php');
            echo '<hr style="border:0;border-top:1px dotted #AAA;margin-top:7px;" />';
            require(DIR_ROOT.'s/views/roomsetting2.php');
            if(!empty($voice_invite_code)&&($voice_invite_code!=-1)){
        ?>
        <div class="room_setting_box_wb">
            <p><label>DISCORDでボイスチャットをする</label></p>
            <div class="discord_button"><a href="#" onClick="pushStartVoiceChat();"><img src="<?=URL_ROOT;?>images/m_icon125.png" width="200" height="100" border="0" /></a></div>
            <p class="font_red font_xsmall">※留意点<br>
                ・ボタンを押して接続が完了後、このページに手動で戻してください。<br>
                ・現行の仕様はプライベートに向いていません。<br>
                ・IEでは動作しません。ブラウザはChromeを推奨します。
            </p>
        </div>
        <?php
            }
            if($login_flag==true){
                echo '<p class="font_xsmall text_right m_top_1em" style="z-index:501;"><a href="'.URL_ROOT.'exe/download-log.php?i='.$base_room_file.'&p='.$principal_id.'" target="_blank">全てのログをダウンロードする</a></p>';
            }
        ?>
        <?=$observer_flag==1?'<div class="dont_touch_screen"></div>':'';?> 
    </div>
    <?php 
        if(file_exists(DIR_ROOT.'s/views/roomad-sp.php')){
            require(DIR_ROOT.'s/views/roomad-sp.php');
        }
    ?>
</div>
<div id="chara_right_menu-bg" style="display:none;">
    <ul id="chara_menu_list" class="right_menu" >
        <li id="crm_rroll" class="operate">右回転</li>
        <li id="crm_lroll" class="operate">左回転</li>
        <li id="crm_expansion" class="operate">拡大</li>
        <li id="crm_shrink" class="operate">縮小</li>
        <li id="crm_fore" class="operate">最前面</li>
        <li id="crm_back" class="operate">最背面</li>
		<li id="crm_rename" class="operate">名称変更</li>
        <li id="crm_delete" class="operate">削除</li>
    </ul>
	<div id="chara_rename_box" class="right_menu">
		<p>名称変更</p>
		<input id="rename_box_input" type="text" maxlength="50" value="" onKeydown="changeChessmanName(event);" />
	</div>
</div>
<div id="notice_box" style="position:fixed;display:inline-block;top:0;left:0;padding:6px;background-color:#FFF;border:1px solid #AAA;border-radius:4px;z-index:99;visibility:hidden;" ></div>
<div id="loading_box" style="position:fixed;display:none;top:0;left:0;width:208px;height:45px;padding:10px;background-color:#FFF;border:1px solid #AAA;border-radius:4px;z-index:1000;" onClick="this.style.display='none';" ><img src="<?=URL_ROOT;?>images/loading2.gif" width="208" height="45" border="0" alt="LOADING..." /></div>
</body>
</html>
<script>
/*////////////////////////////////////////////////////////////////
variable
////////////////////////////////////////////////////////////////*/
var now_utsp=Math.floor(original_client_time/1000);
var obj_character_list=convertJsonTextToObject('<?=$c_character->convertCharListJsonFromArray($characterlist_array);?>');
listStandData=[[(now_utsp-10),'',0,0,''],[(now_utsp-10),'',0,0,''],[(now_utsp-10),'',0,0,'']];
var leftCharDetail=window.innerWidth>0?(window.innerWidth-540)/2-55:window.innerWidth;
/*////////////////////////////////////////////////////////////////
function
////////////////////////////////////////////////////////////////*/
function putAcRoomTimer(){
    if(document.getElementById('id_acd_roomtimer').style.display=="none"){
        document.getElementById('id_acd_roomtimer').style.display="block";
        document.getElementById('id_acb_roomtimer').innerHTML='カウンター&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）';
    }else{
        document.getElementById('id_acd_roomtimer').style.display="none";
        document.getElementById('id_acb_roomtimer').innerHTML='カウンター&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）';
    }
}
function checkPushKeyEnter(code){
    if(13===code){
        postPutComment();
    }
}
function pushMenuTab(open_id){
    if('comment_box'==open_id){
        if(document.getElementById('comment_box').style.overflow=='auto'){
            document.getElementById('comment_box').style.height="100px";
            document.getElementById('comment_box').style.overflow="hidden";
            $('#comment_box').scrollTop(0);
            document.getElementById('menu_li_comment_box').src='<?=URL_ROOT;?>images/m_icon94.png';
        }else{
            document.getElementById('comment_box').style.height=(window.innerHeight+100)+'px';
            document.getElementById('comment_box').style.overflow="auto";
            document.getElementById('menu_li_comment_box').src='<?=URL_ROOT;?>images/m_icon113.png';
        }
    }else{
        if('dice_box'==open_id){
            if(document.getElementById('dice_box').style.display=='none'){
                document.getElementById('menu_li_dice_box').src='<?=URL_ROOT;?>images/m_icon106.png';
            }else{
                document.getElementById('menu_li_dice_box').src='<?=URL_ROOT;?>images/m_icon98.png';
            }
            $('#dice_box').slideToggle(100);
        }else if('id_ltb0'==open_id){
            if(document.getElementById('id_ltb0').style.display=='none'){
                document.getElementById('menu_li_id_ltb0').src='<?=URL_ROOT;?>images/m_icon107.png';
            }else{
                document.getElementById('menu_li_id_ltb0').src='<?=URL_ROOT;?>images/m_icon95.png';
            }
            $('#id_ltb0').slideToggle(100);
        }else if('id_ctb0'==open_id){
            if(document.getElementById('id_ctb0').style.display=='none'){
                document.getElementById('menu_li_id_ctb0').src='<?=URL_ROOT;?>images/m_icon108.png';
            }else{
                document.getElementById('menu_li_id_ctb0').src='<?=URL_ROOT;?>images/m_icon103.png';
            }
            $('#id_ctb0').slideToggle(100);
        }else if('id_rtb0'==open_id){
            if(document.getElementById('id_rtb0').style.display=='none'){
                document.getElementById('menu_li_id_rtb0').src='<?=URL_ROOT;?>images/m_icon109.png';
            }else{
                document.getElementById('menu_li_id_rtb0').src='<?=URL_ROOT;?>images/m_icon102.png';
            }
            $('#id_rtb0').slideToggle(100);
        }else if('id_ctb1'==open_id){
            if(document.getElementById('id_ctb1').style.display=='none'){
                document.getElementById('menu_li_id_ctb1').src='<?=URL_ROOT;?>images/m_icon110.png';
            }else{
                document.getElementById('menu_li_id_ctb1').src='<?=URL_ROOT;?>images/m_icon97.png';
            }
            $('#id_ctb1').slideToggle(100);
        }else if('id_rtb1'==open_id){
            if(document.getElementById('id_rtb1').style.display=='none'){
                document.getElementById('menu_li_id_rtb1').src='<?=URL_ROOT;?>images/m_icon111.png';
            }else{
                document.getElementById('menu_li_id_rtb1').src='<?=URL_ROOT;?>images/m_icon101.png';
            }
            $('#id_rtb1').slideToggle(100);
        }else if('id_ltb3'==open_id){
            if(document.getElementById('id_ltb3').style.display=='none'){
                document.getElementById('menu_li_id_ltb3').src='<?=URL_ROOT;?>images/m_icon112.png';
                flagOpenRoomStting=1;
            }else{
                document.getElementById('menu_li_id_ltb3').src='<?=URL_ROOT;?>images/m_icon96.png';
                flagOpenRoomStting=0;
            }
            $('#id_ltb3').slideToggle(100);
        }
        document.getElementById('comment_box').style.height="100px";
        document.getElementById('comment_box').style.overflow="hidden";
        document.getElementById('menu_li_comment_box').src='<?=URL_ROOT;?>images/m_icon94.png';
        $('#comment_box').scrollTop(0);
    }
    $('#room_menu-sp').slideToggle(100,function(){
        if(document.getElementById('room_menu-sp').style.display=='none'){
            document.getElementById('room_menu_button_icon').src='<?=URL_ROOT;?>images/m_icon104.png';
        }else{
            document.getElementById('room_menu_button_icon').src='<?=URL_ROOT;?>images/m_icon105.png';
        }
    });
}
function postPutComment(temp_comment){
    var temp_comment=document.getElementById('id_comment').value;
    var temp_call_name=document.getElementById('id_call_name').value;
    if(temp_call_name==""){
        temp_call_name='<?=$nick_name!=""?$nick_name:$principal_id;?>';
    }
    if(temp_comment!=""){
        document.getElementById('id_comment').value='';
        if(temp_comment=='/bs'){
            saveLocalBD();
        }else if(temp_comment=='/bl'){
            loadLocalBD('<?=URL_ROOT;?>exe/putsettingdata.php','<?=$base_room_file;?>','<?=$principal_id;?>','id_comment,id_call_name');
        }else{
            document.getElementById('id_comment').disabled=true;
            document.getElementById('id_call_name').disabled=true;
			setTimeout(function(){
					document.getElementById('id_comment').disabled=false;
					document.getElementById('id_call_name').disabled=false;
				},10000);
            $.post('<?=URL_ROOT;?>exe/putcomment.php',{
                comment:temp_comment,
                chat_color:document.getElementById('id_chat_color').value,
                call_name:temp_call_name,
                chat_type:<?=$observer_flag!=1?1:3;?>,
                observer_flag:<?=$observer_flag;?>,
                principal:'<?=$principal_id;?>',
                nick_name:'<?=$nick_name;?>',
                xml:'<?=$base_room_file;?>',
            },function(data){
                openWTL(data);
                document.getElementById('id_comment').disabled=false;
                document.getElementById('id_call_name').disabled=false;
                document.getElementById('id_comment').focus();
            },'html');
        }
    }
}
/*////////////////////////////////////////////////////////////////
events
////////////////////////////////////////////////////////////////*/
document.getElementById('now_loaded_sok').onclick=function(){
    var audio=document.getElementById('game_audio');
    audio.src='<?=URL_ROOT;?>sounds/se02.mp3';
    audio.volume='0.0';
    audio.play();
    document.getElementById('now_loading-bg').style.display='none';
    var audio2=document.getElementById('se_audio_1');
    audio2.src='<?=URL_ROOT;?>sounds/se01.mp3';
    audio2.volume='0.0';
    setCSfontSize();
};
document.getElementById('now_loaded_ook').onclick=function(){
    var audio=document.getElementById('game_audio');
    audio.src='<?=URL_ROOT;?>sounds/se02.mp3';
    var mv=document.getElementById('game_music_volume');
    mv.value=<?php if(isset($_POST['music_volume'])){echo $_POST['music_volume'];}elseif(isset($_COOKIE['music_volume'])){echo $_COOKIE['music_volume'];}else{echo 10;}?>;
    var volumeValue=(mv.value.length==1)?'0.0'+mv.value:'0.'+mv.value;
    audio.volume=volumeValue;
    audio.play();
    document.getElementById('now_loading-bg').style.display='none';
    var audio2=document.getElementById('se_audio_1');
    audio2.src='<?=URL_ROOT;?>sounds/se01.mp3';
    setCSfontSize();
};
$('#room_menu_button-sp').click(function(){
    $('#room_menu-sp').slideToggle(100,function(){
        if(document.getElementById('room_menu-sp').style.display=='none'){
            document.getElementById('room_menu_button_icon').src='<?=URL_ROOT;?>images/m_icon104.png';
        }else{
            document.getElementById('room_menu_button_icon').src='<?=URL_ROOT;?>images/m_icon105.png';
        }
    });
});
/*////////////////////////////////////////////////////////////////
before load process
////////////////////////////////////////////////////////////////*/
document.getElementById('dicebot_text').style.left=leftCharDetail+'px';
// document.getElementById('game_audio').volume=<?=$music_volume;?>; 2018/2/6 削除予定
document.getElementById('loading_box').style.left=Math.floor((window.innerWidth-230)/2)+'px';
document.getElementById('loading_box').style.top=Math.floor((window.innerHeight-67)/2)+'px';
document.getElementById('loading_box').style.display='block';
/*////////////////////////////////////////////////////////////////
after load process
////////////////////////////////////////////////////////////////*/
$(function(){
    callRoomdData('<?=URL_ROOT;?>','<?=$base_room_file;?>');
    var interval_id = setInterval("callRoomdData('<?=URL_ROOT;?>','<?=$base_room_file;?>')",1000);
    $("#load_board_data").change(function(){
        var form_data=new FormData();
        form_data.append('upload_state','boarddata');
        form_data.append('room_file','<?=$base_room_file;?>');
        form_data.append('room_pass','<?=$room_pass;?>');
        form_data.append('upload_boarddata',document.getElementById('load_board_data').files[0]);
        $('#load_board_data').replaceWith($('#load_board_data').clone(true));
        $.ajax({
            url:'<?=URL_ROOT;?>exe/uploadbgi.php',
            type:'post',
            processData:false,
            contentType:false,
            data:form_data,
        }).done(function(data){
            if(processAfterUploadMemo(data)){
                displayWTL('ボード情報の読込が完了しました。',50,50,40,163,11);
            }
        }).fail(function(xhr,txtstatus,errorthrown){
            displayWTL('アップロードに失敗しました。('+xhr.status+')',50,50,255,0,0);
        }).always(function(data){
            document.getElementById('load_board_data').value='';
        });
    });
    var sendCharDataTimer = setInterval("checkActiveInputCharData()",1000);
});
</script>