<div class="room_setting_box">
    <p><label>見学者用URL（PC版）</label></p>
    <?php 
        $pas_room_url=
            '/roomin.php'.
            '?rn='.urlencode($base_room_file).
            '&pr='.urlencode($principal_id).
            '&nm='.urlencode($nick_name).
            '&lu='.urlencode($lobby_url).
            '&ro=1'; /* room_observer */
    ?>
    <p><input id="id_oburlpc" type="text" value="<?=URL_ROOT.$pas_room_url.'&rt=pc';?>" style="padding:2px 4px;min-width:240px;font-size:10px;" onFocus="$(this).select();" readonly /></p>
</div>
<div class="room_setting_box_wb">
    <p><label>見学者用URL（スマホ版）</label></p>
    <p><input id="id_oburlsp" type="text" value="<?=URL_ROOT.$pas_room_url.'&rt=sp';?>" style="padding:2px 4px;min-width:240px;font-size:10px;" onFocus="$(this).select();" readonly /></p>
</div>
<?php if($observer_flag!=1){ ?> 
<div class="room_setting_box_wb">
    <?php 
        $pas_room_url=
            '/roomin.php'.
            '?rn='.urlencode($base_room_file).
            '&rp='.urlencode($room_pass).
            '&pr='.urlencode($principal_id).
            '&nm='.urlencode($nick_name).
            '&lu='.urlencode($lobby_url).
            '&ro=0'; /* room_observer */
    ?>
    <p><label>参加者用URL（PC版）</label></p>
    <p><input id="id_jmurlpc" type="text" value="<?=URL_ROOT.$pas_room_url.'&rt=pc';?>" style="padding:2px 4px;min-width:240px;font-size:10px;" onFocus="$(this).select();" readonly /></p>
    <p class="font_red small">※&nbsp;このURLはパスワードを知らなくても参加者として入室できるため参加者以外には開示しないことをお勧めします。</p>
</div>
<div class="room_setting_box_wb">
    <p><label>参加者用URL（スマホ版）</label></p>
    <p><input id="id_jmurlsp" type="text" value="<?=URL_ROOT.$pas_room_url.'&rt=sp';?>" style="padding:2px 4px;min-width:240px;font-size:10px;" onFocus="$(this).select();" readonly /></p>
    <p class="font_red small">※&nbsp;このURLはパスワードを知らなくても参加者として入室できるため参加者以外には開示しないことをお勧めします。</p>
</div>
<?php if(!empty($voice_invite_code)&&($voice_invite_code!=-1)){ ?> 
    <div class="room_setting_box_wb">
        <p><label>ボイスチャット用URL（DISCORD）</label></p>
        <p><input id="id_jmurlsp" type="text" value="https://discord.gg/<?=$voice_invite_code;?>" style="padding:2px 4px;min-width:240px;font-size:10px;" onFocus="$(this).select();" readonly /></p>
        <p class="font_red small">※&nbsp;このURLはパスワードを知らなくても参加者として入室できるため参加者以外には開示しないことをお勧めします。</p>
    </div>
<?php 
        }
    } 
?>