<div id="comment_write">
    <div style="height:56px;">
        <table style="width:100%;">
            <tr>
                <td style="vertical-align:top;">
                    <input id="id_call_name" type="text" maxlength="50" value="" style="display:none;width:140px;" onkeyup="textCallNameEvent();" <?=$observer_flag!=1?'':'disabled="disabled"';?>
                    /><select id="id_call_name_st" style="width:140px;" onChange="putCallName();" <?=$observer_flag!=1?'':'disabled="disabled"';?>>
                        <?=setOptionCallName($principal_id,$nick_name,$characterlist_array,0);?></select>
                    <input id="id_call_name_cb" type="checkbox" onClick="switchInputCallName();" style="vertical-align:middle;" <?=$observer_flag!=1?'':'disabled="disabled"';?> />
                    <select id="id_stand_st" style="width:86px;" <?=$observer_flag!=1?'':'disabled="disabled"';?> >
                        <option value="-1" selected="seleted">立ち絵なし</option>
                    </select>
                    <select id="id_stand_p_st" style="width:38px;" <?=$observer_flag!=1?'':'disabled="disabled"';?> >
                        <option value="0" selected="seleted">左</option>
                        <option value="1">中</option>
                        <option value="2">右</option>
                    </select>
                    <input id="del_stand" type="button" value="消" onClick="deleteStandImage();" style="width:22px;z-index:501;" />
                    <select id="ind_dicebot" style="width:86px;" <?=$observer_flag!=1?'':'disabled="disabled"';?> >
                    <?php
                        $v_html='';
                        foreach($dicebot_textlist as $key => $value){
                            $v_html.='<option value="'.$key.'"';
                            if($key==(string)$xml->head->game_dicebot){
                                $v_html.=' selected="selected"';
                            }
                            $v_html.='>'.$value[0].'：ｵﾝｾﾝdb</option>';
                        }
                        if(isset($bac_gamelist)){
                            foreach($bac_gamelist as $key => $value){
                                $v_html.='<option value="'.$key.'"';
                                if($key==(string)$xml->head->game_dicebot){
                                    $v_html.=' selected="selected"';
                                }
                                $v_html.='>'.$value[0].'：bac</option>';
                            }
                        }
                        echo $v_html;
                    ?>
                    </select>
                    <span id="dicebot_text_box">
                        <img style="vertical-align:middle;" src="<?=URL_ROOT;?>images/m_icon18.png" onClick="setDiceBotCommentary()" width="18" height="24" border="0" title="コマンド説明" />
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
                </td>
                <td style="width:42px;text-align:center;vertical-align:top;">
                    <input type="button" value="ログ" onClick="confirmOutputLog();" style="width:40px;z-index:501;" <?=$login_flag!=true?'disabled="disabled"':'';?> />
                </td>
                <td style="width:62px;text-align:center;vertical-align:top;">
                    <input type="button" value="マクロ" onClick="pushMainMenu('22');" style="width:60px;" <?=$observer_flag!=1?'':'disabled="disabled"';?> />
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top;">
                    <input type="text" id="id_comment" maxlength="200" value="" style="width:98%;" <?php
                        if(($login_flag!=true)||
                           (($observer_flag!=0)&&($observer_write!=0))){
                            echo 'disabled="disabled"';
                        }else{
                            echo 'autofocus';
                        }
                    ?> />
                </td>
                <td style="width:42px;text-align:center;vertical-align:top;">
                    <select id="id_chat_color" style="width:40px;" onChange="setCookieSettingData();" <?php
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
                <td style="width:62px;text-align:center;vertical-align:top;">
                    <input id="send_cmt_b" type="button" value="送信" onClick="postPutComment();" style="width:60px;" <?php
                        if(($login_flag!=true)||
                           (($observer_flag!=0)&&($observer_write!=0))){
                            echo 'disabled="disabled"';
                        }else{
                            echo 'autofocus';
                        }
                    ?> />
                </td>
            </tr>
        </table>
    </div>
    <div class="lt_t_tab">
        <span id="chat_type0_tab" onClick="pushChatTypeTab(0)" class="lt_t_tab_actived">全て<div style="display:inline;" id="chat_type0_cnt"></div></span>
        <span id="chat_type1_tab" onClick="pushChatTypeTab(1)">メイン<div style="display:inline;" id="chat_type1_cnt"></div></span>
        <span id="chat_type2_tab" onClick="pushChatTypeTab(2)">雑談<div style="display:inline;" id="chat_type2_cnt"></div></span>
        <span id="chat_type3_tab" <?=$observer_write!=0?'style="display:none;"':'';?> onClick="pushChatTypeTab(3)">見学用<div style="display:inline;" id="chat_type3_cnt"></div></span>
    </div>
    <div style="border:1px solid #AAA;padding-left:2px;margin:-2px 2px 0 2px;"><div id="comment_space" style="overflow:auto;"></div></div>
</div>
<input id="id_call_name_id" type="hidden" value="">
<script>
var leftCharDetail=0;
var chattextcookie_load_no=0;
	
function pushChatTypeTab(tab_key){
	for(var i=0;i<4;i++){
		if(i==tab_key){
			document.getElementById('chat_type'+i+'_tab').classList.add("lt_t_tab_actived");
		}else{
			document.getElementById('chat_type'+i+'_tab').classList.remove("lt_t_tab_actived");
		}
	}
	if((tab_key>=0)&&(tab_key<=3)){
		last_comment='**nothing**';
		chat_type_flag=tab_key;
		displayChatText(chat_type_flag);
	}
}
function deleteStandImage(){
	document.getElementById('stand_img0').style.display='none';
	document.getElementById('stand_img1').style.display='none';
	document.getElementById('stand_img2').style.display='none';
}
function confirmOutputLog(){
	if(confirm('このルームのログをダウンロードしますか？')){
		window.open('<?=URL_ROOT;?>exe/download-log.php?i=<?=$base_room_file;?>&p=<?=$principal_id;?>','_blank');
	}
}
function textCallNameEvent(){
	var call_name_id=changeSelectStandImageCN();
	if(call_name_id!=''){
		getCharacterData(call_name_id,-1);
	}
}
function changeSelectStandImageCN(call_name){
	if(call_name==null){
		call_name=document.getElementById('id_call_name').value;
	}
	var call_name_id='';
	var html_v='<option value="-1">立ち絵なし</option>';
	if(obj_character_list.charlist){
		for(var key in obj_character_list.charlist){
			if(obj_character_list.charlist[key].name==call_name){
				call_name_id=obj_character_list.charlist[key].id;
				for(var sm_key in obj_character_list.charlist[key].stand_image){
					html_v+='<option value="'+sm_key+'">'+obj_character_list.charlist[key].stand_image[sm_key].name+'</option>';
				}
				break;
			}
		}
	}
	document.getElementById('id_call_name_id').value=call_name_id;
	document.getElementById('id_stand_st').innerHTML=html_v;
	document.getElementById('id_stand_st').value='-1';
	setMacroList(call_name_id);
	return call_name_id;
}
function changeSelectStandImageCNID(call_name_id){
	var html_v='<option value="-1">立ち絵なし</option>';
	if(obj_character_list.charlist){
		for(var key in obj_character_list.charlist){
			if(obj_character_list.charlist[key].id==call_name_id){
				document.getElementById('id_call_name_id').value=obj_character_list.charlist[key].id;
				for(var sm_key in obj_character_list.charlist[key].stand_image){
					html_v+='<option value="'+sm_key+'">'+obj_character_list.charlist[key].stand_image[sm_key].name+'</option>';
				}
				break;
			}
		}
	}
	document.getElementById('id_stand_st').innerHTML=html_v;
	document.getElementById('id_stand_st').value='-1';
}
function switchInputCallName(){
	if(document.getElementById('id_call_name_cb').checked==true){
		document.getElementById('id_call_name').style.display='inline-block';
		document.getElementById('id_call_name_st').style.display='none';
	}else{
		document.getElementById('id_call_name').style.display='none';
		document.getElementById('id_call_name_st').style.display='inline-block';
		putCallName();
	}
}
function postPutComment(){
	var temp_comment=document.getElementById('id_comment').value;
	var temp_call_name=document.getElementById('id_call_name').value;
	if(temp_call_name==""){
		temp_call_name='<?=$nick_name!=""?$nick_name:$principal_id;?>';
	}
	var temp_chat_type=chat_type_flag;
	if(<?=$observer_flag!=1?0:1;?>==1){
		temp_chat_type=3;
	}else if(temp_chat_type==0){
		temp_chat_type=1
	}
	if(temp_comment!=""){
		setChatTextCookie();
		document.getElementById('id_comment').value='';
		if(temp_comment=='/bs'){
			saveLocalBD('<?=URL_ROOT;?>r/n/<?=$base_room_file;?>/data.xml','id_comment,id_call_name');
		}else if(temp_comment=='/bl'){
			loadLocalBD('<?=URL_ROOT;?>exe/putsettingdata.php','<?=$base_room_file;?>','<?=$principal_id;?>','id_comment,id_call_name');
		}else{
			document.getElementById('id_comment').disabled=true;
			document.getElementById('id_call_name').disabled=true;
			document.getElementById('send_cmt_b').disabled=true;
			document.getElementById('id_stand_st').disabled=true;
			document.getElementById('id_stand_p_st').disabled=true;
			setTimeout(function(){
					document.getElementById('id_comment').disabled=false;
					document.getElementById('id_call_name').disabled=false;
					document.getElementById('send_cmt_b').disabled=false;
				},10000);
			$.post('<?=URL_ROOT;?>exe/putcomment.php',{
				comment:temp_comment,
				chat_color:document.getElementById('id_chat_color').value,
				call_name:temp_call_name,
				chat_type:temp_chat_type,
				observer_flag:<?=$observer_flag;?>,
				use_dicebot:document.getElementById('ind_dicebot').value,
				stand_img:document.getElementById('id_stand_st').value,
				stand_pos:document.getElementById('id_stand_p_st').value,
				principal:'<?=$principal_id;?>',
				nick_name:'<?=$nick_name;?>',
				xml:'<?=$base_room_file;?>',
			},function(data){
				openWTL(data);
				document.getElementById('id_comment').disabled=false;
				document.getElementById('id_call_name').disabled=false;
				document.getElementById('send_cmt_b').disabled=false;
				<?=$observer_flag!=1?'document.getElementById(\'id_stand_st\').disabled=false;':'';?>
				<?=$observer_flag!=1?'document.getElementById(\'id_stand_p_st\').disabled=false;':'';?>
				document.getElementById('id_comment').focus();
			},'html');
		}
	}
}
function setChatTextCookie(){
	var cookieString=document.cookie;
	if(cookieString!=""){
		let reg=/chat_past_text\d=[^;]+;?/g;
		let matched=cookieString.match(reg);
		if(matched!==null){
			matched.forEach(function(str){
				var no=parseInt(str.substring(14,15));
				if(no<9){
					no++;
					document.cookie='chat_past_text'+no+'='+str.substring(16).replace(';','');
				}
			});
		}
	}
	document.cookie="chat_past_text0="+encodeURI(document.getElementById('id_comment').value);
}
function loadChatTextCookie(){
	var result=false;
	var cookieString=document.cookie;
	if(cookieString!=""){
		var no=chattextcookie_load_no;
		if(no<0){
			no=0;
		}
		let reg=new RegExp('chat_past_text'+no+'=[^;]+;?','g');
		let matched=cookieString.match(reg);
		if(matched!==null){
			matched.forEach(function(str){
				document.getElementById('id_comment').value=decodeURI(str.substring(16).replace(";",""));
				result=true;
			});
		}
	}
	return result;
}
function setDiceBotCommentary(){
	var form_data=new FormData();
	form_data.append('game_key',document.getElementById("ind_dicebot").value);
	$.post('<?=URL_ROOT;?>exe/pushdicebottext.php',{
		game_key:document.getElementById("ind_dicebot").value
	},function(msg){
		document.getElementById("dicebot_text").innerHTML=msg;
		openDicebotDetailWindow('dicebot_text','情報（チャットコマンドの説明）');
	},'html');
}
function openDicebotDetailWindow(transfer_id,title){
	var transferElement=document.getElementById(transfer_id);
	var explanationElement=document.getElementById('explanation_space');
	if(explanationElement){
		explanationElement.innerHTML=transferElement.innerHTML;
		document.getElementById('puw_13_head_title').innerHTML=title;
		var node=document.getElementById('puw_13');
		node.style.zIndex=countPopInWindow+1;
		if(node.style.display=='none'){
			pushMainMenu('13');
		}
		return true;
	}else{
		return false;
	}
}
$(function(){
	putCallName();
	document.onkeydown=function(e){
		var alt,ctrl;
		if(e!=null){ // Mozilla
			keycode=e.which;
			alt=typeof e.modifiers=='undefined'?e.altKey:e.modifiers & Event.ALT_MASK;
		}else{ // IE
			keycode=event.keyCode;
			alt=event.altKey;
		}
		if((alt)||(ctrl)){ // Alt 同時押しの場合
			if(document.getElementById("id_comment").disabled==false){
				keychar=String.fromCharCode(keycode).toUpperCase(); // キーコードの文字を取得
				if(keychar=="1"){
					var comment_value=document.getElementById("id_comment").value.replace(/^\/[kmzKMZ] /,'');
					if(<?=$observer_flag;?>!=1){
						document.getElementById("id_comment").value=comment_value;
					}else{
						document.getElementById("id_comment").value='/m '+comment_value;
					}
					document.getElementById("id_comment").focus();
				}else if(keychar=="2"){
					var comment_value=document.getElementById("id_comment").value.replace(/^\/[kmzKMZ] /,'');
					document.getElementById("id_comment").value='/z '+comment_value;
					document.getElementById("id_comment").focus();
				}else if(keychar=="3"){
					var comment_value=document.getElementById("id_comment").value.replace(/^\/[kmzKMZ] /,'');
					if(<?=$observer_flag;?>!=1){
						document.getElementById("id_comment").value='/k '+comment_value;
					}else{
						document.getElementById("id_comment").value=comment_value;
					}
					document.getElementById("id_comment").focus();
				}
			}
		}
	}
	document.getElementById("id_comment").addEventListener('keydown',(event)=>{
		if(event!=null){ // Mozilla
			var keycode=event.which;
		}else{ // IE
			var keycode=event.keyCode;
		}
		if(keycode==13){
			postPutComment();
			chattextcookie_load_no=0;
		}else if(keycode==38){
			if(chattextcookie_load_no<0){
				chattextcookie_load_no=1;
			}
			if(loadChatTextCookie()){
				chattextcookie_load_no++;
				if(chattextcookie_load_no>9){
					chattextcookie_load_no=9;
				}
			}
		}else if(keycode==40){
			chattextcookie_load_no--;
			loadChatTextCookie();
		}
	});
	document.getElementById("id_comment").addEventListener('blur',(event)=>{
		chattextcookie_load_no=0;
	});
	var leftCharDetail=window.innerWidth;
	if(leftCharDetail>0){
		leftCharDetail=(leftCharDetail-540)/2-55;
	}
	document.getElementById('dicebot_text').style.left=leftCharDetail+'px';
});
</script>