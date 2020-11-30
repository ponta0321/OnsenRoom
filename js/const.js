/*////////////////////////////////////////////////////////////////
global variable
////////////////////////////////////////////////////////////////*/
document.charset='UTF-8';
var activeInputCharData=0;
var noneUpdateBoardTime=0; // ボード情報を更新しない時間
var noneUpdateMusicStateTime=0; // オーディオを更新しない時間
var noneUpdateSettingId=''; // 指定されたIDの情報を更新しない
var audio_state=''; // オーディオの状態 play=再生 stop=停止 pause=一時停止
var flagOpenRoomStting=0;
var xml_not_found_flag=0; // ルームデータが存在するかを読み込みできない時間でチェック PC版は60秒 スマホ版は600秒
var last_comment='';
var last_comment_date=0;
var listNickName={};
var listCardStock={
    0:{},
    1:{},
    2:{},
    3:{},
    4:{}
};
var listDiscardStock={
    0:{},
    1:{},
    2:{},
    3:{},
    4:{}
};
var loadedimgsheet_flag=[
    [0,CONST_URL_ROOT+'images/cardback.jpg',1],
    [0,'about:blank',0],
    [0,'about:blank',0],
    [0,'about:blank',0],
    [0,'about:blank',0]
];
var chessman_array=[];
var font_color_list={
    '#000000':'font_black',
    '#606060':'font_gray',
    '#CC0000':'font_deepred',
    '#0000CC':'font_deepblue',
    '#00CC00':'font_thickgreen',
    '#E0B000':'font_yellow',
    '#FF8119':'font_citrus',
    '#C400CC':'font_purple',
    '#272672':'font_indigo',
    '#E54500':'font_gag',
    '#6D9A4A':'font_ingale',
    '#7C6035':'font_brown',
    '#4E2F4F':'font_bubbly',
    '#EF7585':'font_pink'
};
var active_memo_key=1;
var listMemoData={};
var listStandData=[
    [0,'',0,0,''],
    [0,'',0,0,''],
    [0,'',0,0,'']
];
var mouse={id:null,flg:null,ox:null,oy:null,gx:null,gy:null,rx:null,ry:null,go:null,at:null};
var chat_type_flag=0;
var text_chat_array=[];
var tag_last_comment_date=[0,0,0,0];
var drag_image=new Image(); // ドラッグ時表示画像
var mapping_image=new Image(); // マップチップ画像
var loadedimgmapping_flag=0; // マップチップ画像が読込フラグ 0=未読込 1=読込済
var map_linechip_w=0; // マップチップ（ライン）width
var map_linechip_h=0; // マップチップ（ライン）height
var map_decchip_w=0; // マップチップ（デコレーション）width
var map_decchip_h=0; // マップチップ（デコレーション）height
/*////////////////////////////////////////////////////////////////
function
////////////////////////////////////////////////////////////////*/
// XML読み込み
function createXMLHttpRequest(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }else{
        if(window.ActiveXObject){
            return new ActiveXObject("Microsoft.XMLHTTP");
        }else{
            return false;
        }
    }
}
// ROOMデータの読み込み
function callRoomdData(data_url,roomid){
    var room_data_file=data_url+'r/n/'+roomid+'/data.xml?'+Math.random();
	var HttpObject=createXMLHttpRequest();
	HttpObject.open('GET',room_data_file,true);
	HttpObject.onreadystatechange=function(){
		if(HttpObject.readyState==4){
            if(HttpObject.status==200){
                CB(HttpObject);
                xml_not_found_flag=0;
            }else{
                xml_not_found_flag++;
                var d_location=document.location;
                if(d_location['pathname'].indexOf('/sp/')!==-1){ // SP版
                    if(xml_not_found_flag>600){
                        window.location.href=CONST_ROBBY_URL_ROOT+'sp/roomout.php?err=7';
                    }
                }else{ // PC版
                    if(xml_not_found_flag>60){
                        window.location.href=CONST_ROBBY_URL_ROOT+'roomout.php?err=7';
                    }
                }
            }
		}
	}
    HttpObject.overrideMimeType('text/xml;charset=UTF-8');
	HttpObject.send(null);
}
// メイン処理（コールバック）
function CB(HttpObj){
    if((HttpObj.responseXML!==null)&&(HttpObj.responseXML.documentElement!==null)){
        var resHTTP=HttpObj.responseXML.documentElement;
    }else{
        return false;
    }
    
    var strRoomId=document.getElementById('id_room_id').value;
    var strPlayerId=document.getElementById('id_player_id').value;
    var now_date=new Date();
    var now=now_date.getTime()+time_revised_value;
    
    // チャット情報
    processChatText(resHTTP,now,strPlayerId);
    
    // ダイス情報
    var memberDiceSurface = resHTTP.getElementsByTagName('d_surface');
    var memberDiceNumber = resHTTP.getElementsByTagName('d_number');
    var memberDiceResult = resHTTP.getElementsByTagName('d_result');
    var memberDiceRoll = resHTTP.getElementsByTagName('dr_count');
    var intDiceResult = Number(memberDiceResult[0].textContent);
    var strDiceSurface = memberDiceSurface[0].textContent;
    var strDiceNumber = memberDiceNumber[0].textContent;
    var arrayDiceSurface = strDiceSurface.split(",");
    var arrayDiceNumber = strDiceNumber.split(",");
    var textDiceHtml = '';
    // リール処理
    var max_no=0;
    for(j=0;j<arrayDiceSurface.length;j++){
        max_no=max_no+Number(arrayDiceSurface[j]);
    }
    if(max_no<intDiceResult){
        max_no = intDiceResult;
    }
    var reel_no = String(max_no).length;
    if(now>(parseInt(memberDiceRoll[0].textContent)*1000)){
        var dice_sum_total = intDiceResult;
        textDiceHtml += '<div id="reel_image_box">';
        for(j=(reel_no-1);j>=0;j--){
            display_no =  Math.floor(dice_sum_total/Math.pow(10,j));
            textDiceHtml +=
                '<img src="'+CONST_URL_ROOT+'images/dicex_' + display_no + '.png" width="44" height="50">';
            dice_sum_total=dice_sum_total-display_no*Math.pow(10,j);
        }
        textDiceHtml += '</div>';
    }else{
        textDiceHtml += '<div id="reel_image_box">';
        for(j=0;j<reel_no;j++){
            textDiceHtml +=
                '<img src="'+CONST_URL_ROOT+'images/dicexm.gif" width="44" height="50">';
        }
        textDiceHtml += '</div>';
    }
    // ロール処理
    textDiceHtml += '<div id="dice_image_box">';
    if(now>(parseInt(memberDiceRoll[0].textContent)*1000)){
        for(j=0;j<arrayDiceNumber.length;j++){
            if((arrayDiceSurface[j]==2)||(arrayDiceSurface[j]==4)||(arrayDiceSurface[j]==6)||(arrayDiceSurface[j]==8)||
               (arrayDiceSurface[j]==10)||(arrayDiceSurface[j]==12)||(arrayDiceSurface[j]==20)){
                textDiceHtml +=
                    '<div class="stopped_dice"><img src="'+CONST_URL_ROOT+'images/dice'+arrayDiceSurface[j]+'_' + arrayDiceNumber[j] + '.png" width="50" height="50"></div>';
            }
        }
    }else{
        for(j=0;j<arrayDiceNumber.length;j++){
            if((arrayDiceSurface[j]==2)||(arrayDiceSurface[j]==4)||(arrayDiceSurface[j]==6)||(arrayDiceSurface[j]==8)||
               (arrayDiceSurface[j]==10)||(arrayDiceSurface[j]==12)||(arrayDiceSurface[j]==20)){
                textDiceHtml +=
                    '<div class="rolling_dice"><img src="'+CONST_URL_ROOT+'images/dice'+arrayDiceSurface[j]+'m.gif" width="68" height="68"></div>';
            }
        }
    }
    textDiceHtml += '</div>';
    if(document.getElementById('dice_space').innerHTML!=textDiceHtml){
        document.getElementById('dice_space').innerHTML = textDiceHtml;
    }
    
    // キャラクター情報
    var nodeCharacterSpace = document.getElementById('character_space');
    var memberCharOwner = resHTTP.getElementsByTagName('char_owner');
    var memberCharId = resHTTP.getElementsByTagName('char_id');
    var memberCharName = resHTTP.getElementsByTagName('char_name');
    var memberCharHp = resHTTP.getElementsByTagName('char_hp');
    var memberCharMhp = resHTTP.getElementsByTagName('char_mhp');
    var memberCharMp = resHTTP.getElementsByTagName('char_mp');
    var memberCharMmp = resHTTP.getElementsByTagName('char_mmp');
    var memberCharOuterUrl = resHTTP.getElementsByTagName('char_outer_url');
    var memberCharMemo = resHTTP.getElementsByTagName('char_memo');
    var memberCharImage = resHTTP.getElementsByTagName('char_image');
    var noneUpdateCharList = false;
    var memberCharTarget = null;
    // キャラクターの追加
    for(k = 0; k < (memberCharOwner.length); k++){
        memberCharTarget = document.getElementById('cl_'+memberCharId[k].textContent);
        if(memberCharTarget==null){
            noneUpdateCharList = true;
            break;
        }
    }
    // キャラクターの削除
    if(noneUpdateCharList != true){
        var flagExistenceNode = false;
        for(i = 0; i< (nodeCharacterSpace.children.length); i++){
            flagExistenceNode = false;
            for(k = 0; k < (memberCharId.length); k++){
                if('cl_'+memberCharId[k].textContent == nodeCharacterSpace.children[i].id){
                    flagExistenceNode = true;
                    break;
                }
            }
            if(flagExistenceNode == false){
                noneUpdateCharList = true;
                break;
            }
        }
    }
    if(noneUpdateCharList==true){
        var leftCharDetail = window.innerWidth;
        if(leftCharDetail>0){
            leftCharDetail=(leftCharDetail-540)/2-55;
        }
        var outerUrlFlag=false;
        var textCharURL='';
        var textCharHtml='';
        for(k = 0; k < (memberCharOwner.length); k++){
            textCharHtml += '<div id="cl_'+memberCharId[k].textContent+'" class="character_list">';
            textCharHtml += '<div class="top">';
            if(typeof(memberCharOuterUrl[k])!="undefined"){
                if(memberCharOuterUrl[k].textContent==''){
                    outerUrlFlag=false;
                }else{
                    outerUrlFlag=true;
                }
            }else{
                outerUrlFlag=false;
            }
            if(outerUrlFlag==false){
                textCharURL=CONST_ROBBY_URL_ROOT+'/character-detail.php?c='+memberCharId[k].textContent+'&s='+memberCharOwner[k].textContent;
            }else{
				var str_outerUrl=memberCharOuterUrl[k].textContent;
				textCharURL=str_outerUrl.replace('＆','&');
            }
            textCharHtml += '<div id="cn_'+memberCharId[k].textContent+'" class="name"><a id="cnat_'+memberCharId[k].textContent+'" href="'+textCharURL+'" target="_blank">'+memberCharName[k].textContent+'</a></div>';
            
            textCharHtml += '<div id="cdc_'+memberCharId[k].textContent+'" class="delete">';
            textCharHtml += '<img src="'+CONST_URL_ROOT+'images/m_icon134.png" onClick="delFromCharList(\''+memberCharId[k].textContent+'\',\''+memberCharName[k].textContent+'\');" ';
            textCharHtml += 'width="16" height="16" border="0" />';
            textCharHtml += '</div>';
            
            textCharHtml += '<div id="cdb_'+memberCharId[k].textContent+'" class="detail">';
            textCharHtml += '<img src="'+CONST_URL_ROOT+'images/m_icon17.png" onClick="openExplanationWindow(\''+memberCharId[k].textContent+'\',\''+memberCharOwner[k].textContent+'\',\'detail_b\',\'情報（'+memberCharName[k].textContent+'の詳細B）\');" ';
            if(outerUrlFlag==true){
                textCharHtml += 'style="visibility:hidden" ';
            }
            textCharHtml += 'width="16" height="16" border="0" />';
            textCharHtml += '</div>';
            textCharHtml += '<div id="cda_'+memberCharId[k].textContent+'" class="detail">';
            textCharHtml += '<img src="'+CONST_URL_ROOT+'images/m_icon16.png" onClick="openExplanationWindow(\''+memberCharId[k].textContent+'\',\''+memberCharOwner[k].textContent+'\',\'detail_a\',\'情報（'+memberCharName[k].textContent+'の詳細A）\');" ';
            if(outerUrlFlag==true){
                textCharHtml += 'style="visibility:hidden" ';
            }
            textCharHtml += 'width="16" height="16" border="0" />';
            textCharHtml += '</div>';
            textCharHtml += '</div>';
            textCharHtml += '<div class="char_state">';
            if(memberCharImage[k].textContent==''){
                textCharHtml += '<div class="image"><img id="chesscharNtB'+memberCharId[k].textContent+'" src="'+CONST_URL_ROOT+'images/no_image256.jpg" width="50" border="0" alt="'+memberCharName[k].textContent+'" /></div>';
            }else{
                textCharHtml += '<div class="image"><img id="chesscharNtB'+memberCharId[k].textContent+'" src="'+memberCharImage[k].textContent+'" width="50" border="0" alt="'+memberCharName[k].textContent+'" /></div>';
            }
            textCharHtml += '<p class="physical phy_hp"><img src="'+CONST_URL_ROOT+'images/m_icon86.png" style="vertical-align:middle;" width="12" height="12" border="0" />';
            textCharHtml += '<input id="ch_'+memberCharId[k].textContent+'" class="pv" type="text" value="'+memberCharHp[k].textContent+'" ';
            if(memberCharOwner[k].textContent != strPlayerId){
                textCharHtml += 'readonly';
            }else{
                textCharHtml += 'onfocus="setActiveInputCharData(\''+memberCharId[k].textContent+'\');" onblur="removeActiveInputCharData(\''+memberCharId[k].textContent+'\');"';
            }
            textCharHtml += '/><span style="padding:0 3px;">/</span>';
            textCharHtml += '<input id="cmh_'+memberCharId[k].textContent+'" class="mv" type="text" value="'+memberCharMhp[k].textContent+'" ';
            if(memberCharOwner[k].textContent != strPlayerId){
                textCharHtml += 'readonly';
            }else{
                textCharHtml += 'onfocus="setActiveInputCharData(\''+memberCharId[k].textContent+'\');" onblur="removeActiveInputCharData(\''+memberCharId[k].textContent+'\');"';
            }
            textCharHtml += '/></p>';
            textCharHtml += '<p class="physical phy_mp"><img src="'+CONST_URL_ROOT+'images/m_icon87.png" style="vertical-align:middle;" width="12" height="12" border="0" />';
            textCharHtml += '<input id="cm_'+memberCharId[k].textContent+'" class="pv" type="text" value="'+memberCharMp[k].textContent+'" ';
            if(memberCharOwner[k].textContent != strPlayerId){
                textCharHtml += 'readonly';
            }else{
                textCharHtml += 'onfocus="setActiveInputCharData(\''+memberCharId[k].textContent+'\');" onblur="removeActiveInputCharData(\''+memberCharId[k].textContent+'\');"';
            }
            textCharHtml += '/><span style="padding:0 3px;">/</span>';
            textCharHtml += '<input id="cmm_'+memberCharId[k].textContent+'" class="mv" type="text" value="'+memberCharMmp[k].textContent+'" ';
            if(memberCharOwner[k].textContent != strPlayerId){
                textCharHtml += 'readonly';
            }else{
                textCharHtml += 'onfocus="setActiveInputCharData(\''+memberCharId[k].textContent+'\');" onblur="removeActiveInputCharData(\''+memberCharId[k].textContent+'\');"';
            }
            textCharHtml += '/></p>';
            textCharHtml += '<textarea id="cme_'+memberCharId[k].textContent+'" class="char_smemo" name="detail_a" ';
            if(memberCharOwner[k].textContent != strPlayerId){
                textCharHtml += 'readonly';
            }else{
                textCharHtml += 'onfocus="setActiveInputCharData(\''+memberCharId[k].textContent+'\');" onblur="removeActiveInputCharData(\''+memberCharId[k].textContent+'\');"';
            }
            textCharHtml += '>'+memberCharMemo[k].textContent+'</textarea>';
            textCharHtml += '</div>';
            textCharHtml += '</div>';
        }
        nodeCharacterSpace.innerHTML=textCharHtml;
    }
    var nodeTarget = null;
    for(k = 0; k < (memberCharOwner.length); k++){
        nodeTarget = document.getElementById('cnat_'+memberCharId[k].textContent);
        if(nodeTarget.innerHTML!=memberCharName[k].textContent){
            nodeTarget.innerHTML=memberCharName[k].textContent;
        }
        nodeTarget = document.getElementById('chesscharNtB'+memberCharId[k].textContent);
        if(nodeTarget.src != memberCharImage[k].textContent){
            if(memberCharImage[k].textContent == ''){
                nodeTarget.src = CONST_URL_ROOT+'images/no_image256.jpg';
            }else{
                nodeTarget.src = memberCharImage[k].textContent;
            }
        }
        if(memberCharOwner[k].textContent != strPlayerId){
            nodeTarget = document.getElementById('ch_'+memberCharId[k].textContent);
            if(nodeTarget.value != memberCharHp[k].textContent){
                nodeTarget.value = memberCharHp[k].textContent;
            }
            nodeTarget = document.getElementById('cmh_'+memberCharId[k].textContent);
            if(nodeTarget.value != memberCharMhp[k].textContent){
                nodeTarget.value = memberCharMhp[k].textContent;
            }
            nodeTarget = document.getElementById('cm_'+memberCharId[k].textContent);
            if(nodeTarget.value != memberCharMp[k].textContent){
                nodeTarget.value = memberCharMp[k].textContent;
            }
            nodeTarget = document.getElementById('cmm_'+memberCharId[k].textContent);
            if(nodeTarget.value != memberCharMmp[k].textContent){
                nodeTarget.value = memberCharMmp[k].textContent;
            }
            nodeTarget = document.getElementById('cme_'+memberCharId[k].textContent);
            if(nodeTarget.value != memberCharMemo[k].textContent){
                nodeTarget.value = memberCharMemo[k].textContent;
            }
        }
    }
    // 参加者情報
    var nodeParticipantSpace=document.getElementById('participant_space');
    var memberParticipantId=resHTTP.getElementsByTagName('participant_id');
    var memberParticipantNm=resHTTP.getElementsByTagName('participant_nm');
    var memberParticipantUt=resHTTP.getElementsByTagName('participant_ut');
    var textParticipantHtml='';
    for(k=0;k<(memberParticipantId.length);k++){
        listNickName[memberParticipantId[k].textContent]={0:memberParticipantNm[k].textContent,1:memberParticipantUt[k].textContent};
        textParticipantHtml+='<div class="participant_list">';
        textParticipantHtml+='<p class="top"><a href="'+CONST_ROBBY_URL_ROOT+'player-detail.php?c='+memberParticipantId[k].textContent+'" target="_blank">'+memberParticipantNm[k].textContent+'</a></p>';
        textParticipantHtml+='<p class="bottom">ID:<span class="plist_id" onClick="pushPListID(\''+memberParticipantId[k].textContent+'\');" title="'+memberParticipantId[k].textContent+'">'+memberParticipantId[k].textContent+'</span>　LAST:'+stmpToDate(memberParticipantUt[k].textContent)+'</p>';
        textParticipantHtml+='</div>';    
    }
    if(nodeParticipantSpace.innerHTML!=textParticipantHtml){
        nodeParticipantSpace.innerHTML=textParticipantHtml;
    }
    // 共有メモ・設定の更新
    var memberGameData=null;
    var elementGameData=null;
    if(noneUpdateSettingId!='game_memo'){
		setMemoDatas(resHTTP.getElementsByTagName('memo'));
		if(listMemoData[active_memo_key]){
			document.getElementById('game_memo').value=listMemoData[active_memo_key]['txt'];
			document.getElementById('game_memo_img').src=listMemoData[active_memo_key]['img'];
			if(listMemoData[active_memo_key]['img']!=''){
				document.getElementById('game_memo_img').style.visibility='visible';
			}else{
				document.getElementById('game_memo_img').style.visibility='hidden';
			}
		}
    }
	updatePopUpWindow();
    // ウィンドウ_ポップアップ
    if((typeof openPopUpWindow)=='function'){
        openPopUpWindow(resHTTP.getElementsByTagName('memo'));
    }
    var changed_scale_flag=false;
    if(noneUpdateSettingId!='game_boardwidth'){
        var memberGameData=resHTTP.getElementsByTagName('game_boardwidth');
        var scale_data=parseInt(memberGameData[0].textContent);
        var elementGameData=document.getElementById("game_boardwidth");
        if(elementGameData.value!=scale_data){
            if(flagOpenRoomStting!=1){
                elementGameData.value=scale_data;
            }
        }
        scale_data=scale_data*32;
        elementGameData=document.getElementById("chessboard");
        if(elementGameData.style.width!=scale_data+'px'){
            elementGameData.style.width=scale_data+'px';
            document.getElementById("boardnumber").style.width=scale_data+'px';
            document.getElementById("boardgrid").style.width=scale_data+'px';
            changed_scale_flag=true;
        }
        var int_syncsize_flag=0;
        memberGameData=resHTTP.getElementsByTagName("game_syncboardsize");
        if(memberGameData[0]!=null){
            int_syncsize_flag=parseInt(memberGameData[0].textContent);
        }
        elementGameData=document.getElementById("boardimage");
        if(int_syncsize_flag!=0){
            if(typeof elementGameData.naturalWidth!=='undefined'){
                scale_data=elementGameData.naturalWidth;
            }
        }
        if(elementGameData.width!=scale_data){
            document.getElementById("boardimage").width=scale_data;
        }
    }
    if(noneUpdateSettingId!='game_boardheight'){
        var memberGameData=resHTTP.getElementsByTagName('game_boardheight');
        var scale_data=parseInt(memberGameData[0].textContent);
        var elementGameData=document.getElementById("game_boardheight");
        if(elementGameData.value!=scale_data){
            if(flagOpenRoomStting!=1){
                elementGameData.value=scale_data;
            }
        }
        scale_data=scale_data*32;
        elementGameData=document.getElementById("chessboard");
        if(elementGameData.style.height!=scale_data+'px'){
            elementGameData.style.height=scale_data+'px';
            document.getElementById("boardnumber").style.height=scale_data+'px';
            document.getElementById("boardgrid").style.height=scale_data+'px';
            changed_scale_flag=true;
        }
        var int_syncsize_flag=0;
        memberGameData=resHTTP.getElementsByTagName("game_syncboardsize");
        if(memberGameData[0]!=null){
            int_syncsize_flag=parseInt(memberGameData[0].textContent);
        }
        elementGameData=document.getElementById("boardimage");
        if(int_syncsize_flag!=0){
            if(typeof elementGameData.naturalHeight!=='undefined'){
                scale_data=elementGameData.naturalHeight;
            }
        }
        if(elementGameData.height!=scale_data){
            document.getElementById("boardimage").height=scale_data;
        }
    }
    if(changed_scale_flag==true){
        var memberGameData=resHTTP.getElementsByTagName('game_boardwidth');
        var scale_data_x=parseInt(memberGameData[0].textContent);
        memberGameData=resHTTP.getElementsByTagName('game_boardheight');
        var scale_data_y=parseInt(memberGameData[0].textContent);
        if((typeof changeMappingDataSize)=='function'){
            changeMappingDataSize(scale_data_x,scale_data_y);
        }
    }
    if(noneUpdateSettingId!='game_syncboardsize'){
        var memberGameData = resHTTP.getElementsByTagName('game_syncboardsize');
        var elementGameData = document.getElementById("game_syncboardsize");
        var bool_syncsize_flag=false;
        if(memberGameData[0]!=null){
            if(memberGameData[0].textContent!=0){
                bool_syncsize_flag=true;
            }
        }
        if(elementGameData.checked!=bool_syncsize_flag){
            if(flagOpenRoomStting != 1){
                elementGameData.checked=bool_syncsize_flag;
            }
        }
    }
    if(noneUpdateSettingId!='game_grid'){
        var memberGameData = resHTTP.getElementsByTagName('game_grid');
        var elementGameData = document.getElementById("game_grid");
        if(elementGameData.value!=memberGameData[0].textContent){
            if(flagOpenRoomStting != 1){
                elementGameData.value=memberGameData[0].textContent;
            }
        }
        document.getElementById("boardgrid").style.opacity=(memberGameData[0].textContent/10);
    }
    if(noneUpdateSettingId!='game_backimage'){
        var memberGameData = resHTTP.getElementsByTagName('game_backimage');
        var elementGameData = document.getElementById("game_backimage");
        var elementBoardImage = document.getElementById("boardimage");
        if(memberGameData[0].textContent==''){
            elementBoardImage.style.display='none';
        }else if(elementBoardImage.src!=memberGameData[0].textContent){
            elementGameData.value=memberGameData[0].textContent;
            elementBoardImage.src=memberGameData[0].textContent;
            elementBoardImage.style.display='inline-block';
        }else{
            elementBoardImage.style.display='inline-block';
        }
    }
    if(noneUpdateSettingId!='game_imagestrength'){
        var memberGameData = resHTTP.getElementsByTagName('game_imagestrength');
        var elementGameData = document.getElementById("game_imagestrength");
        if(elementGameData.value!=memberGameData[0].textContent){
            if(flagOpenRoomStting != 1){
                elementGameData.value=memberGameData[0].textContent;
            }
        }
        document.getElementById("boardimage").style.opacity=(memberGameData[0].textContent/10);
    }
    //if(typeof(textDiceBot)!="undefined"){
        if(noneUpdateSettingId!='game_dicebot'){
            var memberGameData=resHTTP.getElementsByTagName('game_dicebot');
            var elementGameData=document.getElementById("game_dicebot");
            if(elementGameData.value!=memberGameData[0].textContent){
                elementGameData.value=memberGameData[0].textContent;
                //document.getElementById('dicebot_text').innerHTML=textDiceBot[document.getElementById('game_dicebot').value][1];
            }
        }
    //}
    var memberGameMusic=resHTTP.getElementsByTagName('game_music');
    var memberGameMusicState=resHTTP.getElementsByTagName('game_music_state');
    if((audio_state!=memberGameMusicState[0].textContent)||(document.getElementById('game_audio').src!=memberGameMusic[0].textContent)){
        if(noneUpdateMusicStateTime==0){
            var memberGameAudio=document.getElementById('game_audio');
            if(memberGameMusicState[0].textContent=='play'){
                if(memberGameAudio.src!=memberGameMusic[0].textContent){
                    if(memberGameMusic[0].textContent==''){
                        if(memberGameAudio.src.indexOf('sounds/se02.mp3')==-1){
                            memberGameAudio.src=CONST_URL_ROOT+'sounds/se02.mp3';
                        }
                    }else{
                        memberGameAudio.src=memberGameMusic[0].textContent;
                    }
                }
                if(memberGameAudio.loop!=null){
                    if(memberGameAudio.loop!=true){
                        memberGameAudio.loop=true;
                    }
                }
                if(memberGameAudio.paused==true){
                    memberGameAudio.play();
                }
            }else{
                if(memberGameAudio.paused==false){
                    memberGameAudio.pause();
                }
            }
        }else{
            noneUpdateMusicStateTime=noneUpdateMusicStateTime>0?--noneUpdateMusicStateTime:0;
        }
    }
    if(audio_state=='play'){
        document.getElementById("now_music_state").innerHTML='<span style="color:#00CC00;">▶</span>';
    }else{
        document.getElementById("now_music_state").innerHTML='<span style="color:#606060;">■</span>';
    }
    // チェスボードの更新
    if(noneUpdateBoardTime<=0){
        var strMapData='';
        var resultOnMap='';
        var memberChessBoard=resHTTP.getElementsByTagName('map_data');
        var elementChessBoard=document.getElementById("chessboard");
        for(i = 0; i< (elementChessBoard.children.length); i++){
            if(i != 0){
                resultOnMap+='^';
            }
            if(elementChessBoard.children[i].tagName.toLowerCase()=='img'){
                resultOnMap+=String(elementChessBoard.children[i].id)+'|'+String(elementChessBoard.children[i].src)+'|'+String(elementChessBoard.children[i].style.top)+'|'+String(elementChessBoard.children[i].style.left)+'|'+String(elementChessBoard.children[i].width)+'|'+String(elementChessBoard.children[i].height)+'|img';
            }else{
                resultOnMap+=String(elementChessBoard.children[i].id)+'||'+String(elementChessBoard.children[i].style.top)+'|'+String(elementChessBoard.children[i].style.left)+'|||oth';
            }
        }
        strMapData = memberChessBoard[0].textContent;
        if(resultOnMap!=strMapData){
            var arrayCharOnMapData=strMapData.split("^");
            var arrayMapChipRecord=[];
            var d_chessman_array=[];
            var textImageData='';
            var countChessman=1;
            var nametag_element=null;
            for(var i=0;i<arrayCharOnMapData.length;i++){
                if(arrayCharOnMapData[i]){
                    //arrayMapChipRecord=['','','0px','0px','0','0']; // 0=id 1=url 2=top 3=left 4=width 5=height
                    arrayMapChipRecord=arrayCharOnMapData[i].split('|');
                    d_chessman_array.push(arrayMapChipRecord);
                }
            }
            if(chessman_array.toString()!=d_chessman_array.toString()){
                drawAllChessman(d_chessman_array);
                chessman_array=d_chessman_array;
            }
        }
    }else{
        noneUpdateBoardTime=noneUpdateBoardTime>0?--noneUpdateBoardTime:0;
    }
    // 参加者数
    var count_participant=0;
    var count_observer=0;
    var memberLoginPlayers=resHTTP.getElementsByTagName('login_players');
    var strLoginPlayers=memberLoginPlayers[0].textContent;
    var arrayLoginPlayers=strLoginPlayers.split("^");
    var arrayLoginPlayerRecord=[];
    for(i=0;i<arrayLoginPlayers.length;i++){
        if(arrayLoginPlayers[i]){
            arrayLoginPlayerRecord=arrayLoginPlayers[i].split('|');
            if(arrayLoginPlayerRecord[2]==1){
                count_observer++;
            }else{
                count_participant++;
            }
        }
    }
    var elementCountParticipant=document.getElementById("id_c_participant");
    if(elementCountParticipant.innerHTML!=count_participant){
        elementCountParticipant.innerHTML=count_participant;
    }
    var elementCountObserver=document.getElementById("id_c_observer");
    if(elementCountObserver.innerHTML!=count_observer){
        elementCountObserver.innerHTML=count_observer;
    }
    // 立ち絵の更新
    if((typeof displayStandImage)=='function'){
        displayStandImage(resHTTP);
    }
    // カードスペースがあるかチェック
    if((typeof displayCardSpace)=='function'){
        displayCardSpace(resHTTP);
    }
    // タイマーの更新
    if((typeof changeTimerValue)=='function'){
        var memberGameData=resHTTP.getElementsByTagName('game_timer');
        if(memberGameData[0]!=null){
            var str_game_timer=memberGameData[0].textContent;
            changeTimerValue(str_game_timer);
        }
    }
    // マップチップの確認
    updateMapChipImg(resHTTP);
    // マップの描画
    var memberGameData=resHTTP.getElementsByTagName('game_mapping');
    if(memberGameData[0]!=null){
        var str_game_mapping=memberGameData[0].textContent;
        drawMapImage(disassembleMappingData(str_game_mapping));
    }
    // マッピングデータ送信
    if((typeof sendChangeMappingData)=='function'){
        sendChangeMappingData();
    }
}
// Display CopyRight
function writeCopyRight(title,begin){
    document.write("Copyright &copy ");
    thisDate = new Date();
    thisYear = thisDate.getFullYear();
    if(begin!=thisYear){
        document.write(begin+"-"+thisYear);
    }else{
        document.write(begin);
    }
    document.write(" "+title);
}
// 正規表現エスケープ
function escapeRegExp(target_string){
    return target_string.replace(/[-\/\\^$*+?.()|[\]{}]/g,'\$&');
}
// 文字列から画像URLを探査し返す
function searchImgUrl(target_string){
    var str_candidate_img=target_string.replace(/＆/,'&');
    var match_result=null;
    // 本サイト内の画像かチェック
    if(str_candidate_img.indexOf(CONST_URL_ROOT)!=-1){
        var pattern_img=new RegExp('('+escapeRegExp(CONST_URL_ROOT)+'r\/n\/[0-9a-z]+\/((uploadimage[0-9m]+|char_[._0-9a-z]+)|[0-9a-z]+\/i\/chara[0-9]+))','i');
        if(match_result=str_candidate_img.match(pattern_img)){
            return match_result[0];
        }else{
            return false;
        }
    // trpgsession.click内の画像かチェック
    }else if(str_candidate_img.indexOf('//trpgsession.click')!=-1){
        if(match_result=str_candidate_img.match(/((|https?:)\/\/trpgsession.click\/[cp]\/[0-9a-z]+\/(i|image)\/[a-z]+[0-9]+)/i)){
            return match_result[0];
        }else{
            return false;
        }
    // 外部サイトかチェック
    }else if(match_result=str_candidate_img.match(/(https?:\/\/[0-9a-z_.,:;+*=@#&$%?\/!()\'-]+\.(png|jpg|jpeg|gif))/i)){
        return match_result[0];
    }
    return false;
}
function processChatText(resHTTP,now,strPlayerId){
    var memberContent=resHTTP.getElementsByTagName('content');
    
    var textLine='';
    var contentData=0;
    var contentAuthor='システム';
    var contentChatColor='#000000';
    var contentID='';
    var contentType=1;
    
    var checkPlotFlag='pd&'+strPlayerId+' ';
    var checkSecretDiceFlag='sd&'+strPlayerId+' ';
    var hideTextFlag=0;
    var shortcut_flag=0;
    
    text_chat_array=[];
    //ノードの数だけループ
    for(var i = (memberContent.length-1); i >= 0; i--){
        textLine='';
        contentData=0;
        contentAuthor='システム';
        contentChatColor='#000000';
        contentID=String(memberContent[i].getAttribute("id"));
        contentType=1;
        for(var j=0;j<memberContent[i].childNodes.length;j++){
            if(memberContent[i].childNodes[j].nodeName=='date'){
                contentData=memberContent[i].childNodes[j].textContent;
            }else if(memberContent[i].childNodes[j].nodeName=='text'){
                textLine=memberContent[i].childNodes[j].textContent;
            }else if(memberContent[i].childNodes[j].nodeName=='author'){
                contentAuthor=memberContent[i].childNodes[j].textContent;
            }else if(memberContent[i].childNodes[j].nodeName=='chat_color'){
                contentChatColor=memberContent[i].childNodes[j].textContent;
            }else if(memberContent[i].childNodes[j].nodeName=='ctyp'){
                contentType=memberContent[i].childNodes[j].textContent;
            }
        }
        if(now>(parseInt(contentData)*1000)){
            hideTextFlag=0;
            shortcut_flag=0;
            if(/^pd&[a-zA-Z0-9]+? /.test(textLine)==true){
				shortcut_flag=1;
                if(textLine.indexOf(checkPlotFlag)!=-1){
                    textLine=textLine.replace(/^pd&[a-zA-Z0-9]+? /,'(秘) ');
                }else{
                    textLine='（プロット）';
                }
            }else if(/^sd&[a-zA-Z0-9]+? /.test(textLine)==true){
                if(textLine.indexOf(checkSecretDiceFlag)!=-1){
                    textLine=textLine.replace(/^sd&[a-zA-Z0-9]+? /,'(秘) ');
                    shortcut_flag=1;
                }else{
                    textLine='（シークレットダイス）';
                }
            }else if(/^@[a-zA-Z0-9]+?&[a-zA-Z0-9]+? /.test(textLine)==true){
                reg1=new RegExp('^@'+strPlayerId+'&[a-zA-Z0-9]+? ');
                reg2=new RegExp('^@[a-zA-Z0-9]+?&'+strPlayerId+' ');
                if(reg1.test(textLine)==true){
                    textLine=textLine.replace(/^@[a-zA-Z0-9]+?&([a-zA-Z0-9]+?) /,'(ウィスパー受信:$1) ');
                }else if(reg2.test(textLine)==true){
                    textLine=textLine.replace(/^@([a-zA-Z0-9]+?)&[a-zA-Z0-9]+? /,'(ウィスパー送信:$1) ');
                }else{
                    hideTextFlag = 1;
                }
            }else if(/(|https?:)\/\/[0-9a-zA-Z_.,:;+*=@#＆&$%?\/!()\'-]+/.test(textLine)==true){
                textLine=textLine.replace(/＆/,'&');
                textLine=textLine.replace(/((|https?:)\/\/[0-9a-zA-Z_.,:;+*=@#&$%?\/!()\'-]+)/,'<a href="$1" target="_blank">$1</a>');
            }
            if(hideTextFlag==0){
                text_chat_array.push([contentData,contentChatColor,contentAuthor,textLine,contentID,shortcut_flag,contentType]);
            }
        }
    }
    text_chat_array.sort(function(a,b){
        if(a[0]>b[0]){return -1;}
        if(a[0]<b[0]){return 1;}
        return 0;
    });
    // テキストの出力
    displayChatText(chat_type_flag);
}
function displayChatText(ctyp){
    var now_date=new Date();
    var now=now_date.getTime()+time_revised_value;
    
    var last_comment_flag=false;
    var last_comment_time=0;
    var temporary_last_comment='';
    var textChatData='';
    var outputlineData='';
    
    var temp_tag_last_comment_date=[0,0,0,0];
    
    for(var i=0;i<text_chat_array.length;i++){
        epoc_time=text_chat_array[i][0];
        diff_time=now-epoc_time*1000;
        if((ctyp==0)||(text_chat_array[i][6]==ctyp)){
            if(text_chat_array[i][4]!=='null'){
                outputlineData='';
                outputlineData+='<p id="csid_'+text_chat_array[i][4]+'">';
                if(text_chat_array[i][6]==1){
                    outputlineData+='<span class="ctm">メ</span>';
                }else if(text_chat_array[i][6]==2){
                    outputlineData+='<span class="ctz">雑</span>';
                }else if(text_chat_array[i][6]==3){
                    outputlineData+='<span class="ctk">見</span>';
                }
                outputlineData+='<span class="'+font_color_list[text_chat_array[i][1]]+'">'+text_chat_array[i][2]+' : '+text_chat_array[i][3]+'</span>';
                outputlineData+='<span class="font_lightgray"> '+stmpToDate(text_chat_array[i][0])+' ';
                if(text_chat_array[i][5]==1){
                    outputlineData+='<span class="open_remark" onClick="pushOpenRemark(\''+text_chat_array[i][4]+'\');">#'+text_chat_array[i][4]+'</span>';
                }else{
                    outputlineData+='#'+text_chat_array[i][4];
                }
                outputlineData+='</span>';
                outputlineData+=newCommentImg(diff_time)+'</p>';
                textChatData+=outputlineData;
            }else{
                textChatData+='<p id="csid_'+text_chat_array[i][4]+'"><span style="color:'+text_chat_array[i][1]+';">'+text_chat_array[i][2]+' : '+text_chat_array[i][3]+'</span>'+
                              '<span class="font_lightgray"> '+stmpToDate(text_chat_array[i][0])+'</span>'+
                              newCommentImg(diff_time)+'</p>';
            }
            if(last_comment_flag==false){
                last_comment_time=epoc_time;
                temporary_last_comment=textChatData;
                //temporary_last_comment=text_chat_array[i][2] + ' : ' + text_chat_array[i][3];
                last_comment_flag=true;
            }
        }
        if(temp_tag_last_comment_date[text_chat_array[i][6]]<epoc_time){
            temp_tag_last_comment_date[text_chat_array[i][6]]=epoc_time;
        }
        if((ctyp==0)||
           (text_chat_array[i][6]==ctyp)){
            if(tag_last_comment_date[text_chat_array[i][6]]<epoc_time){
                tag_last_comment_date[text_chat_array[i][6]]=epoc_time;
            }
        }
    }
    if(last_comment.replace( />/g,'&gt;').replace( /</g,'&lt;')!=temporary_last_comment.replace( />/g,'&gt;').replace( /</g,'&lt;')){
        last_comment=temporary_last_comment;
        document.getElementById('comment_space').innerHTML=textChatData;
        if(last_comment_date<last_comment_time){
            if(document.getElementById('game_chat_se').value=='true'){
                if((now-last_comment_time*1000)<10000){
                    //document.getElementById('se_audio_1').src=CONST_URL_ROOT+'sounds/se01.mp3';
                    document.getElementById('se_audio_1').play();
                }
            }
            //last_comment=temporary_last_comment;
            last_comment_date=last_comment_time;
        }
    }
    if(document.getElementById('chat_type0_cnt')!==null){
        for(var i=1;i<4;i++){
            if(temp_tag_last_comment_date[i]>tag_last_comment_date[i]){
                document.getElementById('chat_type'+i+'_cnt').innerHTML='+';
            }else{
                document.getElementById('chat_type'+i+'_cnt').innerHTML='';
            }
        }
    }
}
// コマの描画／削除
function drawAllChessman(d_chessman_array){
    var str_result='';
    var cm_id_list=[];
    // コマ削除
    var cb_elm=document.getElementById('chessboard');
    var cm_elm_list=cb_elm.getElementsByTagName('div');
    var del_elm_list=[];
    for(var i=0;i<cm_elm_list.length;i++) { 
        cm_elm_id=cm_elm_list[i].getAttribute('id');
        var del_flag=true;
        for(var j=0;j<cm_id_list.length;j++){
            if(cm_elm_id==(cm_id_list[j]+'_box')){
                del_flag=false;
                break;
            }
        }
        if(del_flag==true){
            del_elm_list.push(cm_elm_id);
        }
    }
    if(del_elm_list.length>1){
        cb_elm.innerHTML='';
    }else if(del_elm_list.length==1){
        deleteDom(del_elm_list[0]);
    }
    // ベース描画
    for(var i=0;i<d_chessman_array.length;i++){
        cm_id_list[i]=drawChessmanOnBoard(d_chessman_array[i]);
    }
    // イメージ描画
    for(var i=0;i<d_chessman_array.length;i++){
        drawChessImage(d_chessman_array[i]);
    }
}
// DOM削除
function deleteDom(del_id){
    var deleteElement=document.getElementById(del_id);
    if(deleteElement.parentNode.removeChild(deleteElement)){
        return 1;
    }
}
// コマのベース描画
function drawChessmanOnBoard(chessman_record){
    var cm_vl=['','',0,0,32,32,'','',0,1];
    for(var i=0;i<10;i++){
        if(typeof(chessman_record[i])!="undefined"){
            cm_vl[i]=chessman_record[i];
        }
    }
    var target_elm=document.getElementById(cm_vl[0]+'_box');
    if(target_elm==null){
        // box作成
        var new_element=document.createElement('div');
        new_element.setAttribute('id',cm_vl[0]+'_box');
        new_element.setAttribute('style','top:'+(cm_vl[2]-12)+'px;left:'+(cm_vl[3]-3)+'px;');
        new_element.className='chessman';
        document.getElementById('chessboard').appendChild(new_element);
        // title決定
        if(cm_vl[7]!='marking'){
            var cm_title=cm_vl[7]+'('+cm_vl[9]+')';
        }else{
            var cm_title='('+cm_vl[9]+')';
        }
        // img作成
        var new_element=document.createElement('img');
        new_element.setAttribute('id',cm_vl[0]);
        new_element.setAttribute('draggable',true);
        new_element.setAttribute('width',cm_vl[4]);
        new_element.setAttribute('height',cm_vl[5]);
        new_element.setAttribute('title',cm_title);
        if(cm_vl[0].indexOf('range')!=-1){
            new_element.setAttribute('style','z-index:5;opacity:0.5;');
        }else{
            new_element.setAttribute('style','z-index:10;');
        }
        document.getElementById(cm_vl[0]+'_box').appendChild(new_element);
        // title作成
        var new_element=document.createElement('span');
        new_element.setAttribute('id',cm_vl[0]+'_title');
        document.getElementById('text_ruler').textContent=cm_title;
        new_element.className='cm_pnumb';
        new_element.innerHTML=cm_title;
        new_element.setAttribute('style','left:'+(((parseInt(cm_vl[4])+6)/2)-Math.floor(document.getElementById('text_ruler').offsetWidth/2))+'px;');
        document.getElementById(cm_vl[0]+'_box').appendChild(new_element);
    }else{
        target_elm.setAttribute('style','top:'+(cm_vl[2]-12)+'px;left:'+(cm_vl[3]-3)+'px;');
        document.getElementById(cm_vl[0]).setAttribute('width',cm_vl[4]);
        document.getElementById(cm_vl[0]).setAttribute('height',cm_vl[5]);
        if(cm_vl[7]!='marking'){
            var cm_title=cm_vl[7]+'('+cm_vl[9]+')';
        }else{
            var cm_title='('+cm_vl[9]+')';
        }
        document.getElementById('text_ruler').textContent=cm_title;
        document.getElementById(cm_vl[0]+'_title').setAttribute('style','left:'+(((parseInt(cm_vl[4])+6)/2)-Math.floor(document.getElementById('text_ruler').offsetWidth/2))+'px;');
    }
    return cm_vl[0];
}
// コマのイメージ描画
function drawChessImage(chessman_record){
    var cm_vl=['','',0,0,32,32,'','',0,1];
    for(var i=0;i<10;i++){
        if(typeof(chessman_record[i])!="undefined"){
            cm_vl[i]=chessman_record[i];
        }
    }
    if(cm_vl[6]=='img'){
        ImageResize(cm_vl[0],cm_vl[1],'image/png',cm_vl[4],cm_vl[5],cm_vl[8]);
    }
}
// 画像リサイズ／回転処理
function ImageResize(id,image_src,mime_type,width,height,rotate){
    var image=new Image();
    if(rotate==0){
        document.getElementById(id).src=image_src;
    }else{
        image.src=image_src;
        var canvas=document.createElement('canvas');
        if(rotate==90||rotate==270){
            // swap w <==> h
            canvas.width=height;
            canvas.height=width;
        }else{
            canvas.width=width;
            canvas.height=height;
        }
        // Draw (Resize)
        var ctx=canvas.getContext('2d');
        if(rotate && rotate>0){
            ctx.rotate(rotate*Math.PI/180);
            if(rotate==90){
                ctx.translate(0,-height);
            }else if(rotate==180){
                ctx.translate(-width, -height);
            }else if(rotate == 270){
                ctx.translate(-width, 0);
            }
        }
        image.onload=function(){
            ctx.drawImage(image,0,0,width,height);
            document.getElementById(id).src=canvas.toDataURL(mime_type);
        }
    }
}
// マッピングデータ分解
function disassembleMappingData(str_mappingdata){
    var d_map_da=[];
    for(var i=0;i<100;i++){
        d_map_da[i]=[];
        for(var j=0;j<100;j++){
            d_map_da[i][j]=-1;
        }
    }
    var record_mappingdata=str_mappingdata.split("^");
    var array_mappingdata_x=[];
    for(var i=0;i<record_mappingdata.length;i++){
        array_mappingdata_x=record_mappingdata[i].split(',');
        for(var j=0;j<array_mappingdata_x.length;j++){
            d_map_da[i][j]=array_mappingdata_x[j];
        }
    }
    return d_map_da;
}
// マッピングデータ結合
function combineMappingData(array_mappingdata,area_width,area_height){
    var str_mappingdata='';
    var map_surface_width=area_width;
    if(map_surface_width<1){
        for(var i=0;i<100;i++){
            if(array_mappingdata[0][i]==-1){
                break;
            }
            map_surface_width=i;
        }
    }
    var map_surface_height=area_height;
    if(map_surface_height<1){
        for(var i=0;i<100;i++){
            if(array_mappingdata[i][0]==-1){
                break;
            }
            map_surface_height=i;
        }
    }
    for(var i=0;i<map_surface_height;i++){
        if(i!=0){
            str_mappingdata=str_mappingdata+'^';
        }
        for(var j=0;j<map_surface_width;j++){
            if(j!=0){
                str_mappingdata=str_mappingdata+',';
            }
            if(array_mappingdata[i][j]<0){
                str_mappingdata=str_mappingdata+0;
            }else{
                str_mappingdata=str_mappingdata+array_mappingdata[i][j];
            }
        }
    }
    return str_mappingdata;
}
// 参加者名の表示 add.2016.12.19
function displayNickName(id){
    if(listNickName[id]){
        return listNickName[id][0];
    }
    return id;
}
// 参加者名リストNoを返す add.2016.12.20
function intNMListNo(id){
    var result=0;
    var hit_flag=false;
    for(key in listNickName){
        if(key==id){
            hit_flag=true;
            break;
        }
        result=result+1;
    }
    if(hit_flag==true){
        return result;
    }else{
        return 0;
    }
}
// timestamp > 文字日時
function stmpToDate(time_stamp){
    var now = new Date(time_stamp*1000);
    var hour = now.getHours(); // 時
    var min = now.getMinutes(); // 分
    var sec = now.getSeconds(); // 秒
    return ("0"+hour).slice(-2) +':' + ("0"+min).slice(-2) + ':' + ("0"+sec).slice(-2);
}
// 新しいコメントの場合、new画像を表示する
function newCommentImg(diff_time){
    var $result='';
    if(diff_time<30000){
        $result='<img style="margin-left:1em;vertical-align:middle;" src="'+CONST_URL_ROOT+'images/m_icon25.gif" width="30" height="11">';
    }
    return $result;
}
// DOMから値を取得する（nullの場合はstring空白を返す)
function getDomValue(dom){
    if(dom!=null){
        return dom.textContent;
    }else{
        return '';
    }
}
// ローカルの一時記憶領域にボード情報を保存する
function saveLocalBD(room_file,locked_eml_ids){
    var locked_eml_id_list=locked_eml_ids.split(',');
    for(var i=0;i<locked_eml_id_list.length;i++){
        document.getElementById(locked_eml_id_list[i]).disabled=true;
    }
    select_fileValue=document.getElementById('ss_gam_select').value;
    var xmlObject=createXMLHttpRequest();
    xmlObject.onreadystatechange=function(){
        if(xmlObject.readyState==4){
            var xml=xmlObject.responseXML;
            if(xml){
                var elm_map_data=xml.getElementsByTagName('map_data');
                var map_data='';
                if(elm_map_data[0]!=null){
                    map_data=getDomValue(elm_map_data[0]);
                }
                window.localStorage.setItem('map_data',map_data);
                var elm_game_mapping=xml.getElementsByTagName('game_mapping');
                var game_mapping='0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
                if(elm_game_mapping[0]!=null){
                    game_mapping=getDomValue(elm_game_mapping[0]);
                }
                window.localStorage.setItem('game_mapping',game_mapping);
                var elm_game_mapchip=xml.getElementsByTagName('game_mapchip');
                var game_mapchip='';
                if(elm_game_mapchip[0]!=null){
                    game_mapchip=getDomValue(elm_game_mapchip[0]);
                }
                window.localStorage.setItem('game_mapchip',game_mapchip);
                var elm_game_backimage=xml.getElementsByTagName('game_backimage');
                var game_backimage='';
                if(elm_game_backimage[0]!=null){
                    game_backimage=getDomValue(elm_game_backimage[0]);
                }
                window.localStorage.setItem('game_backimage',game_backimage);
                var elm_game_boardwidth=xml.getElementsByTagName('game_boardwidth');
                var game_boardwidth=17;
                if(elm_game_boardwidth[0]!=null){
                    game_boardwidth=getDomValue(elm_game_boardwidth[0]);
                }
                window.localStorage.setItem('game_boardwidth',game_boardwidth);
                var elm_game_boardheight=xml.getElementsByTagName('game_boardheight');
                var game_boardheight=20;
                if(elm_game_boardheight[0]!=null){
                    game_boardheight=getDomValue(elm_game_boardheight[0]);
                }
                window.localStorage.setItem('game_boardheight',game_boardheight);
                var elm_game_syncboardsize=xml.getElementsByTagName('game_syncboardsize');
                var game_syncboardsize=0;
                if(elm_game_syncboardsize[0]!=null){
                    game_syncboardsize=getDomValue(elm_game_syncboardsize[0]);
                }
                window.localStorage.setItem('game_syncboardsize',game_syncboardsize);
                var elm_game_grid=xml.getElementsByTagName('game_grid');
                var game_grid=5;
                if(elm_game_grid[0]!=null){
                    game_grid=getDomValue(elm_game_grid[0]);
                }
                window.localStorage.setItem('game_grid',game_grid);
                openWTL('SWM=ボード情報を一時記憶しました。');
            }
            for(var i=0;i<locked_eml_id_list.length;i++){
                document.getElementById(locked_eml_id_list[i]).disabled=false;
            }
        }
    }
    // openメソッドでXMLファイルを開く
    xmlObject.open('POST',room_file,true);
    xmlObject.send();
}
// ローカルの一時記憶領域にあるボード情報を読み込む
function loadLocalBD(psd_file,room_id,principal_id,locked_eml_ids){
    var locked_eml_id_list=locked_eml_ids.split(',');
    for(var i=0;i<locked_eml_id_list.length;i++){
        document.getElementById(locked_eml_id_list[i]).disabled=true;
    }
    var map_data=window.localStorage.getItem('map_data');
    if(map_data==null){
        map_data='';
    }
    var game_mapping=window.localStorage.getItem('game_mapping');
    if(game_mapping==null){
        game_mapping='0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0^0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
    }
    var game_mapchip=window.localStorage.getItem('game_mapchip');
    if(game_mapchip==null){
        game_mapchip='';
    }
    var game_backimage=window.localStorage.getItem('game_backimage');
    if(game_backimage==null){
        game_backimage='';
    }
    var game_boardwidth=window.localStorage.getItem('game_boardwidth');
    if(game_boardwidth==null){
        game_boardwidth=17;
    }
    var game_boardheight=window.localStorage.getItem('game_boardheight');
    if(game_boardheight==null){
        game_boardheight=20;
    }
    var game_syncboardsize=window.localStorage.getItem('game_syncboardsize');
    if(game_syncboardsize==null){
        game_syncboardsize=0;
    }
    var game_grid=window.localStorage.getItem('game_grid');
    if(game_grid==null){
        game_grid=5;
    }
    $.post(psd_file,{
        xml:room_id,
        principal:principal_id,
        map_data:map_data,
        game_mapping:game_mapping,
        game_mapchip:game_mapchip,
        game_backimage:game_backimage,
        game_boardwidth:game_boardwidth,
        game_boardheight:game_boardheight,
        game_syncboardsize:game_syncboardsize,
        game_grid:game_grid,
    },function(data){
        for(var i=0;i<locked_eml_id_list.length;i++){
            document.getElementById(locked_eml_id_list[i]).disabled=false;
        }
        openWTL('SWM=一時記憶したボード情報を読み込みました。');
    });
}
function convertJsonTextToObject(json_string){
    try{
        var json_object=JSON.parse(json_string);
        return json_object;
    }catch(e){
        return JSON.parse('{"error":"can_not_read","error_description":"読み込みのできないデータです。"}');
    }
}
function pushPListID(p_id){
    document.getElementById('id_comment').value='@'+p_id+' ';
    document.getElementById('id_comment').focus();
}
function pushOpenRemark(r_id){
    document.getElementById('id_comment').value='/o #'+r_id;
    document.getElementById('id_comment').focus();
}
// １要素の削除
function deleteElement(id){
    var dom_obj=document.getElementById(id);
    var dom_obj_parent=dom_obj.parentNode;
    while(dom_obj.lastChild){
        dom_obj.removeChild(dom_obj.lastChild);
    }
    dom_obj_parent.removeChild(dom_obj);
}
// 複数要素の削除
function deleteElements(ids){
    var element_array=[];
    for(var i=0;i<ids.length;i++){
        element_array.push(document.getElementById(ids[i]));
    }
    for(var i=0;i<element_array.length;i++){
        var dom_obj_parent=element_array[i].parentNode;
        while(element_array[i].lastChild){
            element_array[i].removeChild(element_array[i].lastChild);
        }
        dom_obj_parent.removeChild(element_array[i]);
    }
}
function createMappingTool(ic_no){
    var html_v='';
    html_v='<canvas id="mappingtool_ic_'+ic_no+'" class="mappingtool" width="32" height="32" onClick="setMappingTool('+ic_no+')"></canvas>';
    if(ic_no<100){
        $('#mt_bx_line').append(html_v);
    }else{
        $('#mt_bx_deco').append(html_v);
    }
}
function initMappingTool(){
    if(mapping_image.height<32){
        map_linechip_h=0;
        map_linechip_w=0;
        map_decchip_h=0;
        map_decchip_w=0;
    }else if(mapping_image.height>128){
        map_linechip_h=4;
        map_linechip_w=Math.floor(mapping_image.width/32);
        if(mapping_image.height<160){
            map_decchip_w=0;
            map_decchip_h=0;
        }else if(mapping_image.height>256){
            map_decchip_h=4;
            map_decchip_w=map_linechip_w;
        }else{
            map_decchip_h=Math.floor(mapping_image.height/32)-4;
            map_decchip_w=map_linechip_w;
        }
    }else{
        map_linechip_h=Math.floor(mapping_image.height/32);
        map_decchip_h=0;
        map_decchip_w=0;
    }
    var canvas=null;
    var ctx=null;
    var broken_flag=false;
    var mappingtool_count=1;
    for(var i=0;i<map_linechip_w;i++){
        for(var j=0;j<map_linechip_h;j++){
            createMappingTool(mappingtool_count);
            canvas=document.getElementById('mappingtool_ic_'+mappingtool_count);
            ctx=canvas.getContext('2d');
            ctx.drawImage(mapping_image,i*32,j*32,32,32,0,0,32,32);
            mappingtool_count++;
            if(mappingtool_count>99){
                broken_flag=true;
            }
            if(broken_flag) break;
        }
        if(broken_flag) break;
    }
    var broken_flag=false;
    var mappingtool_count=100;
    for(var i=0;i<map_decchip_w;i++){
        for(var j=0;j<map_decchip_h;j++){
            createMappingTool(mappingtool_count);
            canvas=document.getElementById('mappingtool_ic_'+mappingtool_count);
            ctx=canvas.getContext('2d');
            ctx.drawImage(mapping_image,i*32,j*32+128,32,32,0,0,32,32);
            mappingtool_count=mappingtool_count+100;
            if(mappingtool_count>9900){
                broken_flag=true;
            }
            if(broken_flag) break;
        }
        if(broken_flag) break;
    }
}
function doNotUpdate(id){
    noneUpdateSettingId=id;
}
function openWTL(msg){
    if(msg.indexOf('SWM=')!=-1){
        var op_msg=msg.replace(/SWM=/,'');
        displayWTL(op_msg,50,50,40,163,11);
    }else if(msg.indexOf('ERR=')!=-1){
        var op_msg=msg.replace(/ERR=/,'');
        displayWTL(op_msg,50,50,255,0,0);
    }
}
function checkActiveInputCharData(){
    if(activeInputCharData!=0){
        setModCharData(activeInputCharData);
    }
}
function setActiveInputCharData(id){
    activeInputCharData=id;
}
function removeActiveInputCharData(id){
    if(activeInputCharData==id){
        activeInputCharData=0;
    }
    setModCharData(id);
}
function setCookieSettingData(){
    var cookie_expire=new Date();
    cookie_expire.setTime(cookie_expire.getTime()+1000*3600*24*365);
    var setting_data=document.getElementById('game_music_volume').value;
    document.cookie='music_volume='+setting_data+'; expires='+cookie_expire.toUTCString();
    setting_data=document.getElementById('game_cs_setting_select').value;
    document.cookie='chat_font_size='+setting_data+'; expires='+cookie_expire.toUTCString();
    setting_data=document.getElementById('game_chat_se').value;
    document.cookie='chat_sound='+setting_data+'; expires='+cookie_expire.toUTCString();
    var setting_data=document.getElementById('chat_sound_vol').value;
    document.cookie='chat_sound_vol='+setting_data+'; expires='+cookie_expire.toUTCString();
    setting_data=document.getElementById('id_chat_color').value;
    document.cookie='chat_color='+setting_data+'; expires='+cookie_expire.toUTCString();
    if(setting_data=document.getElementById('game_bn_display').checked==true){
        document.cookie='bn_notdisplay=1; expires='+cookie_expire.toUTCString();
    }else{
        document.cookie='bn_notdisplay=0; expires='+cookie_expire.toUTCString();
    }
}
function displayWTL(msg,cutl,first,fc_r,fc_g,fc_b){
    if(fc_r==null){
        fc_r=0;
    }
    if(fc_g==null){
        fc_g=0;
    }
    if(fc_b==null){
        fc_b=0;
    }
    var nbox=document.getElementById('notice_box');
    if(cutl==first){
        nbox.innerHTML=msg;
        var rect=nbox.getBoundingClientRect();
        nbox.style.left=Math.floor((window.innerWidth-rect.width)/2)+'px';
        nbox.style.color='rgba('+fc_r+','+fc_g+','+fc_b+',1.0)';
        nbox.style.backgroundColor='rgba(255,255,255,1.0)';
        nbox.style.borderColor='rgba(170,170,170,1.0)';
    }
    if(cutl<10){
        nbox.style.color='rgba('+fc_r+','+fc_g+','+fc_b+',0.'+cutl+')';
        nbox.style.backgroundColor='rgba(255,255,255,0.'+cutl+')';
        nbox.style.borderColor='rgba(170,170,170,0.'+cutl+')';
    }
    cutl--;
    if(cutl<1){
        nbox.style.visibility='hidden';
        return true;
    }else{
        nbox.style.visibility='visible';
    }
    setTimeout(function(){
        displayWTL(msg,cutl,first,fc_r,fc_g,fc_b);
    },100);
    return true;
}
function setMappingTool(tool_num){
    document.getElementById('mappingtool_ic_'+mapping_tool_flag[1]).classList.remove('actived_mappingtool');
    mapping_tool_flag[1]=tool_num;
    document.getElementById('mappingtool_ic_'+tool_num).classList.add('actived_mappingtool');
}
// マップデータ配列の初期化
function initMapDataArray(){
    var md_array=[];
    for(var i=0;i<100;i++){
        md_array[i]=[];
        for(var j=0;j<100;j++){
            md_array[i][j]='-1';
        }
    }
    return md_array;
}
// 座標の表示
function drawCoordinate(color){
    var canvas=document.getElementById('cvs_boardnumber');
    // Draw (Resize)
    var ctx=canvas.getContext('2d');
    //tranformContext(ctx,1,dw,dh,0);
    // 背景塗り
    ctx.fillStyle="rgba("+[0,0,0,0]+")";
    ctx.beginPath();
    ctx.rect(0,0,3200,3200);
    ctx.fill();
    // 配置初期設定
    ctx.font='10px "Osaka−等幅","ＭＳ ゴシック","Consolas","Courier New","Monaco","Courier","monospace"';
    ctx.fillStyle=color;
    var str_scale=0.8; // スケール
    ctx.scale(str_scale,str_scale);
    var base_sh=ctx.measureText('Ｍ').width; // 初期サイズの文字高さ
    var base_ph=((32/str_scale)/2)+(base_sh/2)-1; // 配置初期値 y軸
    //tranformContext(ctx,str_scale,3200,3200,0);
    for(var i=0;i<100;i++){
        for(var j=0;j<100;j++){
            var cd_name=i+'-'+j;
            var base_px=((32/str_scale)-ctx.measureText(cd_name).width)/2+1; // 配置初期値 x軸
            ctx.fillText(cd_name,base_px+j*(32/str_scale),base_ph+i*(32/str_scale));
        }
    }
}
function changeMappingDataSize(scale_data_x,scale_data_y){
    var map_surface=document.getElementById('mapsurface');
    map_surface.width=scale_data_x*32;
    map_surface.height=scale_data_y*32;
}
// マップ画像の更新
function drawMapImage(d_map_da){
    if(loadedimgmapping_flag!=1){
        return false;
    }
    var difference_flag=false;
    if(change_map_data_flag!=true){
        for(var i=0;i<100;i++){
            for(var j=0;j<100;j++){
                if(map_data_array[i][j]!=d_map_da[i][j]){
                    map_data_array[i][j]=d_map_da[i][j];
                    difference_flag=true;
                }
            }
        }
    }
    if(difference_flag!=false){
        var map_surface=document.getElementById('mapsurface');
        var map_surface_width=0;
        for(var i=0;i<100;i++){
            if(map_data_array[0][i]==-1){
                break;
            }
            map_surface_width=(i+1)*32;
        }
        var map_surface_height=0;
        for(var i=0;i<100;i++){
            if(map_data_array[i][0]==-1){
                break;
            }
            map_surface_height=(i+1)*32;
        }
        map_surface.width=map_surface_width;
        map_surface.height=map_surface_height;
        var map_surface_ctx=map_surface.getContext('2d');
        map_surface_ctx.clearRect(0,0,map_surface_width,map_surface_height);
        for(var i=0;i<100;i++){
            for(var j=0;j<100;j++){
                var line_num=map_data_array[i][j]%100;
                var deco_num=Math.floor(map_data_array[i][j]/100);
                // ライン描画
                if(line_num>0){
                    map_surface_ctx.drawImage(mapping_image,
                                              Math.floor((line_num-1)/map_linechip_h)*32,
                                              ((line_num-1)%map_linechip_h)*32,
                                              32,32,j*32,i*32,32,32);
                }
                // デコレーション貼り付け
                if(deco_num>0){
                    map_surface_ctx.drawImage(mapping_image,
                                              Math.floor((deco_num-1)/map_decchip_h)*32,
                                              ((deco_num-1)%map_decchip_h+4)*32,
                                              32,32,j*32,i*32,32,32);
                
                }
            }
        }
    }
    return true;
}
// マップ画像の一部更新
function changeMapChipImage(tool_num,cmap_x,cmap_y){
    var map_surface=document.getElementById('mapsurface');
    var map_surface_ctx=map_surface.getContext('2d');
    map_surface_ctx.clearRect(cmap_x*32,cmap_y*32,32,32);
    var line_num=tool_num%100;
    var deco_num=Math.floor(tool_num/100);
    if(deco_num>0){
        if(map_data_array[cmap_y][cmap_x]!=null){
            var line_num=map_data_array[cmap_y][cmap_x]%100;
        }
    }
    // ライン描画
    if(line_num>0){
        map_surface_ctx.drawImage(mapping_image,
                                  Math.floor((line_num-1)/map_linechip_h)*32,
                                  ((line_num-1)%map_linechip_h)*32,
                                  32,32,cmap_x*32,cmap_y*32,32,32);
    }
    // デコレーション貼り付け
    if(deco_num>0){
        map_surface_ctx.drawImage(mapping_image,
                                  Math.floor((deco_num-1)/map_decchip_h)*32,
                                  ((deco_num-1)%map_decchip_h+4)*32,
                                  32,32,cmap_x*32,cmap_y*32,32,32);
    }
    return true;
}
// マップチップの更新
function updateMapChipImg(resHTTP){
    var memberGameMapChip=resHTTP.getElementsByTagName('game_mapchip');
    if(memberGameMapChip[0]!=null){
        var str_mapchip_image=memberGameMapChip[0].textContent;
    }else{
        var str_mapchip_image=CONST_DEFAULT_URL_MAPCHIP;
    }
    if(mapping_image.src!=str_mapchip_image){
        var delete_mappingtool_array=[];
        mappingtoolElements=document.getElementsByClassName('mappingtool');
        for(var i=0;i<(mappingtoolElements.length);i++){
            delete_mappingtool_array.push(mappingtoolElements[i].id);
        }
        deleteElements(delete_mappingtool_array);
        loadedimgmapping_flag=0;
        var map_surface=document.getElementById('mapsurface');
        var map_surface_ctx=map_surface.getContext('2d');
        map_surface_ctx.clearRect(0,0,map_surface.width,map_surface.height);
        map_data_array=initMapDataArray();
        mapping_image.src=str_mapchip_image;
    }
    return true;
}
// コマ移動
function placeChessboard($event){
    if(mouse.id==null){
        return false;
    }else if(mouse.id.indexOf('chessmanNtB')!=-1){
        var date=new Date();
        var setobj_id=mouse.id.replace(/chessmanNtB([0-9]+$)/,'chessmanOtB$1')+'_'+date.getTime();
    }else if(mouse.id.indexOf('rangeNtB')!=-1){
        var date=new Date();
        var setobj_id=mouse.id.replace(/rangeNtB([0-9]+$)/,'rangeOtB$1')+'_'+date.getTime();
    }else if(mouse.id.indexOf('chesscharNtB')!=-1){
        var date=new Date();
        var setobj_id=mouse.id.replace(/chesscharNtB([0-9a-zA-Z]+$)/,'chesscharOtB$1')+'_'+date.getTime();
    }else if(mouse.id.indexOf('chessmanOtB')!=-1){
        var setobj_id=mouse.id;
    }else if(mouse.id.indexOf('rangeOtB')!=-1){
        var setobj_id=mouse.id;
    }else if(mouse.id.indexOf('chesscharOtB')!=-1){
        var setobj_id=mouse.id;
    }else{
        return false;
    }
    var intSetImageX=roundUpDecimal((mouse.ox-mouse.rx/2),16);
    var intSetImageY=roundUpDecimal((mouse.oy-mouse.ry/2),16);
    var max_num=0;
    var chessman_key=-1;
    for(var i=0;i<chessman_array.length;i++){
        if(typeof(chessman_array[i][9])!="undefined"){
            if(chessman_array[i][9]>max_num){
                max_num=parseInt(chessman_array[i][9]);
            }
        }
        if(chessman_array[i][0]==setobj_id){
            chessman_key=i;
        }
    }
    max_num=max_num+1;
    if(chessman_key==-1){
        chessman_array.push([setobj_id,mouse.go,intSetImageY,intSetImageX,mouse.rx,mouse.ry,'img',mouse.at,0,max_num]);
    }else{
        chessman_array[chessman_key][2]=intSetImageY;
        chessman_array[chessman_key][3]=intSetImageX;
    }
    drawAllChessman(chessman_array);
    saveChessmanData();
    return true;
}
// コマ削除
function dropChessman($event){
    var chessman_key=-1;
    for(var i=0;i<chessman_array.length;i++){
        if(chessman_array[i][0]==mouse.id){
            chessman_key=i;
        }
    }
    if(chessman_key==-1){
        return false;
    }else{
        chessman_array.splice(chessman_key,1);
    }
    if((mouse.id.indexOf('chessmanOtB')!=-1)||
       (mouse.id.indexOf('rangeOtB')!=-1)||
       (mouse.id.indexOf('chesscharOtB')!=-1)){
        if(document.getElementById(mouse.id+'_box')){
            var deleteElement=document.getElementById(mouse.id+'_box');
            deleteElement.parentNode.removeChild(deleteElement);
        }
    }
    saveChessmanData();
}
// コマNo.カウント
function roundUpDecimal(number,decimal){
    var division_number=(number+decimal/2)%decimal;
    var divisible_number=((number+decimal/2)-division_number);
    return divisible_number;
}
function setModSettingData(eid){
    //document.getElementById(eid).disabled=true;
    if(eid=='game_memo'){
        var sd={game_memo:document.getElementById('game_memo').value,
				game_memo_key:active_memo_key};
    }else if(eid=='add_memo'){
        var sd2={event_memo:'add'};
		var unlock_id='game_memo_add_buttom';
    }else if(eid=='delete_memo'){
        var sd2={event_memo:'delete',
				 game_memo_key:active_memo_key};
		var unlock_id='game_memo_detele_buttom';
		pushMemoTab(1);
    }else if(eid=='game_memo_p'){
        var sd2={game_memo:document.getElementById('game_memo').value,
				 game_memo_key:active_memo_key,
                 puw_limit_time:document.getElementById('puw_limit_time').value,
                 popup_flag:active_memo_key};
		var unlock_id='game_memo_a_buttom';
    }else if(eid=='game_boardwidth'){
        var syncboardsize_flag=0;
        if(document.getElementById('game_syncboardsize').checked==true){
            syncboardsize_flag=1;
        }
        var sd={game_boardwidth:document.getElementById(eid).value,
                game_syncboardsize:syncboardsize_flag};
    }else if(eid=='game_boardheight'){
        var syncboardsize_flag=0;
        if(document.getElementById('game_syncboardsize').checked==true){
            syncboardsize_flag=1;
        }
        var sd={game_boardheight:document.getElementById(eid).value,
                game_syncboardsize:syncboardsize_flag};
    }else if(eid=='game_syncboardsize'){
        var syncboardsize_flag=0;
        if(document.getElementById('game_syncboardsize').checked==true){
            syncboardsize_flag=1;
        }
        var sd={game_syncboardsize:syncboardsize_flag};
    }else if(eid=='game_grid'){
        var sd={game_grid:document.getElementById(eid).value};
    }else if(eid=='game_backimage'){
        var sd={game_backimage:document.getElementById(eid).value};
    }else if(eid=='game_imagestrength'){
        var sd={game_imagestrength:document.getElementById(eid).value};
    }else if(eid=='game_dicebot'){
        var sd={game_dicebot:document.getElementById(eid).value};
    }else if(eid=='game_music_state'){
        var sd={game_music:document.getElementById('game_music').value,
                game_music_state:document.getElementById(eid).value};
    }else if(eid=='game_cardset1'){
        var sd={game_cardset1:document.getElementById(eid).value};
    }else if(eid=='game_cardset2'){
        var sd={game_cardset2:document.getElementById(eid).value};
    }else if(eid=='game_cardset3'){
        var sd={game_cardset3:document.getElementById(eid).value};
    }else if(eid=='game_cardset4'){
        var sd={game_cardset4:document.getElementById(eid).value};
    }else if(eid=='game_mapchip'){
        var sd={game_mapchip:document.getElementById(eid).value};
    }
    if(sd!=null){
        sendSettingData(sd);
    }else if(sd2!=null){
        sd2.xml=CONST_ROOM_ID;
        sd2.principal=CONST_PRINCIPAL_ID;
        sd2.nick_name=CONST_NICKNAME;
        $.post(CONST_URL_ROOT+'exe/putsettingdata.php',sd2,function(data){
            openWTL(data);
			if(unlock_id){
				document.getElementById(unlock_id).disabled=false;
            }
			noneUpdateSettingId='';
        },'html');
    }
}
function sendSettingData(sd){
    sd.xml=CONST_ROOM_ID;
    sd.principal=CONST_PRINCIPAL_ID;
    sd.nick_name=CONST_NICKNAME;
    $.post(CONST_URL_ROOT+'exe/putsettingdata.php',sd,function(data){
        openWTL(data);
        noneUpdateSettingId='';
    },'html');
}
function setModCharData(id){
    $.post(CONST_URL_ROOT+'exe/modcharacter.php',{
        xml:CONST_ROOM_ID,
        principal:CONST_PRINCIPAL_ID,
        nick_name:CONST_NICKNAME,
        char_id:id,
        char_hp:document.getElementById('ch_'+id).value,
        char_mhp:document.getElementById('cmh_'+id).value,
        char_mp:document.getElementById('cm_'+id).value,
        char_mmp:document.getElementById('cmm_'+id).value,
        char_memo:document.getElementById('cme_'+id).value,
    },function(data){
        openWTL(data);
    },'html');
}
function setCookiePriMemo(memo_key){
    var cookie_expire=new Date();
    cookie_expire.setTime(cookie_expire.getTime()+1000*3600*24*1);
    var setting_data=document.getElementById('prin_memo'+memo_key).value;
    setting_data=encodeURIComponent(setting_data);
    document.cookie='pri_memo'+memo_key+'_'+CONST_ROOM_ID+'='+setting_data+'; expires='+cookie_expire.toUTCString();
}
// キャラ一覧からキャラ削除
function delFromCharList(char_id,char_name){
    var caution_comment=char_name+'をキャラ一覧から消去します。'+
                        "\n\n"+
                        '本当によろしいですか？';
    if(confirm(caution_comment)){
        $.post(CONST_URL_ROOT+'exe/setcharacterlist.php',{
            room_id:CONST_ROOM_ID,
            principal_id:CONST_PRINCIPAL_ID,
            nick_name:CONST_NICKNAME,
            flag:0,
            char_id:char_id,
            char_name:char_name,
        },function(data){
            openWTL(data);
        },'html');
    }else{
        /* 何もしない */
    }
}
function clearAllMapping(){
    var caution_comment='マッピングを全て消去します。'+"\n\n"+'本当によろしいですか？';
    if(confirm(caution_comment)){
        change_map_data_flag=true;
        var bh=parseInt(document.getElementById("game_boardheight").value);
        var bw=parseInt(document.getElementById("game_boardwidth").value);
        for(var i=0;i<bh;i++){
            for(var j=0;j<bw;j++){
                map_data_array[i][j]=0;
            }
        }
        var map_surface=document.getElementById('mapsurface');
        var map_surface_ctx=map_surface.getContext('2d');
        map_surface_ctx.clearRect(0,0,bw*32,bh*32);
        $.post(CONST_URL_ROOT+'exe/putsettingdata.php',{
            xml:CONST_ROOM_ID,
            principal:CONST_PRINCIPAL_ID,
            nick_name:CONST_NICKNAME,
            game_mapping:combineMappingData(map_data_array,bw,bh),
        },function(data){
            openWTL(data);
            change_map_data_flag=false;
        },'html');
    }else{
        /* 何もしない */
    }
}
function putChessmanReset(num){
    var caution_comment = ['コマとマーキングを全て消去します。'+"\n\n"+'本当によろしいですか？',
                           'コマと全て消去します。'+"\n"+'（マーキングは消去されません。）'+"\n\n"+'本当によろしいですか？',
                           'マーキングを全て消去します。'+"\n"+'（コマは消去されません。）'+"\n\n"+'本当によろしいですか？'];
    if(confirm(caution_comment[num])){
        $.post(CONST_URL_ROOT+'exe/putresetchessman.php',{
            xml:CONST_ROOM_ID,
            principal:CONST_PRINCIPAL_ID,
            nick_name:CONST_NICKNAME,
            type:num,
        },function(data){
            openWTL(data);
            noneUpdateSettingId='';
        },'html');
    }else{
        /* 何もしない */
    }
}
function pushDiceRoll(){
    document.getElementById('id_roll_button').disabled=true;
    document.getElementById('id_dice_count_number').disabled=true;
    document.getElementById('id_dice_surface').disabled=true;
    $.post(CONST_URL_ROOT+'exe/diceroll.php',{
        chat_color:document.getElementById('id_chat_color').value,
        call_name:document.getElementById('id_call_name').value,
        dcn:document.getElementById('id_dice_count_number').value,
        ds:document.getElementById('id_dice_surface').value,
        principal:CONST_PRINCIPAL_ID,
        nick_name:CONST_NICKNAME,
        xml:CONST_ROOM_ID,
    },function(data){
        openWTL(data);
        document.getElementById('id_roll_button').disabled=false;
        document.getElementById('id_dice_count_number').disabled=false;
        document.getElementById('id_dice_surface').disabled=false;
    },'html');
}
function pushStartVoiceChat(){
    $.post(CONST_URL_ROOT+'exe/putsysmsg.php',{
        principal:CONST_PRINCIPAL_ID,
        nick_name:CONST_NICKNAME,
        system_msg:'1',
        xml:CONST_ROOM_ID,
    },function(data){
        openWTL(data);
    },'html');
    window.open('https://discord.gg/'+CONST_VOICE_CODE);
}
// 変更マッピングデータの送信
function sendChangeMappingData(){
    if(change_map_data_flag==true){
        var bw=parseInt(document.getElementById("game_boardwidth").value);
        var bh=parseInt(document.getElementById("game_boardheight").value);
        change_map_data_flag=false;
        $.post(CONST_URL_ROOT+'exe/putsettingdata.php',{
            xml:CONST_ROOM_ID,
            principal:CONST_PRINCIPAL_ID,
            nick_name:CONST_NICKNAME,
            game_mapping:combineMappingData(map_data_array,bw,bh),
        },function(data){
            openWTL(data);
        },'html');
    }
}
// コマの保存
function saveChessmanData(){
    var resultOnMap = '';
    for(var i=0;i<chessman_array.length;i++){
        if(i!=0){
            resultOnMap+='^';
        }
        if((chessman_array[i][1]!='undefined')||
           (chessman_array[i][2]!='NaN')||
           (chessman_array[i][3]!='NaN')||
           (chessman_array[i][4]!='undefined')||
           (chessman_array[i][5]!='undefined')||
           (chessman_array[i][1]!='undefined')||
           (chessman_array[i][7]!='undefined')){
            resultOnMap+=chessman_array[i][0]+'|'
                        +chessman_array[i][1]+'|'
                        +chessman_array[i][2]+'|'
                        +chessman_array[i][3]+'|'
                        +chessman_array[i][4]+'|'
                        +chessman_array[i][5]+'|'
                        +chessman_array[i][6]+'|'
                        +chessman_array[i][7]+'|'
                        +chessman_array[i][8]+'|'
                        +chessman_array[i][9];
        }
    }
    noneUpdateBoardTime=1;
    $.post(CONST_URL_ROOT+'exe/putpositiondata.php',{
        principal:CONST_PRINCIPAL_ID,
        nick_name:CONST_NICKNAME,
        xml:CONST_ROOM_ID,
        map_data:resultOnMap,
    },function(data){
        openWTL(data);
    },'html');
}
function putCallName(){
    var call_name_id=document.getElementById('id_call_name_st').value;
    var call_name=call_name_id;
    if(obj_character_list.charlist){
        for(var key in obj_character_list.charlist){
            if(obj_character_list.charlist[key].id==call_name_id){
                call_name=obj_character_list.charlist[key].name;
                break;
            }
        }
    }
    if(call_name=='nick_name'){
        call_name=CONST_NICKNAME;
    }else if(call_name=='gm_nick_name'){
        call_name='GM／'+CONST_NICKNAME;
    }else if(call_name=='principal_id'){
        call_name=CONST_PRINCIPAL_ID;
    }else if(call_name=='gm_principal_id'){
        call_name='GM／'+CONST_PRINCIPAL_ID;
    }
    document.getElementById('id_call_name').value=call_name;
    document.getElementById('id_call_name_id').value=call_name_id;
    if(CONST_ROOM_TYPE=='pc'){
        if((typeof changeSelectStandImageCNID)=='function'){
            changeSelectStandImageCNID(call_name_id);
        }
        setMacroList(call_name_id);
    }
    getCharacterData(call_name_id,-1);
}
function getEventChessmanKey(){
	var chessman_key=-1;
	for(var i=0;i<chessman_array.length;i++){
		if(chessman_array[i][0]==event_CRM_flag[1]){
			chessman_key=i;
		}
	}
	return chessman_key;
}
function setMemoDatas(nodeMemos){
	var id,txt,flag,limit,check,img;
	var checkedKey=[];
	for(var key=0;key<nodeMemos.length;key++){
		if(id=nodeMemos[key].getAttribute('id')){
			txt='';img='';flag=0;limit=30;
			checkedKey.push(id);
			for(var i=0;i<nodeMemos[key].childNodes.length;i++){
				if(nodeMemos[key].childNodes[i].nodeName=='txt'){
					txt=nodeMemos[key].childNodes[i].textContent;
				}else if(nodeMemos[key].childNodes[i].nodeName=='flag'){
					flag=Number(nodeMemos[key].childNodes[i].textContent);
				}else if(nodeMemos[key].childNodes[i].nodeName=='limit'){
					limit=Number(nodeMemos[key].childNodes[i].textContent);
				}
			}
			img=searchImgUrl(txt);
			if(img===false){
				img='';
			}
			var now_date=new Date();
			var now_time=Math.floor((now_date.getTime()+time_revised_value)/1000);
			if(listMemoData[id]){
				if((listMemoData[id]['txt']!=txt)||
				   (listMemoData[id]['img']!=img)){
					   
					listMemoData[id]['txt']=txt;
					listMemoData[id]['img']=img;
					listMemoData[id]['udt']=now_time;
				}
				listMemoData[id]['flag']=flag;
				listMemoData[id]['limit']=limit;
			}else{
				listMemoData[id]={
					'txt':txt,
					'img':img,
					'flag':flag,
					'limit':limit,
					'last':now_time,
					'puw':0,
					'eid':'',
					'udt':now_time
				};
				createMemoPreview(id);
				createMemoTag(id);
			}
		}
	}
	for(var key in listMemoData){
		check=false;
		for(var i=0;i<checkedKey.length;i++){
			if(key==checkedKey[i]){
				check=true;
				break;
			}
		}
		if(check==false){
			delete listMemoData[key];
			if(document.getElementById('game_memo'+key+'_tab')){
				deleteDom('game_memo'+key+'_tab');
			}
			if(document.getElementById('puw_memo_'+key)){
				deleteDom('puw_memo_'+key);
			}
			if(CONST_ROOM_TYPE=='pc'){
				for(var i=0;i<puw_no_list.length;i++){
					if(puw_no_list[i]=='memo_'+key){
						delete puw_no_list[i];
						break;
					}
				}
			}
		}
	}
}
function createMemoPreview(id){
	if(CONST_ROOM_TYPE=='sp'){
		return true;
	}
	if(listMemoData[id]['puw']==0){
		// ウィンドウ作成
		var style_top=42;
		var style_left=258+32*id;
		var fixDefaultWidth=getFixDefaultWidth();
		if(fixDefaultWidth>1){
			style_left=style_left+fixDefaultWidth;
		}
		var html_v=
			'<div class="puw_head" onMousedown="sortPUWzIndex(\'memo_'+id+'\')">'+
				'<table><tr>'+
					'<td id="puw_memo_'+id+'_head_title" class="text_left">共有メモ'+id+'</td>'+
					'<td class="text_right"><span class="puw_close" onClick="closePopupWindow(\'memo_'+id+'\');">&nbsp;</span></td>'+
				'</tr></table>'+
			'</div>'+
			'<div class="puw_body">'+
				'<div id="puw_inbody_memo_'+id+'" class="puw_inbody ctabbody" style="height:302px;">'+
					'<pre id="ex_space_memo_'+id+'" style="font-size:14px;white-space:pre-wrap;word-wrap:break-word;margin-top:10px;">'+
				'</div>'+
			'</div>';
		var new_element=document.createElement('div');
		new_element.setAttribute('id','puw_memo_'+id);
		new_element.setAttribute('style','display:none;width:281px;height:340px;top:'+style_top+'px;left:'+style_left+'px;');
		new_element.className='popup_window';
		new_element.innerHTML=html_v;
		document.getElementById('wdf').appendChild(new_element);
		// イベント付与
		$("#puw_memo_"+id).draggable({handle:"div.puw_head",cancel:"span.puw_close"});
		$("#puw_memo_"+id).resizable({minHeight:120,minWidth:140,alsoResize:"#puw_inbody_memo_"+id});
		// リスト追加
		listMemoData[id]['puw']=1;
		var add_flag=true;
		for(var i=0;i<puw_no_list.length;i++){
			if(puw_no_list[i]=='memo_'+id){
				add_flag=false;
				break;
			}
		}
		if(add_flag===true){
			puw_no_list.push('memo_'+id);
		}
		return true;
	}
	return false;
}
function pushMemoTab(tab_key){
	if(listMemoData[tab_key]){
		active_memo_key=tab_key;
		if(noneUpdateSettingId!='game_memo'){
			document.getElementById('game_memo').value=listMemoData[tab_key]['txt'];
			document.getElementById('game_memo_img').src=listMemoData[tab_key]['img'];
			if(listMemoData[tab_key]['img']!=''){
				document.getElementById('game_memo_img').style.visibility='visible';
			}else{
				document.getElementById('game_memo_img').style.visibility='hidden';
			}
		}
		for(var key in listMemoData){
			var node=document.getElementById('game_memo'+key+'_tab');
			if(node){
				if(key==tab_key){
					node.classList.add("lt_t_tab_actived");
				}else{
					node.classList.remove("lt_t_tab_actived");
				}
			}
		}
		if(tab_key<6){
			document.getElementById('game_memo_detele_buttom').style.visibility='hidden';
		}else{
			document.getElementById('game_memo_detele_buttom').style.visibility='visible';
		}
	}
}
function createMemoTag(id){
	// ウィンドウ作成
	var new_element=document.createElement('span');
	new_element.setAttribute('id','game_memo'+id+'_tab');
	if(active_memo_key==id){
		new_element.className='lt_t_tab_actived';
	}
	new_element.innerHTML='メモ'+id;
	document.getElementById('game_memo_tags').appendChild(new_element);
	// イベント付与
    new_element.onclick=function(){
		pushMemoTab(id);
	};
}
function updatePopUpWindow(){
	for(var key in listMemoData){
		if(listMemoData[key]['udt']>listMemoData[key]['puw']){
			var exElement=document.getElementById('ex_space_memo_'+key);
			if(exElement){
				if(listMemoData[key]['img']==''){
					exElement.innerHTML=listMemoData[key]['txt'];
				}else{
					exElement.innerHTML='<img src="'+listMemoData[key]['img']+'" style="width:100%;" border="0">';
				}
				listMemoData[key]['puw']=listMemoData[key]['udt'];
			}
		}
	}
}
function getFixDefaultWidth(){
	var width=window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth;
	return Math.floor((width-1260)/2);
}
/*////////////////////////////////////////////////////////////////
after load process
////////////////////////////////////////////////////////////////*/
// smartphone - hover
(function(){
    var tapClass = "";
    var hoverClass = "";
    var Hover = window.Hover = function (ele) {
        return new Hover.fn.init(ele);
    };
    Hover.fn = {
        //Hover Instance
        init : function (ele) {
            this.prop = ele;
        },
        bind : function (_hoverClass, _tapClass) {
            hoverClass = _hoverClass;
            tapClass = _tapClass;
            $(window).bind("touchstart", function(event) {
                var target = event.target || window.target;
                var bindElement = null;
                if (target.tagName == "A" || $(target).hasClass(tapClass)) {
                    bindElement = $(target);
                } else if ($(target).parents("a").length > 0) {
                    bindElement = $(target).parents("a");
                } else if ($(target).parents("." + tapClass).length > 0) {
                    bindElement = $(target).parents("." + tapClass);
                }
                if (bindElement != null) {
                    Hover().touchstartHoverElement(bindElement);
                }
            });
        }, 
        touchstartHoverElement : function (bindElement) {
            bindElement.addClass(hoverClass);
            bindElement.unbind("touchmove", Hover().touchmoveHoverElement);
            bindElement.bind("touchmove", Hover().touchmoveHoverElement);
            bindElement.unbind("touchend", Hover().touchendHoverElement);
            bindElement.bind("touchend", Hover().touchendHoverElement);
        }, 
        touchmoveHoverElement : function (event) {
            $(this).removeClass(hoverClass);
        }, 
        touchendHoverElement : function (event) {
            $(this).removeClass(hoverClass);
        }
    }
    Hover.fn.init.prototype = Hover.fn;
    Hover().bind("hover", "tap");
    }
)();
// smartphone - page-top button
$(function(){
    var topBtn = $('#page-top');    
    topBtn.hide();
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100){
            topBtn.fadeIn();
        } else {
            topBtn.fadeOut();
        }
    });
    topBtn.click(function(){
        $('body,html').animate({
            scrollTop: 0
        }, 500);
        return false;
    });
});