<?php 
    $local_page_url='room.php';
    $_SESSION['room_type']='pc';
    require('./s/common/initroom.php');
    $global_page_title='ルーム「'.$room_name.'」｜'.SITE_TITLE;
    $global_page_url=URL_ROOT.$local_page_url;
?>
<!DOCTYPE html>
<html lang="ja">
<?php require(DIR_ROOT.'s/views/head3.php');?>
<body>
<input id="id_room_id" type="hidden" name="xml" value="<?=$base_room_file;?>" />
<input id="id_player_id" type="hidden" name="principal" value="<?=$principal_id;?>" />
<audio id="game_audio" style="display:none;" src="<?=$first_play_music;?>"></audio>
<audio id="se_audio_1" style="display:none;" src="<?=URL_ROOT;?>sounds/se01.mp3"></audio>
<div id="wdf" class="room_bg">
    <ul id="main_menu">
        <li class="mm_l"><span id="mm_00" class="mm_ck" onClick="pushMainMenu('00');">メモ</span></li>
        <li class="mm_l"><span id="mm_20" class="<?=$observer_flag!=1?'mm_ck':'mm_no';?>"<?=$observer_flag!=1?' onClick="pushMainMenu(\'20\');"':'';?>>カウンター</span></li>
        <li class="mm_l"><span id="mm_05" class="mm_ck" onClick="pushMainMenu('05');">ダイス</span></li>
        <li class="mm_l"><span id="mm_06" class="mm_ck" onClick="pushMainMenu('06');">チャット</span></li>
        <li class="mm_l"><span id="mm_07" class="mm_ck" onClick="pushMainMenu('07');">ボード</span></li>
        <li class="mm_l"><span id="mm_01" class="<?=$observer_flag!=1?'mm_ck':'mm_no';?>"<?=$observer_flag!=1?' onClick="pushMainMenu(\'01\');"':'';?>>コマ</span></li>
        <li class="mm_l"><span id="mm_11" class="mm_ck" onClick="pushMainMenu('11');">カード</span></li>
        <li class="mm_l"><span id="mm_08" class="<?=$observer_flag!=1?'mm_ck':'mm_no';?>"<?=$observer_flag!=1?' onClick="pushMainMenu(\'08\');"':'';?>>キャラシート</span></li>
        <li class="mm_l"<?=$voice_invite_code==-1?' style="display:none;"':'';?>><span id="mm_04" class="mm_ck" onClick="pushMainMenu('04');">ボイス</span></li>
        <li class="mm_l"><span id="mm_02" class="<?=$observer_flag!=1?'mm_ck':'mm_no';?>"<?=$observer_flag!=1?' onClick="pushMainMenu(\'02\');"':'';?>>設定</span></li>
        <li class="mm_l"><span id="mm_09" class="mm_ck" onClick="pushMainMenu('09');">キャラ一覧</span></li>
        <li class="mm_l"><span id="mm_10" class="mm_ck" onClick="pushMainMenu('10');">参加者一覧</span></li>
        <li class="mm_l"><div class="ab_roomname">参:<span id="id_c_participant"><?=$count_participant;?></span>見:<span id="id_c_observer"><?=$count_observer;?></span><span id="id_c_roomname"><?=$room_name;?></span></div></li>
        <li class="mm_l"><span id="mm_97" class="mm_ck" onClick="setDefaultPositionPUW(false);">初期位置に戻す</span></li>
        <li class="mm_r"><a id="mm_98" class="mm_ck" href="<?=URL_ROOT;?>exit.php">退室する</a></li>
    </ul>
    <?php 
        if(file_exists(DIR_ROOT.'s/views/roomad.php')){
            require(DIR_ROOT.'s/views/roomad.php');
        }
    ?>
    <div id="puw_00" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('00')">
            <table><tr>
                <td class="text_left">メモ</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('00');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_00" class="puw_inbody">
                <div class="ltabbody">
                    <?php require(DIR_ROOT.'s/views/roommemo.php');?>
                </div>
            </div>
        </div>
    </div>
    <div id="puw_01" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('01')">
            <table><tr>
                <td class="text_left">コマ</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('01');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_01" class="puw_inbody ltabbody">
                <?php require(DIR_ROOT.'s/views/roomchessman.php');?>
            </div>
        </div>
    </div>
    <div id="puw_02" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('02')">
            <table><tr>
                <td class="text_left">設定</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('02');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_02" class="puw_inbody ltabbody">
                <div class="lt_t_tab">
                    <span id="setting_block_tab1" class="lt_t_tab_actived" onClick="pushSettingTab(1)">共通</span>
                    <span id="setting_block_tab2" onClick="pushSettingTab(2)">個別</span>
                    <span id="setting_block_tab3" onClick="pushSettingTab(3)">カード</span>
                    <span id="setting_block_tab4" onClick="pushSettingTab(4)">URL</span>
                    <span id="setting_block_tab5" onClick="pushSettingTab(5)">ヘルプ</span>
                </div>
                <div id="setting_block1" class="tab_block">
                    <?php require(DIR_ROOT.'s/views/roomsetting.php');?>
                </div>
                <div id="setting_block2" class="tab_block" style="display:none;">
                    <?php require(DIR_ROOT.'s/views/roomsetting2.php');?>
                    <div class="room_setting_box">
                        <p><label>GM補助</label></p>
                        <p><input type="button" value="シナリオセットウィンドウ" style="padding:0 4px;font-size:10px;" onClick="pushMainMenu('21');" /></p>
                    </div>
                </div>
                <div id="setting_block3" class="tab_block" style="display:none;">
                    <?php require(DIR_ROOT.'s/views/roomcardset.php');?>
                </div>
                <div id="setting_block4" class="tab_block" style="display:none;">
                    <?php require(DIR_ROOT.'s/views/roomurl.php');?>
                </div>
                <div id="setting_block5" class="tab_block" style="display:none;">
                    <?php require(DIR_ROOT.'s/views/roomhelp.php');?>
                </div>
            </div>
        </div>
    </div>
    <div id="puw_04" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('04')">
            <table><tr>
                <td class="text_left">ボイス</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('04');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_04" class="puw_inbody ltabbody">
                <?php require(DIR_ROOT.'s/views/roomvoice.php');?>
            </div>
        </div>
    </div>
    <div id="puw_05" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('05')">
            <table><tr>
                <td class="text_left">ダイス</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('05');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_05" class="puw_inbody">
                <?php require(DIR_ROOT.'s/views/roomdiceroll.php');?>
            </div>
        </div>
    </div>
    <div id="puw_06" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('06')">
            <table><tr>
                <td class="text_left">チャット</td>
                <td class="text_right"><span id="puw_ms_06" class="puw_maximum_size" style="display:inline-block;" onClick="chageMaxHeightPUW('06','comment_space');">&nbsp;</span>
                                       <span id="puw_ss_06" class="puw_standard_size" style="display:none;" onClick="chageBaseHeightPUW('06','comment_space');">&nbsp;</span>
                                       <span class="puw_close" onClick="closePopupWindow('06');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_06">
                <?php require(DIR_ROOT.'s/views/roomchatspace.php');?>
            </div>
        </div>
    </div>
    <div id="puw_19" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('19')">
            <table><tr>
                <td id="puw_19_head_title" class="text_left">マッピングツール</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('19');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_19" class="puw_inbody ctabbody">
                <?php require(DIR_ROOT.'s/views/roommappingtool.php');?>
            </div>
        </div>
    </div>
    <div id="puw_07" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('07')">
            <table style="width:100%;"><tr>
                <td class="text_left">ボード</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('07');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="boardlabel" class="clearfix">
                <div class="float_left">
                    <span id="bl_tab_0" class="bl_buttom_actived" onClick="pushBoardLabelTab(0)">コマ操作</span>&nbsp;
                    <span id="bl_tab_1" onClick="pushBoardLabelTab(1)">マッピング</span>
                </div>
                <div class="float_right">
                    <a id="bl_tab_2" href="<?=URL_ROOT;?>exe/download-board.php?i=<?=$base_room_file;?>" style="text-decoration:none;" target="_blank"><span>保存</span></a>&nbsp;
                    <label id="bl_tab_3" for="load_board_data">読込<input type="file" id="load_board_data" accept="text/xml,application/xml" style="display:none;"></label>
                </div>
            </div>
            <div id="puw_inbody_07" class="puw_inbody">
                <?php require(DIR_ROOT.'s/views/roomchessboard.php');?>
            </div>
            <img id="trashbox"
                 draggable="false"
                 ondrop="dropChessman(event);event.preventDefault();"
                 ondragenter="event.preventDefault();" 
                 ondragover="event.preventDefault();" 
                 src="<?=URL_ROOT?>/images/trashbox.png" 
                 width="64" height="64" />
            <div id="stand_img_box" style="display:block;position:absolute;bottom:0;">
                <img id="stand_img0" class="stand_img" src="" style="position:absolute;display:none;bottom:0;" />
                <img id="stand_img1" class="stand_img" src="" style="position:absolute;display:none;bottom:0;" />
                <img id="stand_img2" class="stand_img" src="" style="position:absolute;display:none;bottom:0;" />
            </div>
            <?=$observer_flag!=1?'':'<div class="dont_touch_screen"></div>';?>
        </div>
    </div>
    <div id="puw_08" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('08')">
            <table><tr>
                <td class="text_left">キャラシート</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('08');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_08" class="puw_inbody ctabbody">
                <?php require(DIR_ROOT.'s/views/roomcharsheet.php');?>
            </div>
        </div>
    </div>
    <div id="puw_09" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('09')">
            <table><tr>
                <td class="text_left">キャラ一覧</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('09');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_09" class="puw_inbody">
                <div id="character_space"></div>
            </div>
            <?=$observer_flag!=1?'':'<div class="dont_touch_screen"></div>';?>
        </div>
    </div>
    <div id="puw_10" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('10')">
            <table><tr>
                <td class="text_left">参加者一覧</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('10');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_10" class="puw_inbody">
                <div id="participant_space"></div>
            </div>
        </div>
    </div>
    <div id="puw_11" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('11')">
            <table style="width:100%;"><tr>
                <td class="text_left">カード</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('11');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_11" class="puw_inbody">
                <?php require(DIR_ROOT.'s/views/roomcardspace.php');?>
            </div>
            <?=$observer_flag!=1?'':'<div class="dont_touch_screen"></div>';?>
        </div>
    </div>
    <div id="puw_13" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('13')">
            <table><tr>
                <td id="puw_13_head_title" class="text_left">情報</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('13');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_13" class="puw_inbody ctabbody">
                <pre id="explanation_space" class="equal_interval_font" style="font-size:14px;margin-top:10px;">
            </div>
        </div>
    </div>
    <div id="puw_20" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('20')">
            <table><tr>
                <td class="text_left">カウンター</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('20');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_20" class="puw_inbody ltabbody">
                <?php require(DIR_ROOT.'s/views/roomtimer.php');?>
            </div>
        </div>
    </div>
    <div id="puw_21" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('21')">
            <table><tr>
                <td class="text_left">シナリオセット</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('21');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_21" class="puw_inbody ltabbody">
                <?php require(DIR_ROOT.'s/views/roomscenarioset.php');?>
            </div>
        </div>
    </div>
    <div id="puw_22" class="popup_window" style="display:none;">
        <div class="puw_head" onMousedown="sortPUWzIndex('22')">
            <table><tr>
                <td class="text_left">マクロリスト</td>
                <td class="text_right"><span class="puw_close" onClick="closePopupWindow('22');">&nbsp;</span></td>
            </tr></table>
        </div>
        <div class="puw_body">
            <div id="puw_inbody_22" class="puw_inbody ltabbody">
                <?php require(DIR_ROOT.'s/views/roomchatpalette.php');?>
            </div>
        </div>
    </div>
</div>
<div id="notice_box" style="position:fixed;display:inline-block;top:0;left:0;padding:6px;background-color:#FFF;border:1px solid #AAA;border-radius:4px;z-index:99;visibility:hidden;" ></div>
<div id="loading_box" style="position:fixed;display:none;top:0;left:0;width:208px;height:45px;padding:10px;background-color:#FFF;border:1px solid #AAA;border-radius:4px;z-index:99;" onClick="this.style.display='none';" ><img src="<?=URL_ROOT;?>images/loading2.gif" width="208" height="45" border="0" alt="LOADING..." /></div>
</body>
</html>
<script>
/*////////////////////////////////////////////////////////////////
variable
////////////////////////////////////////////////////////////////*/
var now_utsp=Math.floor(original_client_time/1000);
var obj_character_list=convertJsonTextToObject('<?=$c_character->convertCharListJsonFromArray($characterlist_array);?>');
listStandData=[[(now_utsp-10),'',0,0,''],[(now_utsp-10),'',0,0,''],[(now_utsp-10),'',0,0,'']];
var countPopInWindow=0; // ポップアップウィンドウの初期位置設定
var baseHeightPUW={};
var listPreviewMemoData=[
    ['','',''],
    ['','',''],
    ['','',''],
    ['','',''],
    ['','','']
];
var puw_no_list=[<?php if(file_exists(DIR_ROOT.'s/views/roomad.php')){echo '\'99\',';} ?>'00','01','02','04','05','06','19','07','08','09','10','11','13','20','21','22'];
/*////////////////////////////////////////////////////////////////
function
////////////////////////////////////////////////////////////////*/
function pushSettingTab(tab_key){
    for(var i=1;i<6;i++){
        if(i==tab_key){
            document.getElementById('setting_block_tab'+i).classList.add("lt_t_tab_actived");
            document.getElementById('setting_block'+i).style.display="block";
        }else{
            document.getElementById('setting_block_tab'+i).classList.remove("lt_t_tab_actived");
            document.getElementById('setting_block'+i).style.display="none";
        }
    }
}
function setDefaultPositionPUW(boot_flag){
    if(boot_flag==true){
        document.getElementById('puw_inbody_00').style.height='642px';
        document.getElementById('puw_00').style.width='284px';
        document.getElementById('puw_00').style.height='680px';
    }
    document.getElementById('puw_00').style.top='42px';
    document.getElementById('puw_00').style.left='3px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_01').style.height='642px';
        document.getElementById('puw_01').style.width='284px';
        document.getElementById('puw_01').style.height='680px';
    }
    document.getElementById('puw_01').style.top='42px';
    document.getElementById('puw_01').style.left='3px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_02').style.height='642px';
        document.getElementById('puw_02').style.width='284px';
        document.getElementById('puw_02').style.height='680px';
    }
    document.getElementById('puw_02').style.top='42px';
    document.getElementById('puw_02').style.left='3px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_04').style.height='642px';
        document.getElementById('puw_04').style.width='284px';
        document.getElementById('puw_04').style.height='680px';
    }
    document.getElementById('puw_04').style.top='42px';
    document.getElementById('puw_04').style.left='3px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_05').style.height='642px';
        document.getElementById('puw_05').style.width='284px';
        document.getElementById('puw_05').style.height='680px';
    }
    document.getElementById('puw_05').style.top='725px';
    document.getElementById('puw_05').style.left='3px';
    if(boot_flag==true){
        document.getElementById('comment_space').style.height='102px';
        document.getElementById('puw_06').style.width='785px';
        document.getElementById('puw_06').style.height='220px';
    }
    document.getElementById('puw_06').style.top='725px';
    document.getElementById('puw_06').style.left='290px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_07').style.height='622px';
        document.getElementById('puw_07').style.width='562px';
        document.getElementById('puw_07').style.height='680px';
    }
    document.getElementById('puw_07').style.top='42px';
    document.getElementById('puw_07').style.left='290px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_08').style.height='642px';
        document.getElementById('puw_08').style.width='562px';
        document.getElementById('puw_08').style.height='680px';
    }
    document.getElementById('puw_08').style.top='42px';
    document.getElementById('puw_08').style.left='290px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_09').style.height='642px';
        document.getElementById('puw_09').style.width='220px';
        document.getElementById('puw_09').style.height='680px';
    }
    document.getElementById('puw_09').style.top='42px';
    document.getElementById('puw_09').style.left='855px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_10').style.height='642px';
        document.getElementById('puw_10').style.width='220px';
        document.getElementById('puw_10').style.height='680px';
    }
    document.getElementById('puw_10').style.top='42px';
    document.getElementById('puw_10').style.left='855px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_11').style.height='642px';
        document.getElementById('puw_11').style.width='562px';
        document.getElementById('puw_11').style.height='680px';
    }
    document.getElementById('puw_11').style.top='42px';
    document.getElementById('puw_11').style.left='290px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_13').style.height='642px';
        document.getElementById('puw_13').style.width='562px';
        document.getElementById('puw_13').style.height='680px';
    }
    document.getElementById('puw_13').style.top='42px';
    document.getElementById('puw_13').style.left='290px';
    if(boot_flag==true){
        document.getElementById('puw_19').style.width='284px';
        document.getElementById('puw_19').style.height='680px';
        document.getElementById('puw_inbody_19').style.height='642px';
    }
    document.getElementById('puw_19').style.top='42px';
    document.getElementById('puw_19').style.left='3px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_20').style.height='642px';
        document.getElementById('puw_20').style.width='284px';
        document.getElementById('puw_20').style.height='680px';
    }
    document.getElementById('puw_20').style.top='42px';
    document.getElementById('puw_20').style.left='3px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_21').style.height='642px';
        document.getElementById('puw_21').style.width='562px';
        document.getElementById('puw_21').style.height='680px';
    }
    document.getElementById('puw_21').style.top='42px';
    document.getElementById('puw_21').style.left='290px';
    if(boot_flag==true){
        document.getElementById('puw_inbody_22').style.height='342px';
        document.getElementById('puw_22').style.width='388px';
        document.getElementById('puw_22').style.height='380px';
    }
    document.getElementById('puw_22').style.top='342px';
    document.getElementById('puw_22').style.left='855px';
    <?php 
        if(file_exists(DIR_ROOT.'s/views/roomad.php')){
            echo 'if(boot_flag==true){';
            echo 'document.getElementById(\'puw_99\').style.width=\'168px\';';
            echo 'document.getElementById(\'puw_99\').style.height=\'1860px\';';
            echo '}';
            echo 'document.getElementById(\'puw_99\').style.top=\'42px\';';
            echo 'document.getElementById(\'puw_99\').style.left=\'1078px\';';
        }
    ?>
    var fixDefaultWidth=getFixDefaultWidth();
    if(fixDefaultWidth>1){
        document.getElementById('puw_00').style.left=(parseInt(document.getElementById('puw_00').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_01').style.left=(parseInt(document.getElementById('puw_01').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_02').style.left=(parseInt(document.getElementById('puw_02').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_04').style.left=(parseInt(document.getElementById('puw_04').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_05').style.left=(parseInt(document.getElementById('puw_05').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_06').style.left=(parseInt(document.getElementById('puw_06').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_07').style.left=(parseInt(document.getElementById('puw_07').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_08').style.left=(parseInt(document.getElementById('puw_08').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_09').style.left=(parseInt(document.getElementById('puw_09').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_10').style.left=(parseInt(document.getElementById('puw_10').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_11').style.left=(parseInt(document.getElementById('puw_11').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_13').style.left=(parseInt(document.getElementById('puw_13').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_19').style.left=(parseInt(document.getElementById('puw_19').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_20').style.left=(parseInt(document.getElementById('puw_20').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_21').style.left=(parseInt(document.getElementById('puw_21').style.left)+fixDefaultWidth)+'px';
        document.getElementById('puw_22').style.left=(parseInt(document.getElementById('puw_22').style.left)+fixDefaultWidth)+'px';
        <?php 
            if(file_exists(DIR_ROOT.'s/views/roomad.php')){
                echo 'document.getElementById(\'puw_99\').style.left=(parseInt(document.getElementById(\'puw_99\').style.left)+fixDefaultWidth)+\'px\';';
            }
        ?>
    }
}
function pushMainMenu(element_id){
    var mmElement=document.getElementById('mm_'+element_id);
    var puwElement=document.getElementById('puw_'+element_id);
	if(puwElement.style.display=='none'){
        countPopInWindow++;
        puwElement.style.zIndex=countPopInWindow;
        puwElement.style.display='block';
        if(element_id=='02'){
            flagOpenRoomStting=1;
        }
		if(mmElement){
			mmElement.classList.add('mm_ckd');
			mmElement.classList.remove('mm_ck');
		}
	}else{
        puwElement.style.display='none';
        if(element_id=='02'){
            flagOpenRoomStting=0;
        }
		if(mmElement){
			mmElement.classList.add('mm_ck');
			mmElement.classList.remove('mm_ckd');
		}
    }
}
function closePopupWindow(element_id){
    var puwElement=document.getElementById('puw_'+element_id);
    puwElement.style.display='none';
    if(element_id=='02'){
        flagOpenRoomStting=0;
    }else if(element_id=='19'){
        pushBoardLabelTab(0);
    }
    var mmElement=document.getElementById('mm_'+element_id);
	if(mmElement){
		mmElement.classList.add('mm_ck');
		mmElement.classList.remove('mm_ckd');
	}
}
function chageMaxHeightPUW(fix_id,target_id){
    var fixElement=document.getElementById('puw_'+fix_id);
    var targetElement=document.getElementById(target_id);
    var fixHeight=parseInt(fixElement.style.height);
    var targetHeight=targetElement.offsetHeight;
    var fixValue=targetElement.scrollHeight-targetHeight;
    baseHeightPUW[fix_id]={0:fixHeight,1:targetHeight};
    fixElement.style.height=(fixHeight+fixValue)+'px';
    targetElement.style.height=(targetHeight+fixValue)+'px';
    document.getElementById('puw_ms_'+fix_id).style.display='none';
    document.getElementById('puw_ss_'+fix_id).style.display='inline-block';
}
function chageBaseHeightPUW(fix_id,target_id){
    var fixElement=document.getElementById('puw_'+fix_id);
    var targetElement=document.getElementById(target_id);
    fixElement.style.height=baseHeightPUW[fix_id][0]+'px';
    targetElement.style.height=baseHeightPUW[fix_id][1]+'px';
    document.getElementById('puw_ms_'+fix_id).style.display='inline-block';
    document.getElementById('puw_ss_'+fix_id).style.display='none';
}
function openPopUpWindow(nodeMemos){
	for(var key in listMemoData){
		if(listMemoData[key]['flag']>listMemoData[key]['last']){
			var node=document.getElementById('puw_memo_'+key);
			if(node){
				if(node.style.display=='none'){
					pushMainMenu('memo_'+key);
				}
				listMemoData[key]['last']=listMemoData[key]['flag'];
				if(listMemoData[key]['eid']!=''){
					clearTimeout(listMemoData[key]['eid']);
					listMemoData[key]['eid']='';
				}
				if(listMemoData[key]['limit']>0){
					shutPopUpWindow(key,listMemoData[key]['limit']);
				}else{
					document.getElementById('puw_memo_'+key+'_head_title').innerHTML='共通メモ'+key;
				}
			}
		}
	}
}
function shutPopUpWindow(id,limit_time){
    // puw_no = 1~5
	var node=document.getElementById('puw_memo_'+id+'_head_title');
	if(node){
		node.innerHTML='共通メモ'+id+'（残り'+limit_time+'秒）';
		if(limit_time<=0){
			if(document.getElementById('puw_memo_'+id).style.display!='none'){
				pushMainMenu('memo_'+id);
			}
			return true;
		}
		listMemoData[id]['eid']=setTimeout(function(){
			limit_time--;
			shutPopUpWindow(id,limit_time);
		},1000);
		return true;
	}
	return false;
}
function openExplanationWindow(char_id,owner_id,target_data,title){
    var explanationElement=document.getElementById('explanation_space');
    if(explanationElement){
        explanationElement.innerHTML='';
        $.post('<?=URL_ROOT;?>exe/loadcharacterdata.php',{
            room_id:'<?=$base_room_file;?>',
            principal_id:owner_id,
            char_id:char_id,
        },function(data){
            var json_char_data=convertJsonTextToObject(data);
            if(json_char_data.error){
                displayWTL(json_char_data.error_description,50,50,255,0,0);
            }else{
                if(target_data=='detail_a'){
                    explanationElement.innerHTML=json_char_data.detail_a;
                }else if(target_data=='detail_b'){
                    explanationElement.innerHTML=json_char_data.detail_b;
                }
            }
            document.getElementById('puw_13_head_title').innerHTML=title;
			var node=document.getElementById('puw_13');
            node.style.zIndex=countPopInWindow+1;
            if(node.style.display=='none'){
                pushMainMenu('13');
            }
        },'html');
    }else{
        return false;
    }
}
function sortPUWzIndex(last_row){
    var max_countPopInWindow=0;
    var puw_obj_list=[];
    for(var i=0;i<puw_no_list.length;i++){
        puw_obj_list[i]=[];
        puw_obj_list[i][0]=document.getElementById('puw_'+puw_no_list[i]);
        puw_obj_list[i][1]=Number(puw_obj_list[i][0].style.zIndex);
        if(puw_obj_list[i][1]>max_countPopInWindow){
            max_countPopInWindow=puw_obj_list[i][1];
        }
    }
    puw_obj_list.sort(function(a,b){
        if(a[1]<b[1]) return -1;
        if(a[1]>b[1]) return 1;
        return 0;
    });
    for(var i=0;i<puw_no_list.length;i++){
        puw_obj_list[i][0].style.zIndex=i;
    }
    document.getElementById('puw_'+last_row).style.zIndex=puw_no_list.length;
    countPopInWindow=puw_no_list.length;
}
function displayStandImage(resHTTP){
    var nood_stand=[document.getElementById('stand_img0'),
                    document.getElementById('stand_img1'),
                    document.getElementById('stand_img2')];
    var m_stand=[];
    for(var stand_no=0;stand_no<3;stand_no++){
        m_stand[stand_no]=resHTTP.getElementsByTagName('stand'+stand_no);
        if(m_stand[stand_no][0]!=null){
            listStandData[stand_no][1]=m_stand[stand_no][0].getElementsByTagName('eimg')[0].textContent;
            if(listStandData[stand_no][1]!=''){
                listStandData[stand_no][2]=parseInt(m_stand[stand_no][0].getElementsByTagName('ew')[0].textContent);
                listStandData[stand_no][3]=parseInt(m_stand[stand_no][0].getElementsByTagName('eh')[0].textContent);
                listStandData[stand_no][4]=m_stand[stand_no][0].getElementsByTagName('enm')[0].textContent;
                var setup_time=parseInt(m_stand[stand_no][0].getElementsByTagName('etm')[0].textContent);
                if(setup_time>listStandData[stand_no][0]){
                    var nood_base_position=document.getElementById('puw_07');
                    var size_rate=1;
                    var max_height=parseInt(nood_base_position.style.height)*0.8;
                    if(listStandData[stand_no][3]>max_height){
                        size_rate=max_height/listStandData[stand_no][3];
                    }
                    nood_stand[stand_no].src='<?=URL_ROOT;?>r/n/<?=$base_room_file;?>/'+listStandData[stand_no][1];
                    nood_stand[stand_no].width=Math.floor(listStandData[stand_no][2]*size_rate);
                    nood_stand[stand_no].height=Math.floor(listStandData[stand_no][3]*size_rate);
                    nood_stand[stand_no].style.opacity='1.0';
                    nood_stand[stand_no].style.zIndex='102';
                    nood_stand[stand_no].style.display='inline-block';
                    for(var j=0;j<3;j++){
                        if(j==stand_no){
                            if(stand_no==0){
                                nood_stand[stand_no].style.left='0px';
                            }else if(stand_no==1){
                                nood_stand[stand_no].style.left=Math.floor((parseInt(nood_base_position.style.width)-(listStandData[stand_no][2]*size_rate))/2)+'px';
                            }else if(stand_no==2){
                                nood_stand[stand_no].style.left=Math.floor(parseInt(nood_base_position.style.width)-(listStandData[stand_no][2]*size_rate))+'px';
                            }
                        }else{
                            nood_stand[j].style.zIndex=100;
                            nood_stand[j].style.opacity='0.5';
                            if(listStandData[j][4]==listStandData[stand_no][4]){
                                nood_stand[j].style.display='none';
                            }
                        }
                    }
                    listStandData[stand_no][0]=setup_time;
                }
            }else{
                listStandData[stand_no][2]=0;
                listStandData[stand_no][3]=0;
                listStandData[stand_no][4]='';
                if(nood_stand[stand_no].style.display!='none'){
                    nood_stand[stand_no].style.display='none';
                }
            }
        }
    }
}
/*////////////////////////////////////////////////////////////////
events
////////////////////////////////////////////////////////////////*/
document.getElementById('game_audio').addEventListener('play',
    function(){
        audio_state='play';
    }
);
document.getElementById('game_audio').addEventListener('pause',
    function(){
        audio_state='pause';
    }
);
document.addEventListener("dragstart",
    function(e){
        var eventinit=e.target;
        if(eventinit.tagName=='IMG'){
            document.getElementById("stand_img_box").style.display='none';
            
        }
    }
);
document.addEventListener("dragend",
    function(e){
        var stand_img_box=document.getElementById("stand_img_box")
        if(stand_img_box.style.display=='none'){
            stand_img_box.style.display='block';
        }
    }
);
document.addEventListener("drop",
    function(e){
        var stand_img_box=document.getElementById("stand_img_box")
        if(stand_img_box.style.display=='none'){
            stand_img_box.style.display='block';
        }
    }
);
/*////////////////////////////////////////////////////////////////
before load process
////////////////////////////////////////////////////////////////*/
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
    // ポップアップウィンドウの初期設置
    setDefaultPositionPUW(true);
    $("#puw_00").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_00").resizable({minHeight:510,minWidth:284,alsoResize:"#puw_inbody_00"});
    $("#puw_01").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_01").resizable({minHeight:120,minWidth:284,alsoResize:"#puw_inbody_01"});
    $("#puw_02").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_02").resizable({minHeight:120,minWidth:284,alsoResize:"#puw_inbody_02"});
    $("#puw_04").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_04").resizable({minHeight:120,minWidth:284,alsoResize:"#puw_inbody_04"});
    $("#puw_05").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_05").resizable({minHeight:120,minWidth:284,alsoResize:"#puw_inbody_05"});
    $("#puw_06").draggable({handle:"div.puw_head",cancel:"span.puw_close,#puw_ms_06,#puw_ss_06"});
    $("#puw_06").resizable({minHeight:120,minWidth:562,alsoResize:"#comment_space"});
    $("#puw_07").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_07").resizable({minHeight:340,minWidth:562,alsoResize:"#puw_inbody_07"});
    $("#puw_08").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_08").resizable({minHeight:340,minWidth:562,alsoResize:"#puw_inbody_08"});
    $("#puw_09").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_09").resizable({minHeight:150,minWidth:220,alsoResize:"#puw_inbody_09"});
    $("#puw_10").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_10").resizable({minHeight:88,minWidth:220,alsoResize:"#puw_inbody_10"});
    $("#puw_11").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_11").resizable({minHeight:340,minWidth:562,alsoResize:"#puw_inbody_11"});
    $("#puw_13").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_13").resizable({minHeight:120,minWidth:284,alsoResize:"#puw_inbody_13"});
    $("#puw_19").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_19").resizable({minHeight:120,minWidth:284,alsoResize:"#puw_inbody_19"});
    $("#puw_20").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_20").resizable({minHeight:120,minWidth:284,alsoResize:"#puw_inbody_20"});
    $("#puw_21").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_21").resizable({minHeight:340,minWidth:340,alsoResize:"#puw_inbody_21"});
    $("#puw_22").draggable({handle:"div.puw_head",cancel:"span.puw_close"});
    $("#puw_22").resizable({minHeight:120,minWidth:220,alsoResize:"#puw_inbody_22"});
    <?php 
        if(file_exists(DIR_ROOT.'s/views/roomad.php')){
            echo '$("#puw_99").draggable({handle:"div.puw_head"});';
            echo 'document.getElementById(\'puw_99\').style.display=\'block\';';
        } 
    ?>
    pushMainMenu('07');
    pushMainMenu('00');
    pushMainMenu('09');
    pushMainMenu('05');
    pushMainMenu('06');
    var sendCharDataTimer=setInterval("checkActiveInputCharData()",1000);
});
</script>