<div class="def_table_char_sheet">
    <div class="sheet_select_column">
        <p>キャラクターを選ぶ （キャラ一覧）</p>
        <input id="id_cs_h_id" type="hidden" value="<?=((time()*100)+mt_rand(0,99)).$principal_id;?>" />
        <select id="id_cs_s_name" onchange="callCharacterData();">
            <?php
                echo '<option value="" selected="selected">新しいキャラクターを作る</option>';
                foreach($characterlist_array as $value){
                    echo '<option value="'.$value[0].'">'.$value[2].'</option>';
                }
            ?>
        </select>
    </div>
    <div class="sheet_select_column">
        <p>&nbsp;</p>
        <input id="id_cs_b_reload" type="button" class="r_char_sheet_lbuttom" value="ロビーからキャラ一覧を再取得する" onClick="reloadCharacterList();" />
    </div>
</div>
<div class="def_table_char_sheet">
    <div class="sheet_select_column">
        <div class="sheet_basic_state">
            <div class="chr_sheet_img">
                <div><img id="id_cs_i_img" src="<?=getCharacterImage('');?>" width="150" border="0" /></div>
                <form id="id_cs_img_upload_form" enctype="multipart/form-data"><input id="id_cs_img_upload" type="file" accept="image/jpeg,image/png,image/gif" style="font-size:10px;" /></form>
            </div>
            <p>キャラクター名</p>
            <input id="id_cs_t_name" type="text" value="" />
            <p>TRPGの種別</p>
            <input id="id_cs_t_type" type="text" value="" />
            <select id="id_cs_s_type" onchange="putValueForSelect()">
            <option value="" selected="selected">ここからTRPGを選べます</option><?php
                foreach($global_trpg_name as $key => $trpg_value){
                    echo '<option value="'.$key.'" >'.$trpg_value.'</option>';
                }
            ?></select>&nbsp;
            <input id="id_cs_c_type" type="checkbox" value="1" onchange="changePlaceTextInput()" />
            <p><img src="<?=URL_ROOT;?>images/m_icon86.png" style="vertical-align:middle;" width="12" height="12" border="0" />
               <input id="id_cs_t_hp" type="text" class="cs_life_point_it" value="" />/
               <input id="id_cs_t_mhp" type="text" class="cs_life_point_it" value="" /></p>
               
            <p><img src="<?=URL_ROOT;?>images/m_icon87.png" style="vertical-align:middle;" width="12" height="12" border="0" />
               <input id="id_cs_t_mp" type="text" class="cs_life_point_it" value="" />/
               <input id="id_cs_t_mmp" type="text" class="cs_life_point_it" value="" /></p>
               
            <p>メモ</p>
            <textarea id="id_cs_t_memo"></textarea>
        </div>
        <div class="clearfix">
            <div id="id_edit_page_link" class="m_top_05em m_bottom_05em float_right" style="width:170px;display:none;"><a id="id_edit_page_link_a" style="text-decoration:none;" href="<?=(strpos(LOBBY_URL_SP_ROOT,$global_page_url)===0?LOBBY_URL_SP_ROOT:LOBBY_URL_ROOT);?>make-character.php" target="_blank"><span class="m_b_buttom centering">ロビーの編集ページを開く</span></a></div>
            <div id="id_reload_char_from_lobby" class="m_top_05em m_bottom_05em float_right" style="width:200px;display:none;"><a style="text-decoration:none;" href="javascript:void(0)" onClick="getCharacterDataFromLobbyServer(1);"><span class="m_g_buttom centering">ロビーからこのキャラを再取得する</span></a></div>
        </div>
        <div id="id_r_char_sheet_dda_b" class="r_char_sheet_acb" onClick='putAcCharSheet("id_r_char_sheet_dda")'>詳細A&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
        <div id="id_r_char_sheet_dda" style="display:none;">
            <textarea id="id_cs_t_da" class="equal_interval_font r_char_sheet_bef"></textarea>
        </div>
        <div id="id_r_char_sheet_ddb_b" class="r_char_sheet_acb" onClick='putAcCharSheet("id_r_char_sheet_ddb")'>詳細B&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
        <div id="id_r_char_sheet_ddb" style="display:none;">
            <textarea id="id_cs_t_db" class="equal_interval_font r_char_sheet_bef"></textarea>
        </div>
        <div id="id_r_char_sheet_ddc_b" class="r_char_sheet_acb" onClick='putAcCharSheet("id_r_char_sheet_ddc")'>詳細C&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
        <div id="id_r_char_sheet_ddc" style="display:none;">
            <textarea id="id_cs_t_dc" class="equal_interval_font r_char_sheet_bef"></textarea>
        </div>
        <div id="id_r_char_sheet_macro" class="r_char_sheet_acb" onClick='putAcCharSheet("id_character_macro")'>マクロ&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
        <div id="id_character_macro" style="display:none;">
            <textarea id="id_macro_c1" class="equal_interval_font r_char_sheet_bef"></textarea>
        </div>
        <div id="id_r_char_sheet_stand" class="r_char_sheet_acb" onClick='putAcCharSheet("id_character_stand")'>立ち絵&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
        <div id="id_character_stand" style="display:none;">
            <div id="stand_image_box" class="clearfix">
                <span class="font_gray">こちらの機能はキャラ画像設定後に使うことができます。</span>
            </div>
            <div id="stand_image_cbox" style="display:none;border:1px #AAA solid;margin:2px;padding:4px;">
                <p>状態名：<input type="text" id="stand_img_name" style="width:100px;" /></p>
                <p style="margin-top:3px;">　画像：<input type="file" id="stand_img_file" accept="image/*" /></p>
                <p style="margin-top:3px;">
                    <input type="button" id="add_stand_img" style="width:60px;" onClick="pushAddStandImg();" value="追加" />&nbsp;
                    <input type="button" id="del_stand_img" style="width:60px;" onClick="pushDelStandImg();" value="削除" disabled="disabled" />
                </p>
                <input type="hidden" id="stand_number" value="0" />
            </div>
        </div>
        <div>
            <p>外部URL</p>
            <input id="id_cs_outer_url" type="text" value="" />
        </div>
    </div>
    <div class="sheet_select_column sheet_process">
        <input id="id_cs_b_add" type="button" class="r_char_sheet_add" value="リストに追加" disabled="disabled" onClick="setCharacterList(1);" />
        <input id="id_cs_b_delete" type="button" class="r_char_sheet_add" value="リストから削除" disabled="disabled" onClick="setCharacterList(0);" />
        <input id="id_cs_b_update" type="button" class="r_char_sheet_add" value="保存する" onClick="saveCharacter();" />
    </div>
</div>
<script>
function putValueForSelect(){
	var key=document.getElementById('id_cs_s_type').value;
	$.post('<?=URL_ROOT;?>exe/getchartemplate.php',{
		gt:key,
	},function(data){
		var json_char_data=convertJsonTextToObject(data);
		if(!json_char_data.error){
			if(json_char_data.type){
				document.getElementById('id_cs_t_type').value=json_char_data.type;
			}
			if(json_char_data.da){
				document.getElementById('id_cs_t_da').value=json_char_data.da;
			}
			if(json_char_data.db){
				document.getElementById('id_cs_t_db').value=json_char_data.db;
			}
			if(json_char_data.dc){
				document.getElementById('id_cs_t_dc').value=json_char_data.dc;
			}
		}
	},'html');
}
function changePlaceTextInput(){
	if(document.getElementById('id_cs_c_type').checked==true){
		document.getElementById('id_cs_t_type').style.display='inline-block';
		document.getElementById('id_cs_t_type').readOnly=false;
		document.getElementById('id_cs_s_type').style.display='none';
	}else{
		document.getElementById('id_cs_t_type').style.display='none';
		//document.getElementById('id_cs_t_type').readOnly=true;
		document.getElementById('id_cs_s_type').style.display='inline-block';
	}
}
function putAcCharSheet(targetid){
	var tagid=['id_r_char_sheet_dda_b','id_r_char_sheet_ddb_b','id_r_char_sheet_ddc_b','id_r_char_sheet_macro','id_r_char_sheet_stand'];
	var tagtargetid=['id_r_char_sheet_dda','id_r_char_sheet_ddb','id_r_char_sheet_ddc','id_character_macro','id_character_stand'];
	var tagname=['詳細A','詳細B','詳細C','マクロ','立ち絵'];
	if(document.getElementById(targetid).style.display=="none"){
		document.getElementById(targetid).style.display="block";
		for(i=0;i<5;i++){
			if(tagtargetid[i]==targetid){
				document.getElementById(tagid[i]).innerHTML=tagname[i]+'&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）';
			}
		}
	}else{
		document.getElementById(targetid).style.display="none";
		for(i=0;i<5;i++){
			if(tagtargetid[i]==targetid){
				document.getElementById(tagid[i]).innerHTML=tagname[i]+'&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）';
			}
		}
	}
}
function putDataInNode(id,state,data,default_data){
	var element=document.getElementById(id);
	if(element){
		if(!data){
			data=default_data;
		}
		if(state==='src'){
			if('<?=URL_ROOT.'/r/n/'.$base_room_file.'/';?>'==data){
				data=default_data;
			}
			return element.src=data;
		}else if(state==='value'){
			return element.value=data;
		}
	}
	return false;
}
function convertCaretToEnter(data){
	return data.replace(/\^/g ,"\n");
}
function setNodeSrc(id,tagname,defaultvalue){
	var element = xmlDoc.getElementsByTagName(tagname)[0];
	if(typeof element == "undefined"){
		document.getElementById(id).src = '<?=URL_ROOT;?>'+defaultvalue;
	}else if(element.hasChildNodes()){
		document.getElementById(id).src = '<?=URL_ROOT;?>'+xmlDoc.getElementsByTagName(tagname)[0].firstChild.nodeValue;
	}else{
		document.getElementById(id).src = '<?=URL_ROOT;?>'+defaultvalue;
	}
}
function getCharacterDataFromLobbyServer(forced_load_flag){
	var character_id=document.getElementById('id_cs_s_name').value;
	getCharacterData(character_id,forced_load_flag);
}
function getCharacterData(character_id,forced_load_flag){
	if(character_id==""){
		return false;
	}
	$.post('<?=URL_ROOT;?>exe/getcharacterdata.php',{
		room_id:'<?=$base_room_file;?>',
		principal_id:'<?=$principal_id;?>',
		principal_ip:'<?=$player_ip;?>',
		char_id:character_id,
		forced_load:forced_load_flag,
	},function(data){
		var json_char_data=convertJsonTextToObject(data);
		if(json_char_data.error){
			if(forced_load_flag!=-1){
				displayWTL(json_char_data.error_description,50,50,255,0,0);
			}
		}else{
			if(forced_load_flag!=-1){
				putDataInNode('id_cs_t_name','value',json_char_data.char_name,'');
				var outer_url=json_char_data.outer_url;
				putDataInNode('id_cs_outer_url','value',outer_url.replace('＆','&'),'');
				var game_type=json_char_data.game_type;
				putDataInNode('id_cs_t_type','value',game_type,'');
				putDataInNode('id_cs_t_hp','value',json_char_data.char_hp,'');
				putDataInNode('id_cs_t_mhp','value',json_char_data.char_mhp,'');
				putDataInNode('id_cs_t_mp','value',json_char_data.char_mp,'');
				putDataInNode('id_cs_t_mmp','value',json_char_data.char_mmp,'');
				putDataInNode('id_cs_i_img','src','<?=URL_ROOT.'/r/n/'.$base_room_file.'/';?>'+json_char_data.char_image,'<?=URL_ROOT;?>images/no_image256.jpg');
				putDataInNode('id_cs_t_memo','value',json_char_data.char_memo,'');
				putDataInNode('id_cs_t_da','value',json_char_data.detail_a,'');
				putDataInNode('id_cs_t_db','value',json_char_data.detail_b,'');
				putDataInNode('id_cs_t_dc','value',json_char_data.detail_c,'');
				putDataInNode('id_macro_c1','value',convertCaretToEnter(json_char_data.macro),'');
				if(json_char_data.created_in_room==1){
					document.getElementById('id_reload_char_from_lobby').style.display='none';
					document.getElementById('id_edit_page_link').style.display='none';
				}else{
					document.getElementById('id_reload_char_from_lobby').style.display='block';
					document.getElementById('id_edit_page_link').style.display='block';
				}
				var edit_char_page_url='<?=(strpos(LOBBY_URL_SP_ROOT,$global_page_url)===0?LOBBY_URL_SP_ROOT:LOBBY_URL_ROOT);?>make-character.php?c='+character_id;
				if(json_char_data.designated==1){
					if(game_type.indexOf('クトゥルフ神話')!=-1){
						edit_char_page_url='<?=(strpos(LOBBY_URL_SP_ROOT,$global_page_url)===0?LOBBY_URL_SP_ROOT:LOBBY_URL_ROOT);?>make-coc-character.php?c='+character_id;
					}else if(game_type.indexOf('ソード・ワールド2')!=-1){
						edit_char_page_url='<?=(strpos(LOBBY_URL_SP_ROOT,$global_page_url)===0?LOBBY_URL_SP_ROOT:LOBBY_URL_ROOT);?>make-swordworld2-character.php?c='+character_id;
					}
				}
				document.getElementById('id_edit_page_link_a').href=edit_char_page_url;
				document.getElementById('id_cs_t_da').readOnly=false;
				document.getElementById('id_cs_t_da').style.backgroundColor='#ffffff';
				document.getElementById('id_cs_t_db').readOnly=false;
				document.getElementById('id_cs_t_db').style.backgroundColor='#ffffff';
				document.getElementById('id_cs_t_dc').readOnly=false;
				document.getElementById('id_cs_t_dc').style.backgroundColor='#ffffff';
			}
			writeStandImgBox(json_char_data.stand_image);
			if(forced_load_flag==1){
				obj_character_list=json_char_data.charlist;
				if((typeof changeSelectStandImageCN)=='function'){
					changeSelectStandImageCN();
				}
				if((typeof setMacroList)=='function'){
					setMacroList();
				}
				displayWTL('キャラ「'+json_char_data.char_name+'」の情報を再取得しました。',50,50,40,163,11);
			}
		}
	},'html');
}
function setNewCharaID(){
	var date=new Date();
	return Math.floor(date.getTime()/10)+'<?=$principal_id;?>';
}
function callCharacterData(){
	var character_id=document.getElementById('id_cs_s_name').value;
	if(character_id!=''){
		document.getElementById('id_cs_h_id').value=character_id;
		document.getElementById('id_cs_t_type').style.display='inline';
		document.getElementById('id_cs_t_type').readOnly=true;
		document.getElementById('id_cs_s_type').style.display='none';
		document.getElementById('id_cs_c_type').style.display='none';
		document.getElementById('id_cs_t_name').readOnly=true;
		document.getElementById('id_cs_b_add').disabled=false;
		document.getElementById('id_cs_b_delete').disabled=false;
		document.getElementById('id_cs_b_update').value='更新する';
		document.getElementById('stand_image_cbox').style.display='block';
		getCharacterDataFromLobbyServer(0);
	}else{
		document.getElementById('id_cs_h_id').value=setNewCharaID();
		document.getElementById('id_cs_c_type').style.display='inline';
		document.getElementById('id_cs_c_type').checked=false;
		document.getElementById('id_cs_t_type').style.display='none';
		document.getElementById('id_cs_t_type').value='';
		document.getElementById('id_cs_s_type').style.display='inline';
		document.getElementById('id_cs_t_type').readOnly=false;
		document.getElementById('id_cs_s_type').value='';
		document.getElementById('id_cs_t_name').readOnly=false;
		document.getElementById('id_cs_b_add').disabled=true;
		document.getElementById('id_cs_b_delete').disabled=true;
		document.getElementById('id_cs_b_update').value='保存する';
		document.getElementById('id_cs_t_name').value='';
		document.getElementById('id_cs_i_img').src='<?=URL_ROOT;?>images/no_image256.jpg';
		document.getElementById('id_cs_t_hp').value='';
		document.getElementById('id_cs_t_mhp').value='';
		document.getElementById('id_cs_t_mp').value='';
		document.getElementById('id_cs_t_mmp').value='';
		document.getElementById('id_cs_outer_url').value='';
		document.getElementById('id_cs_t_memo').value='';
		document.getElementById('id_cs_t_da').value='';
		document.getElementById('id_cs_t_db').value='';
		document.getElementById('id_cs_t_dc').value='';
		document.getElementById('id_macro_c1').value='';
		document.getElementById('id_reload_char_from_lobby').style.display='none';
		document.getElementById('id_edit_page_link').style.display='none';
		document.getElementById('stand_image_box').innerHTML='<span class="font_gray">こちらの機能はキャラ画像設定後に使うことができます。</span>';
		document.getElementById('stand_image_cbox').style.display='none';
	}
}
function setCharacterList(flg){
	document.getElementById('id_cs_b_add').disabled=true;
	document.getElementById('id_cs_b_delete').disabled=true;
	document.getElementById('id_cs_b_update').disabled=true;
	$.post('<?=URL_ROOT;?>exe/setcharacterlist.php',{
		room_id:'<?=$base_room_file;?>',
		principal_id:'<?=$principal_id;?>',
		nick_name:'<?=$nick_name;?>',
		flag:flg,
		chat_color:document.getElementById('id_chat_color').value,
		char_id:document.getElementById('id_cs_h_id').value,
		char_name:document.getElementById('id_cs_t_name').value,
		char_hp:document.getElementById('id_cs_t_hp').value,
		char_mhp:document.getElementById('id_cs_t_mhp').value,
		char_mp:document.getElementById('id_cs_t_mp').value,
		char_mmp:document.getElementById('id_cs_t_mmp').value,
		outer_url:document.getElementById('id_cs_outer_url').value,
		char_memo:document.getElementById('id_cs_t_memo').value,
		char_image:document.getElementById('id_cs_i_img').src,
	},function(data){
		openWTL(data);
		document.getElementById('id_cs_b_add').disabled=false;
		document.getElementById('id_cs_b_delete').disabled=false;
		document.getElementById('id_cs_b_update').disabled=false;
	},'html');
}
function saveCharacter(){
	var err_comment='';
	var str_macros=document.getElementById('id_macro_c1').value.replace(/\s+$/g,'');
	if(str_macros!=''){
		var macro_recodes=str_macros.split("\n");
		for(var i=0;i<macro_recodes.length;i++){
			if(macro_recodes[i].indexOf('|')!=-1){
				var macro_column=macro_recodes[i].split('|');
				if(macro_column[0].match(/[#＃$＄@＠|\^]/)){ // 禁止文字エラー
					err_comment+='× マクロ名に使用できない文字が含まれています。<br>#$@|^';
					break;
				}
				if(macro_column[1].match(/[$＄|\^]/)){ // 禁止文字エラー
					err_comment+='× マクロの実行コマンドに使用できない文字が含まれています。<br>$|^';
					break;
				}
			}
		}
	}
	var game_type=document.getElementById('id_cs_t_type').value;
	if(!game_type){
		err_comment+='× TRPGの種類が選択されていません。';
	}
	if(err_comment==''){
		document.getElementById('id_cs_b_add').disabled=true;
		document.getElementById('id_cs_b_delete').disabled=true;
		document.getElementById('id_cs_b_update').disabled=true;
		var form_data=new FormData();
		form_data.append('room_id','<?=$base_room_file;?>');
		form_data.append('principal_id','<?=$principal_id;?>');
		form_data.append('chat_color',document.getElementById('id_chat_color').value);
		form_data.append('char_id',document.getElementById('id_cs_h_id').value);
		form_data.append('game_type',document.getElementById('id_cs_t_type').value);
		form_data.append('char_name',document.getElementById('id_cs_t_name').value);
		form_data.append('char_hp',document.getElementById('id_cs_t_hp').value);
		form_data.append('char_mhp',document.getElementById('id_cs_t_mhp').value);
		form_data.append('char_mp',document.getElementById('id_cs_t_mp').value);
		form_data.append('char_mmp',document.getElementById('id_cs_t_mmp').value);
		form_data.append('char_memo',document.getElementById('id_cs_t_memo').value);
		form_data.append('detail_a',document.getElementById('id_cs_t_da').value);
		form_data.append('detail_b',document.getElementById('id_cs_t_db').value);
		form_data.append('detail_c',document.getElementById('id_cs_t_dc').value);
		form_data.append('macro_c1',document.getElementById('id_macro_c1').value);
		form_data.append('outer_url',document.getElementById('id_cs_outer_url').value);
		form_data.append('char_image',document.getElementById('id_cs_img_upload').files[0]);
		if(document.getElementById('id_edit_page_link').style.display=='none'){
			form_data.append('created_in_room','1');
		}
		$.ajax({
			url:'<?=URL_ROOT;?>exe/savecharacterdata.php',
			type:'post',
			processData:false,
			contentType:false,
			data:form_data,
		}).done(function(data){
			var json_char_data=convertJsonTextToObject(data);
			if(json_char_data.error){
				displayWTL(json_char_data.error_description,50,50,255,0,0);
			}else{
				obj_character_list=json_char_data.charlist;
				writeStandImgBox(json_char_data.stand_image);
				if((typeof changeSelectStandImageCN)=='function'){
					changeSelectStandImageCN();
				}
				if((typeof setMacroList)=='function'){
					setMacroList();
				}
				var new_char_id=document.getElementById('id_cs_h_id').value;
				var new_char_name=document.getElementById('id_cs_t_name').value;
				var nodeCharSelect=document.getElementById('id_cs_s_name');
				if(nodeCharSelect){
					var node_exist_flag=false;
					for(var i=0;i<nodeCharSelect.childNodes.length;i++){
						if(nodeCharSelect.childNodes[i].value==new_char_id){
							node_exist_flag=true;
						}
					}
					if(node_exist_flag==false){
						var nodeNewCsOption=document.createElement('option');
						var nodeNewCsText=document.createTextNode(new_char_name);
						nodeNewCsOption.appendChild(nodeNewCsText);
						nodeNewCsOption.setAttribute('value',new_char_id);
						nodeCharSelect.appendChild(nodeNewCsOption);
						nodeCharSelect.value=new_char_id;
						document.getElementById('id_cs_t_type').style.display='inline';
						document.getElementById('id_cs_t_type').readOnly=true;
						document.getElementById('id_cs_s_type').style.display='none';
						document.getElementById('id_cs_c_type').style.display='none';
						document.getElementById('id_cs_t_name').readOnly=true;
						document.getElementById('id_cs_b_update').value='更新する';
					}
				}
				var nodeChatSelect=document.getElementById('id_call_name_st');
				if(nodeChatSelect){
					var node_exist_flag=false;
					for(var i=0;i<nodeChatSelect.childNodes.length;i++){
						if(nodeChatSelect.childNodes[i].value==new_char_id){
							node_exist_flag=true;
						}
					}
					if(node_exist_flag==false){
						var nodeNewCnOption=document.createElement('option');
						var nodeNewCnText=document.createTextNode(new_char_name);
						nodeNewCnOption.appendChild(nodeNewCnText);
						nodeNewCnOption.setAttribute('value',new_char_id);
						nodeChatSelect.appendChild(nodeNewCnOption);
					}
				}
				for(var item_no in json_char_data.charlist.charlist){
					if(json_char_data.charlist.charlist[item_no].id==document.getElementById('id_cs_h_id').value){
						putDataInNode('id_cs_i_img','src','<?=URL_ROOT.'/r/n/'.$base_room_file.'/';?>'+json_char_data.charlist.charlist[item_no].image,'<?=URL_ROOT;?>images/no_image256.jpg');
						break;
					}
				}
				document.getElementById('stand_image_cbox').style.display='block';
			}
			document.getElementById('id_cs_b_add').disabled=false;
			document.getElementById('id_cs_b_delete').disabled=false;
			document.getElementById('id_cs_b_update').disabled=false;
		}).fail(function(data){
			displayWTL('キャラクターの更新に失敗しました。',50,50,255,0,0);
		}).always(function(data){
			document.getElementById('id_cs_img_upload').value='';
		});
	}else{
		displayWTL('入力が不足しています。<br>'+err_comment,50,50,255,0,0);
	}
}
function reloadCharacterList(){
	document.getElementById('id_cs_b_reload').disabled=true;
	$.post(CONST_URL_ROOT+'exe/reloadcharacterlist.php',{
		room_id:CONST_ROOM_ID,
		principal_id:CONST_PRINCIPAL_ID,
		principal_ip:'<?=$player_ip;?>'
	},function(data){
		var json_charlist_data=convertJsonTextToObject(data);
		if(json_charlist_data.error){
			displayWTL(json_charlist_data.error_description,50,50,255,0,0);
		}else{
			obj_character_list=json_charlist_data;
			if((typeof changeSelectStandImageCN)=='function'){
				changeSelectStandImageCN();
			}
			if((typeof setMacroList)=='function'){
				setMacroList();
			}
			var html_v='';
			if(obj_character_list.charlist){
				for(var key in obj_character_list.charlist){
					html_v+='<option value="'+obj_character_list.charlist[key].id+'">'+obj_character_list.charlist[key].name+'</option>';
				}
			}
			document.getElementById('id_cs_s_name').value='';
			document.getElementById('id_cs_s_name').innerHTML='<option value="">新しいキャラクターを作る</option>'+html_v;
			if(CONST_NICKNAME!=CONST_PRINCIPAL_ID && CONST_NICKNAME!=''){
				html_v='<option value="gm_nick_name">GM／'+CONST_NICKNAME+'</option>'+html_v;
				html_v='<option value="nick_name">'+CONST_NICKNAME+'</option>'+html_v;
			}else{
				html_v='<option value="gm_principal_id">GM／'+CONST_PRINCIPAL_ID+'</option>'+html_v;
				html_v='<option value="principal_id">'+CONST_PRINCIPAL_ID+'</option>'+html_v;
			}
			document.getElementById('id_call_name_st').value='';
			document.getElementById('id_call_name_st').innerHTML=html_v;
			callCharacterData();
			displayWTL('ロビーからキャラ一覧を再取得しました。',50,50,40,163,11);
		}
		document.getElementById('id_cs_b_reload').disabled=false;
	},'html');
}
function uploadCharImage(req_flag,img_name,img_file){
	var character_id=document.getElementById('id_cs_s_name').value;
	if(character_id!=''){
		var form_data=new FormData();
		form_data.append('room_id','<?=$base_room_file;?>');
		form_data.append('principal_id','<?=$principal_id;?>');
		form_data.append('principal_ip','<?=$player_ip;?>');
		form_data.append('char_id',character_id);
		form_data.append('request',req_flag);
		form_data.append('img_name',img_name);
		form_data.append('upload_image',img_file);
		$.ajax({
			url:'<?=URL_ROOT;?>exe/uploadcharimage.php',
			type:'post',
			processData:false,
			contentType:false,
			data:form_data,
		}).done(function(data){
			var json_char_data=convertJsonTextToObject(data);
			if(json_char_data.error){
				displayWTL(json_char_data.error_description,50,50,255,0,0);
			}else{
				if(req_flag=='face'){
					document.getElementById('id_cs_i_img').src=json_char_data.set_image;
				}else if(req_flag.indexOf('stand')!=-1){
					writeStandImgBox(json_char_data.stand_image);
				}
				if(json_char_data.charlist){
					obj_character_list=json_char_data.charlist;
					if((typeof changeSelectStandImageCN)=='function'){
						changeSelectStandImageCN(null);
					}
				}
			}
		}).fail(function(data){
			displayWTL('キャラクターの更新に失敗しました。',50,50,255,0,0);
		}).always(function(data){
			document.getElementById('stand_img_file').value='';
		});
	}else{
		displayWTL('キャラクターが指定されていません。',50,50,255,0,0);
	}
}
function pushSelectStandImg(img_no){
	if(img_no==0){
		document.getElementById('add_stand_img').value='追加';
		document.getElementById('add_stand_img').disabled=false;
		document.getElementById('del_stand_img').disabled=true;
		document.getElementById('stand_img_name').value='';
	}else{
		document.getElementById('add_stand_img').value='変更';
		document.getElementById('add_stand_img').disabled=false;
		document.getElementById('del_stand_img').disabled=false;
		document.getElementById('stand_img_name').value=document.getElementById('stand_name_box'+img_no).innerHTML;
	}
	var nobox_list=document.getElementsByClassName('stand_img_nobox');
	for(var i=0;i<nobox_list.length;i++){
		if(i==img_no){
			nobox_list[i].style.backgroundColor='#F8DC85';
		}else{
			nobox_list[i].style.backgroundColor='#FFF';
		}
	}
	document.getElementById('stand_number').value=img_no;
}
function writeStandImgBox(json_char_stand_data){
	var html_v='';
	for(var i=0;i<json_char_stand_data.length;i++){
		var stand_name='表情'+(i+1);
		if(json_char_stand_data[i][0]!=null){
			if(json_char_stand_data[i][0]!=''){
				stand_name=json_char_stand_data[i][0];
			}
		}
		var stand_image='<?=URL_ROOT;?>images/no_image256.jpg';
		if(json_char_stand_data[i][1]!=null){
			if(json_char_stand_data[i][1]!=''){
				stand_image='<?=URL_ROOT.'/r/n/'.$base_room_file.'/';?>'+json_char_stand_data[i][1];
			}
		}
		html_v+='<div class="stand_img_nobox" style="width:84px;border:1px #AAA solid;float:left;margin:2px;overflow:hidden;background-color:#FFF;" onClick="pushSelectStandImg('+(i+1)+');">';
		html_v+='<div style="width:80px;height:80px;margin:2px;overflow:hidden;">';
		html_v+='<img src="'+stand_image+'" width="80" />';
		html_v+='</div>';
		html_v+='<div id="stand_name_box'+(i+1)+'" style="width:80px;padding:2px;text-align:center;overflow:hidden;">'+stand_name+'</div>';
		html_v+='</div>';
	}
	var add_html_v='<div class="stand_img_nobox" style="width:84px;border:1px #AAA solid;float:left;margin:2px;overflow:hidden;';
	if(i<10){
		add_html_v+='background-color:#F8DC85;';
	}else{
		add_html_v+='background-color:#FFF;';
	}
	if(i<10){
		add_html_v+='" onClick="pushSelectStandImg(0);';
	}
	add_html_v+='">';
	add_html_v+='<div style="width:80px;height:80px;margin:2px;"><span style="display:table-cell;width:80px;height:80px;color:#AAA;font-size:50px;text-align:center;vertical-align:">＋</span></div>';
	add_html_v+='<div style="width:80px;padding:2px;text-align:center;overflow:hidden;';
	if(i>=10){
		add_html_v+='color:#DDD;';
	}
	add_html_v+='">追加</div>';
	add_html_v+='</div>';
	html_v=add_html_v+html_v;
	document.getElementById('stand_image_box').innerHTML=html_v;
	if(i<10){
		pushSelectStandImg(0);
	}else{
		pushSelectStandImg(1);
	}
}
function pushAddStandImg(){
	if(document.getElementById('stand_img_name').value!=''){
		uploadCharImage(
			'stand'+document.getElementById('stand_number').value,
			document.getElementById('stand_img_name').value,
			document.getElementById('stand_img_file').files[0]
		);
	}else{
		displayWTL('状態名がありません。',50,50,255,0,0);
	}
}
function pushDelStandImg(){
	uploadCharImage(
		'dstand'+document.getElementById('stand_number').value,
		'削除',
		''
	);
}
</script>