<?php
	$event_memo_buttons=
		'<input id="game_memo_add_buttom" type="button" value="追加" style="color:#'.
		($observer_flag!=1?'008;" onClick="pushAddMemoTag()"':'AAA;" disabled').' >&nbsp;'.
		'<input id="game_memo_detele_buttom" type="button" value="削除" style="visibility:hidden;color:#'.
		($observer_flag!=1?'800;" onClick="pushDeleteMemoTag()"':'AAA;" disabled').' >';
?><label>共通メモ</label>
<div id="game_memo_tags" class="lt_t_tab"></div>
<div id="game_memo_box" class="g_memo_box thf">
    <img id="game_memo_img" src="" style="visibility:hidden;" border="0">
    <textarea id="game_memo" class="lt_ta" onblur="blurMemo();" onPaste="pushMemoAnyKey();" onkeydown="pushMemoAnyKey();" <?=$observer_flag!=1?'':'disabled';?> ></textarea>
</div><table class="lt_ta_update">
    <tr>
        <td class="la_ud_td1"><input type="button" value="更新する" onClick="pushGameDataUpdate()" <?=$observer_flag!=1?'':'disabled';?>><?=($room_type=='sp'?'&nbsp;'.$event_memo_buttons:'');?></td>
        <td class="la_ud_td2"><span class="sp_no_display" style="position:absolute;right:0;bottom:0;">表示時間</span></td>
        <td class="la_ud_td3"><span class="sp_no_display">
            <input type="button" value="プレビュー" onClick="pushPopUpUpMyself();" <?=$observer_flag!=1?'':'disabled';?>>
        </span></td>
    </tr>
    <tr class="sp_no_display">
        <td class="la_ud_td1">
			<?=($room_type=='pc'?$event_memo_buttons:'&nbsp;');?>
		</td>
        <td class="la_ud_td2">
            <input id="puw_limit_time" type="number" value="30" min="0" max="3600" step="1" <?=$observer_flag!=1?'':'disabled';?>>
        </td>
        <td class="la_ud_td3">
            <input id="game_memo_a_buttom" type="button" value="ポップアップ" onClick="pushPopUpUpdate();" <?=$observer_flag!=1?'':'disabled';?>>
        </td>
    </tr>
</table>
<label>自己メモ&nbsp;<span class="font_blue">※&nbsp;非共有</span></label>
<div class="lt_t_tab">
    <span id="prim_memo1_tab" class="lt_t_tab_actived" onClick="pushMemoPrimTab(1)">メモ1</span><span id="prim_memo2_tab" onClick="pushMemoPrimTab(2)">メモ2</span><span id="prim_memo3_tab" onClick="pushMemoPrimTab(3)">メモ3</span><span id="prim_memo4_tab" onClick="pushMemoPrimTab(4)">メモ4</span><span id="prim_memo5_tab" onClick="pushMemoPrimTab(5)">メモ5</span>
</div>
<textarea id="prin_memo1" class="lt_tb" name="prin_memo1" maxlength="800" onKeyUp="setCookiePriMemo(1);"><?=$prin_memo[1];?></textarea>
<textarea id="prin_memo2" style="display:none;" class="lt_tb" name="prin_memo2" maxlength="800" onKeyUp="setCookiePriMemo(2);"><?=$prin_memo[2];?></textarea>
<textarea id="prin_memo3" style="display:none;" class="lt_tb" name="prin_memo3" maxlength="800" onKeyUp="setCookiePriMemo(3);"><?=$prin_memo[3];?></textarea>
<textarea id="prin_memo4" style="display:none;" class="lt_tb" name="prin_memo4" maxlength="800" onKeyUp="setCookiePriMemo(4);"><?=$prin_memo[4];?></textarea>
<textarea id="prin_memo5" style="display:none;" class="lt_tb" name="prin_memo5" maxlength="800" onKeyUp="setCookiePriMemo(5);"><?=$prin_memo[5];?></textarea>
<script>
    function pushMemoAnyKey(){
        doNotUpdate('game_memo');
        document.getElementById('game_memo').style.backgroundColor='rgba(255,240,240,0.5)';
    }
    function blurMemo(){
        var color_value=document.getElementById('game_memo').style.backgroundColor;
        color_value=color_value.replace(/\s+/g,'');
        if((color_value.indexOf('FFF0F0')!=-1)||
           (color_value.indexOf('255,240,240')!=-1)){
            document.getElementById('game_memo').style.backgroundColor='rgba(255,255,240,0.5)';
            setModSettingData('game_memo');
        }
    }
    function pushMemoPrimTab(tab_key){
        for(var i=1;i<6;i++){
            if(i==tab_key){
                document.getElementById('prim_memo'+i+'_tab').classList.add("lt_t_tab_actived");
                document.getElementById('prin_memo'+i).style.display="block";
            }else{
                document.getElementById('prim_memo'+i+'_tab').classList.remove("lt_t_tab_actived");
                document.getElementById('prin_memo'+i).style.display="none";
            }
        }
    }
    function pushGameDataUpdate(){
        document.getElementById('game_memo').style.backgroundColor='rgba(255,255,240,0.5)';
        setModSettingData('game_memo');
    }
    function pushAddMemoTag(){
        document.getElementById('game_memo_add_buttom').disabled=true;
        setModSettingData('add_memo');
    }
    function pushDeleteMemoTag(){
		if(confirm('共通メモ'+active_memo_key+'を消去します。'+"\n\n"+'本当によろしいですか？')){
			document.getElementById('game_memo_detele_buttom').disabled=true;
			setModSettingData('delete_memo');
		}else{
			/* 何もしない */
		}
    }
    function pushPopUpUpMyself(){
		var node=document.getElementById('puw_memo_'+active_memo_key);
		if(node){
			document.getElementById('puw_memo_'+active_memo_key+'_head_title').innerHTML='共通メモ'+active_memo_key;
			if(listMemoData[active_memo_key]['eid']!=''){
				clearTimeout(listMemoData[active_memo_key]['eid']);
				listMemoData[active_memo_key]['eid']='';
			}
			if(node.style.display=='none'){
				pushMainMenu('memo_'+active_memo_key);
			}
        }
        return true;
    }
    function pushPopUpUpdate(){
        document.getElementById('game_memo').style.backgroundColor='rgba(255,255,240,0.5)';
        document.getElementById('game_memo_a_buttom').disabled=true;
        setModSettingData('game_memo_p');
        return true;
    }
    function uploadMemoFile(file_object){
        var form_data=new FormData();
        form_data.append('upload_state','game_memo'+active_memo_key);
        form_data.append('room_file','<?=$base_room_file;?>');
        form_data.append('room_pass','<?=$room_pass;?>');
        form_data.append('upload_image',file_object);
        $.ajax({
            url:'<?=URL_ROOT;?>exe/uploadbgi.php',
            type:'post',
            processData:false,
            contentType:false,
            data:form_data,
        }).done(function(data){
            if(processAfterUploadMemo(data)){
                displayWTL('アップロードが完了しました。',50,50,40,163,11);
            }
        }).fail(function(xhr,txtstatus,errorthrown){
            displayWTL('アップロードに失敗しました。('+xhr.status+')',50,50,255,0,0);
        });
    }
    function processAfterUploadMemo(msg){
        if(msg.indexOf('uploadbgiIsK=')!=-1){
            return true;
        }else if(msg.indexOf('uploadbgiIsN=')!=-1){
            displayWTL(msg.slice(13),50,50,255,0,0);
        }else{
            displayWTL('通信エラー',50,50,255,0,0);
        }
        return false;
    }
    (function(){
        document.addEventListener("drop",function(e){
            e.preventDefault();
            var target_id=e.target.id;
            if(target_id=='game_memo'){
                var file_object=e.dataTransfer.files[0];
                uploadMemoFile(file_object);
            }
        });
		document.getElementById('game_memo_img').onerror=function(){
			document.getElementById('game_memo_img').style.visibility='hidden';
		};
    })();
</script>