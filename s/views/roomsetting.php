<div class="room_setting_box">
    <p>ボードのマス数&nbsp;<span class="font_blue">（min=x:17&nbsp;y:20,&nbsp;max=100）</span></p>
    <p style="margin-top:5px;">幅:&nbsp;<input id="game_boardwidth" type="number" min="17" max="100" value="17" onFocus="doNotUpdate('game_boardwidth');" onblur="setModSettingData('game_boardwidth');" style="padding:2px 4px;width:40px;font-size:10px;" />&nbsp;
       高さ:&nbsp;<input id="game_boardheight" type="number" min="20" max="100" value="20" onFocus="doNotUpdate('game_boardheight');" onblur="setModSettingData('game_boardheight');" style="padding:2px 4px;width:40px;font-size:10px;" />&nbsp;
       <input type="button" value="セット" style="padding:0 4px;font-size:10px;" onClick="setBoardSize();" /></p>
</div>
<div class="m_top_10 room_setting_box_wb">
    <p>グリッド</p>
    <p>濃さ:&nbsp;MIN&nbsp;<input id="game_grid" type="range" style="width:120px;vertical-align:middle;" value="5" min="0" max="10" step="1" />&nbsp;MAX</p>
</div>

<div class="room_setting_box_wb">
    <p>背景</p>
    <input id="game_backimage" type="hidden" value="<?=(string)$xml->head->game_backimage;?>" />
    <p><select id="game_backimage_select" onFocus="doNotUpdate('game_backimage_select');" onChange="setBackImage();" onblur="setBackImage();" style="padding:2px 4px;width:140px;font-size:10px;">
        <?php
            $uploaded_file_flag=false;
            if((string)$xml->head->game_backimagelist!=''){
                if(file_exists(str_replace(URL_ROOT,DIR_ROOT,(string)$xml->head->game_backimagelist))){
                    $uploadimage_url=(string)$xml->head->game_backimagelist;
                    $uploaded_file_flag=true;
                }
            }
            if($uploaded_file_flag==true){
                $backimage_list_array[]=array($uploadimage_url,'アップロード画像');
            }else{
                $backimage_list_array[]=array('_nothing_image_','アップロード画像');
            }
            $str_backimage_option='';
            foreach($backimage_list_array as $value){
                if($value[1]=='アップロード画像'){
                    if($uploaded_file_flag==true){
                        $str_backimage_option.='<option id="id_uploadimage_o" value="'.$value[0].'" '.($value[0]==(string)$xml->head->game_backimage?'selected="selected"':'').'>アップロード画像</option>';
                    }else{
                        $str_backimage_option.='<option id="id_uploadimage_o" value="_nothing_image_" disabled="disabled" >アップロード画像</option>';
                    }
                }else{
                    $str_backimage_option.='<option value="'.$value[0].'" '.($value[0]==(string)$xml->head->game_backimage?'selected="selected"':'').'>'.$value[1].'</option>';
                }
            }
            echo $str_backimage_option;
        ?>
    </select>&nbsp;
    <input type="button" value="セット" style="padding:0 4px;font-size:10px;" onClick="setBackImage();" /></p>
    <form id="upload_image_form" style="margin-top:5px;" enctype="multipart/form-data" method="post" action="<?=$global_page_url;?>">
        <p>背景画像のアップロード<span class="font_blue small">（<?=displaySimpleByte(UPLOAD_BI_LIMIT_SIZE);?>までのJPEG,PNG,GIF）</span></p>
        <p><input type="file" name="upload_image" accept="image/jpeg,image/png,image/gif" style="font-size:10px;" /></p>
        <p><input type="button" name="submit_button" onClick="uploadBgiFile('upload_image_form');" style="margin-top:10px;padding:0 4px;font-size:10px;" value="アップロードする" /></p>
        <p class="font_gray small">※&nbsp;ボードのマスは1あたり32x32pxです。</p>
        <input type="hidden" name="upload_state" value="image" />
        <input type="hidden" name="room_file" value="<?=$base_room_file;?>" />
        <?php if($observer_flag!=1){ ?> 
        <input type="hidden" name="room_pass" value="<?=$room_pass;?>" />
        <?php } ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=UPLOAD_BI_LIMIT_SIZE;?>" />
    </form>
    <p style="margin-top:5px;">濃さ:&nbsp;MIN&nbsp;<input id="game_imagestrength" type="range" style="width:120px;vertical-align:middle;" value="5" min="0" max="10" step="1" />&nbsp;MAX</p>
    <p style="margin-top:5px;"><input id="game_syncboardsize" type="checkbox" style="vertical-align:middle;" value="1" onFocus="doNotUpdate('game_syncboardsize');" onClick="setModSettingData('game_syncboardsize');" <?=($game_syncboardsize!=0?'checked="checked"':'');?> />&nbsp;背景画像を原寸サイズで表示する</p>
</div>

<div class="room_setting_box_wb">
    <p>マップチップ</p>
    <input id="game_mapchip" type="hidden" value="<?=$game_mapchip_img;?>" />
    <p><select id="game_mapchip_select" onFocus="doNotUpdate('game_mapchip_select');" onChange="setMapChipImg();" onblur="setMapChipImg();" style="padding:2px 4px;width:140px;font-size:10px;">
        <?php
            $uploaded_file_flag=false;
            foreach(glob(DIR_ROOT.'r/n/'.$base_room_file.'/uploadmapchip*') as $value){
                $uploaded_file_flag=true;
                $uploadmapchip_url=str_replace(DIR_ROOT,URL_ROOT,$value);
                break;
            }
            if($uploaded_file_flag==true){
                $mapchip_list_array[]=array($uploadmapchip_url,'アップロードMC');
            }else{
                $mapchip_list_array[]=array('_nothing_image_','アップロードMC');
            }
            $str_mapchip_option='';
            foreach($mapchip_list_array as $value){
                if($value[1]=='アップロードMC'){
                    if($uploaded_file_flag==true){
                        $str_mapchip_option.='<option id="id_uploadmapchip_o" value="'.$value[0].'" '.($value[0]==$game_mapchip_img?'selected="selected"':'').'>アップロードMC</option>';
                    }else{
                        $str_mapchip_option.='<option id="id_uploadmapchip_o" value="_nothing_image_" disabled="disabled" >アップロードMC</option>';
                    }
                }else{
                    $str_mapchip_option.='<option value="'.$value[0].'" '.($value[0]==$game_mapchip_img?'selected="selected"':'').'>'.$value[1].'</option>';
                }
            }
            echo $str_mapchip_option;
        ?>
    </select>&nbsp;
    <input type="button" value="セット" style="padding:0 4px;font-size:10px;" onClick="setMapChipImg();" /></p>
    <form id="upload_mapchip_form" style="margin-top:5px;" enctype="multipart/form-data" method="post" action="<?=$global_page_url;?>">
        <p>マップチップのアップロード<span class="font_blue small">（<?=displaySimpleByte(UPLOAD_BI_LIMIT_SIZE);?>までのPNG）</span></p>
        <p><input type="file" name="upload_image" accept="image/png" style="font-size:10px;" /></p>
        <p><input type="button" name="submit_button" onClick="uploadBgiFile('upload_mapchip_form');" style="margin-top:10px;padding:0 4px;font-size:10px;" value="アップロードする" /></p>
        <input type="hidden" name="upload_state" value="mapchip" />
        <input type="hidden" name="room_file" value="<?=$base_room_file;?>" />
        <?php if($observer_flag!=1){ ?> 
        <input type="hidden" name="room_pass" value="<?=$room_pass;?>" />
        <?php } ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=UPLOAD_BI_LIMIT_SIZE;?>" />
    </form>
</div>

<div class="room_setting_box_wb">
    <input id="game_dicebot" type="hidden" value="<?=(string)$xml->head->game_dicebot;?>" />
    <div>ダイスボット</div>
    <div style="margin:2px 0;">
        <label><input name="base_dicebot_type" style="vertical-align:middle;" type="radio" value="1" onClick="putAcDiceBotBox(1)" <?php
            if(isset($bac_gamelist)){
                echo (strpos((string)$xml->head->game_dicebot,'bac_')!==false?'checked="checked"':'');
            }else{
                echo 'disabled="disabled"';
            }
        ?> />Bone＆Cars</label>
		&nbsp;
        <label><input name="base_dicebot_type" style="vertical-align:middle;" type="radio" value="0" onClick="putAcDiceBotBox(0)" <?php
            if(isset($bac_gamelist)){
                echo (strpos((string)$xml->head->game_dicebot,'bac_')===false?'checked="checked"':'');
            }else{
                echo 'checked="checked"';
            }
        ?> />オンセンdb</label>
    </div>
    <div id="id_onsenbot_box" <?php
        if(isset($bac_gamelist)){
            echo (strpos((string)$xml->head->game_dicebot,'bac_')!==false?'style="display:none;"':'');
        }
    ?> >
        <select id="game_dicebot_select" onFocus="doNotUpdate('game_dicebot_select');" onChange="setDiceBotText();" onblur="setDiceBotText();" style="padding:2px 4px;min-width:160px;font-size:10px;">
        <?php
            $v_html='';
            foreach($dicebot_textlist as $key => $value){
                $v_html.='<option value="'.$key.'"';
                if($key==(string)$xml->head->game_dicebot){
                    $v_html.=' selected="selected"';
                }
                $v_html.='>'.$value[0].'</option>';
            }
            echo $v_html;
        ?>
        </select>&nbsp;
        <input type="button" value="セット" style="padding:0 4px;font-size:10px;" onClick="setDiceBotText();" />
    </div>
    <div id="id_boneandcars_box" <?php
        if(isset($bac_gamelist)){
            echo (strpos((string)$xml->head->game_dicebot,'bac_')===false?'style="display:none;"':'');
        }else{
            echo 'style="display:none;"';
        }
    ?> >
        <select id="bac_dicebot_select" onFocus="doNotUpdate('game_dicebot_select');" onChange="setBaCDiceBot();" onblur="setBaCDiceBot();" style="padding:2px 4px;min-width:160px;font-size:10px;">
        <?php
            $v_html='';
            if(isset($bac_gamelist)){
                foreach($bac_gamelist as $key => $value){
                    $v_html.='<option value="'.$key.'"';
                    if($key==(string)$xml->head->game_dicebot){
                        $v_html.=' selected="selected"';
                    }
                    $v_html.='>'.$value[0].'</option>';
                }
            }
            echo $v_html;
        ?>
        </select>&nbsp;
        <input type="button" value="セット" style="padding:0 4px;font-size:10px;" onClick="setBaCDiceBot();" />
    </div>
</div>

<div class="room_setting_box_wb">
    <p>BGM</p>
    <p><select id="game_music_select" style="padding:2px 4px;width:140px;font-size:10px;">
    <?php
        $uploaded_file_flag=false;
        foreach(glob(DIR_ROOT.'r/n/'.$base_room_file.'/uploadmusic*') as $value){
            $uploadmusic_url=str_replace(DIR_ROOT,URL_ROOT,$value);
            $uploaded_file_flag=true;
            break;
        }
        if($uploaded_file_flag==true){
            $music_list_array[]=array($uploadmusic_url,'アップロードBGM');
        }else{
            $music_list_array[]=array('_nothing_music_','アップロードBGM');
        }
        $str_music_option='';
        foreach($music_list_array as $value){
            if($value[1]=='アップロードBGM'){
                if(((string)$room_pass!='')&&((int)$xml->head->tour!=0)){
                    if($uploaded_file_flag==true){
                        $music_url=TRANSFER_PROTOCOL.preg_replace('/^https?:/','',$value[0]);
                        $str_music_option.='<option id="id_uploadmusic_o" value="'.$music_url.'" '.(strpos((string)$xml->head->game_music,$value[0])!==false?'selected="selected"':'').'>アップロードBGM</option>';
                    }else{
                        $str_music_option.='<option id="id_uploadmusic_o" value="_nothing_music_" disabled="disabled" >アップロードBGM</option>';
                    }
                }
            }else{
                $str_music_option.='<option value="'.$value[0].'" '.($value[0]==(string)$xml->head->game_music?'selected="selected"':'').'>'.$value[1].'</option>';
            }
        }
        echo $str_music_option;
    ?>
    </select>&nbsp;
    <input id="game_music_play" type="button"  value="再生" style="padding:0 4px;font-size:10px;" />&nbsp;
    <input id="game_music_stop" type="button" value="停止" style="padding:0 4px;font-size:10px;" />&nbsp;
    <span id="now_music_state"><span style="color:#606060;">■</span></span></p>
    <form id="upload_music_form" style="margin-top:5px;" enctype="multipart/form-data" method="post" action="<?=$global_page_url;?>">
        <p>BGMのアップロード<span class="font_blue small">（<?=displaySimpleByte(UPLOAD_MS_LIMIT_SIZE);?>までのmp3,mpeg,ogg,wav）</span></p>
        <?php
            if(((string)$room_pass!='')&&((int)$xml->head->tour!=0)){
                echo '<p><input type="file" name="upload_music" accept="audio/wav,audio/mpeg,audio/ogg,audio/mp4" style="font-size:10px;" /></p>';
                echo '<p><input type="button" name="submit_button" onClick="uploadBgiFile(\'upload_music_form\');" style="margin-top:10px;padding:0 4px;font-size:10px;" value="アップロードする" /></p>';
            }else{
                echo '<p><input type="file" name="upload_music" accept="audio/wav,audio/mpeg,audio/ogg,audio/mp4" style="font-size:10px;" disabled="disabled" /></p>';
                echo '<p><input type="button" name="submit_button" style="margin-top:10px;padding:0 4px;font-size:10px;" value="アップロードする" disabled="disabled" /></p>';
            }
            if(!empty($upload_music_err)){
                echo '<p class="font_red small">BGMのアップロードは失敗しました。（理由：'.$upload_music_err.'）</p>';
            }
            if(((string)$room_pass!='')&&((int)$xml->head->tour!=0)){
                echo '<p class="font_gray small">※&nbsp;推奨はmp3、それ以外だとブラウザによって再生できない場合があります。</p>';
            }else{
                echo '<p class="font_red small">※&nbsp;BGMのアップロード及び再生は、ルームパスワードあり＆見学不可の場合のみ使用できます。</p>';
            }
        ?>
        <input type="hidden" name="upload_state" value="music" />
        <input type="hidden" name="room_file" value="<?=$base_room_file;?>" />
        <?php if($observer_flag!=1){ ?> 
        <input type="hidden" name="room_pass" value="<?=$room_pass;?>" />
        <?php } ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=UPLOAD_MS_LIMIT_SIZE;?>" />
    </form>
    <input type="hidden" id="game_music" value="<?=(string)$xml->head->game_music;?>" />
    <input type="hidden" id="game_music_state" value="<?=(string)$xml->head->game_music_state;?>" />
</div>
<script>
function setBoardSize(){
    var syncboardsize_flag=0;
    if(document.getElementById('game_syncboardsize').checked==true){
        syncboardsize_flag=1;
    }
    sendSettingData({game_boardwidth:document.getElementById('game_boardwidth').value,
                     game_boardheight:document.getElementById('game_boardheight').value,
                     game_syncboardsize:syncboardsize_flag});
}
function setBackImage(){
    document.getElementById('game_backimage').value=document.getElementById('game_backimage_select').value;
    setModSettingData('game_backimage');
}
function uploadBgiFile(form_id){
    var formElement=$('#'+form_id);
    var formData=new FormData(formElement[0]);
    $.ajax({
        url:'<?=URL_ROOT;?>exe/uploadbgi.php',
        type:'post',
        processData:false,
        contentType:false,
        data:formData,
    }).done(function(data){
        processAfterUploadBgi(data);
    }).fail(function(xhr,txtstatus,errorthrown){
        displayWTL('アップロードに失敗しました。('+xhr.status+')',50,50,255,0,0);
    });
}
function processAfterUploadBgi(msg){
    if(msg.indexOf('uploadbgiIsK=')!=-1){
        var file_name=msg.slice(13);
        if(file_name.indexOf('uploadimage')!=-1){
            document.getElementById('id_uploadimage_o').value=file_name;
            document.getElementById('id_uploadimage_o').disabled=false;
            document.getElementById('game_backimage_select').value=file_name;
        }else if(file_name.indexOf('uploadmusic')!=-1){
            document.getElementById('id_uploadmusic_o').value=file_name;
            document.getElementById('id_uploadmusic_o').disabled=false;
            document.getElementById('game_music_select').value=file_name;
        }else if(file_name.indexOf('uploadcardset1')!=-1){
            document.getElementById('id_uploadcardset1_o').value=file_name;
            document.getElementById('id_uploadcardset1_o').disabled=false;
            document.getElementById('game_cardset1_select').value=file_name;
        }else if(file_name.indexOf('uploadcardset2')!=-1){
            document.getElementById('id_uploadcardset2_o').value=file_name;
            document.getElementById('id_uploadcardset2_o').disabled=false;
            document.getElementById('game_cardset2_select').value=file_name;
        }else if(file_name.indexOf('uploadcardset3')!=-1){
            document.getElementById('id_uploadcardset3_o').value=file_name;
            document.getElementById('id_uploadcardset3_o').disabled=false;
            document.getElementById('game_cardset3_select').value=file_name;
        }else if(file_name.indexOf('uploadcardset4')!=-1){
            document.getElementById('id_uploadcardset4_o').value=file_name;
            document.getElementById('id_uploadcardset4_o').disabled=false;
            document.getElementById('game_cardset4_select').value=file_name;
        }else if(file_name.indexOf('uploadmapchip')!=-1){
            document.getElementById('id_uploadmapchip_o').value=file_name;
            document.getElementById('id_uploadmapchip_o').disabled=false;
            document.getElementById('game_mapchip_select').value=file_name;
        }
        displayWTL('アップロードが完了しました。',50,50,40,163,11);
    }else if(msg.indexOf('uploadbgiIsN=')!=-1){
        displayWTL(msg.slice(13),50,50,255,0,0);
    }else{
        displayWTL('通信エラー',50,50,255,0,0);
    }
}
function setMapChipImg(){
    document.getElementById('game_mapchip').value=document.getElementById('game_mapchip_select').value;
    setModSettingData('game_mapchip');
}
function setDiceBotText(){
    document.getElementById('game_dicebot').value=document.getElementById('game_dicebot_select').value;
    setModSettingData('game_dicebot');
    if(document.getElementById("ind_dicebot")!=null){
        document.getElementById('ind_dicebot').value=document.getElementById('game_dicebot').value;
    }
}
function setBaCDiceBot(){
    document.getElementById('game_dicebot').value=document.getElementById('bac_dicebot_select').value;
    setModSettingData('game_dicebot');
    if(document.getElementById("ind_dicebot")!=null){
        document.getElementById('ind_dicebot').value=document.getElementById('game_dicebot').value;
    }
}
function putAcDiceBotBox(target){
    if(target==1){ // Bone&Cars
        document.getElementById('id_onsenbot_box').style.display="none";
        document.getElementById('id_boneandcars_box').style.display="block";
        document.getElementById('game_dicebot').value=document.getElementById('bac_dicebot_select').value;
        setModSettingData('game_dicebot');
        if(document.getElementById("ind_dicebot")!=null){
            document.getElementById('ind_dicebot').value=document.getElementById('game_dicebot').value;
        }
    }else{ // オンセンbot
        document.getElementById('id_onsenbot_box').style.display="block";
        document.getElementById('id_boneandcars_box').style.display="none";
        document.getElementById('game_dicebot').value=document.getElementById('game_dicebot_select').value;
        setModSettingData('game_dicebot');
        if(document.getElementById("ind_dicebot")!=null){
            document.getElementById('ind_dicebot').value=document.getElementById('game_dicebot').value;
        }
    }
}
$(function(){
    var inputRangeImageStrength=document.getElementById('game_imagestrength');
    inputRangeImageStrength.addEventListener('change',function(){doNotUpdate('game_imagestrength');setModSettingData('game_imagestrength');},false);
    var inputRangeGrid=document.getElementById('game_grid');
    inputRangeGrid.addEventListener('change',function(){doNotUpdate('game_grid');setModSettingData('game_grid');},false);
});
</script>