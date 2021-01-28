<div class="room_setting_box">
    <p>BGMの音量</p>
    <p style="margin-top:5px;">MIN&nbsp;<input id="game_music_volume" type="range" name="music_volume" value="<?=$music_volume;?>" min="0" max="99" step="1" style="width:120px;vertical-align:middle;" />&nbsp;MAX</p>
</div>
<div class="room_setting_box_wb">
    <p>座標の表示</p>
    <p style="margin-top:5px;"><input id="game_bn_display" type="checkbox" style="vertical-align:middle;" value="1" onClick="displayBoardNumber();setCookieSettingData();" <?=($bn_notdisplay!=0?'checked="checked"':'');?> />&nbsp;表示しない</p>
</div>
<div class="room_setting_box_wb">
    <p>チャットの文字サイズ</p>
    <p><select id="game_cs_setting_select" onChange="setCSfontSize();setCookieSettingData();" style="padding:2px 4px;min-width:160px;font-size:10px;">
        <option value="10px" <?=($chat_font_size=='10px'?'selected="selected"':'');?>>小さい</option>
        <option value="12px" <?=($chat_font_size=='12px'?'selected="selected"':'');?>>標準</option>
        <option value="14px" <?=($chat_font_size=='14px'?'selected="selected"':'');?>>大きい</option>
        <option value="16px" <?=($chat_font_size=='16px'?'selected="selected"':'');?>>最大</option>
    </select></p>
    <p style="margin-top:5px;">新着コメントの着信音</p>
    <p><select id="game_chat_se" onChange="setCookieSettingData();" style="padding:2px 4px;min-width:160px;font-size:10px;">
        <option value="false" <?=($chat_sound=='false'?'selected="selected"':'');?>>鳴らさない</option>
        <option value="true" <?=($chat_sound=='true'?'selected="selected"':'');?>>鳴らす</option>
    </select></p>
    <p style="margin-top:5px;">着信音の音量</p>
    <p style="margin-top:5px;">MIN&nbsp;<input id="chat_sound_vol" type="range" name="chat_sound_vol" value="<?=$chat_sound_vol;?>" min="0" max="99" step="1" style="width:120px;vertical-align:middle;" />&nbsp;MAX</p>
</div>
<script>
    function displayBoardNumber(){
        if(document.getElementById('game_bn_display').checked==true){
            document.getElementById('boardnumber').style.visibility='hidden';
        }else{
            document.getElementById('boardnumber').style.visibility='visible';
        }
    }
    function setCSfontSize(){
        document.getElementById('comment_space').style.fontSize=document.getElementById('game_cs_setting_select').value;
    }
    var music_playBtn=document.getElementById('game_music_play');
    var music_stopBtn=document.getElementById('game_music_stop');
    var music_volume=document.getElementById('game_music_volume');
    var chat_sound_volume=document.getElementById('chat_sound_vol');
    music_playBtn.onclick=function(){
        var el_music_url=document.getElementById('game_music');
        el_music_url.value=document.getElementById('game_music_select').value;
        document.getElementById('game_music_state').value='play';
        audio_state='play';
        var audio = document.getElementById('game_audio');
        var volumeValue=(music_volume.value.length==1)?'0.0'+music_volume.value:'0.'+music_volume.value;
        audio.volume=volumeValue;
        if(audio.src!=el_music_url.value){
            if(el_music_url.value==''){
                if(audio.src.indexOf('sounds/se02.mp3')==-1){
                    audio.src='<?=URL_ROOT;?>/sounds/se02.mp3';
                }
            }else{
                audio.src=el_music_url.value;
            }
        }
        if(audio.loop!=null){
            if(audio.loop!=true){
                audio.loop=true;
            }
        }
        if(audio.paused==true){
            audio.play();
        }
        document.getElementById("now_music_state").innerHTML='<span style="color:#00CC00;">▶</span>';
        noneUpdateMusicStateTime=1;
        setModSettingData('game_music_state');
    };
    music_stopBtn.onclick=function(){
        document.getElementById('game_music').value=document.getElementById('game_music_select').value;
        document.getElementById('game_music_state').value='stop';
        audio_state='stop';
        var audio = document.getElementById('game_audio');
        var volumeValue=(music_volume.value.length==1)?'0.0'+music_volume.value:'0.'+music_volume.value;
        audio.src=document.getElementById('game_music').value;
        audio.volume=volumeValue;
        audio.pause();
        document.getElementById("now_music_state").innerHTML='<span style="color:#606060;">■</span>';
        noneUpdateMusicStateTime=1;
        setModSettingData('game_music_state');
    };
    $(function(){
        document.getElementById('game_audio').volume=(music_volume.value.length==1)?'0.0'+<?=$music_volume;?>:'0.'+<?=$music_volume;?>;
        music_volume.addEventListener('change',function(){
            var volumeValue=(music_volume.value.length==1)?'0.0'+music_volume.value:'0.'+music_volume.value;
            document.getElementById('game_audio').volume=volumeValue;
            setCookieSettingData()
        },false);
        document.getElementById('se_audio_1').volume=(chat_sound_volume.value.length==1)?'0.0'+<?=$chat_sound_vol;?>:'0.'+<?=$chat_sound_vol;?>;
        chat_sound_volume.addEventListener('change',function(){
            var volumeValue=(chat_sound_volume.value.length==1)?'0.0'+chat_sound_volume.value:'0.'+chat_sound_volume.value;
            document.getElementById('se_audio_1').volume=volumeValue;
            setCookieSettingData()
        },false);
        displayBoardNumber();
        setCSfontSize();
    });
</script>