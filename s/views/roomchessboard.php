<div id="underboard">
    <div id="boardsurface">
        <img id="boardimage" src="" width="<?=$game_board_bgimg_width;?>" height="<?=$game_board_bgimg_height;?>" border="0" style="display:none;" />
    </div>
    <div id="boardnumber" style="width:<?=$game_boardwidth*32;?>px;height:<?=$game_boardheight*32;?>px;">
        <canvas id="cvs_boardnumber" width="3200" height="3200" style="position:absolute;top:0;left:0;"></canvas>
    </div>
    <div id="boardgrid" class="c_bg_a" style="width:<?=$game_boardwidth*32;?>px;height:<?=$game_boardheight*32;?>px;z-index:1;"></div>
    <canvas id="mapsurface" width="3200" height="3200" style="position:absolute;top:0;left:0;z-index:2;"></canvas>
    <div
        id="chessboard"
        draggable="false"
        ondrop="placeChessboard(event);event.preventDefault();"
        ondragenter="event.preventDefault();"
        ondragover="event.preventDefault();"
        style="width:<?=$game_boardwidth*32;?>px;height:<?=$game_boardheight*32;?>px;z-index:3;">
    </div>
</div>
<div id="chara_right_menu" style="position:absolute;width:100px;display:none;z-index:100;">
	<ul id="chara_menu_list" class="right_menu">
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
<span id="text_ruler" class="cm_pnumb" style="visibility:hidden;"></span>
<script>
/*////////////////////////////////////////////////////////////////
variable
////////////////////////////////////////////////////////////////*/
var event_CRM_flag=[false,''];
var mapping_tool_flag=[false,0];
var change_map_data_array=[]; // Y,X,N
var change_map_data_flag=false;
var map_data_array=initMapDataArray(); // Y,X
var long_touch_timer=''; // ロングタッチ処理
mapping_image.src='about:blank'; // 画像ロード処理
/*////////////////////////////////////////////////////////////////
anonymous function
////////////////////////////////////////////////////////////////*/
// コマ用 右クリックメニュー オープン処理
openRightMenu=function(e){
    var eventinit=e.target;
    if((eventinit.id.indexOf('chessman')!=-1)||
       (eventinit.id.indexOf('chesschar')!=-1)||
       (eventinit.id.indexOf('range')!=-1)){
        if(event_CRM_flag[0]==false){
            e.preventDefault();
            if(eventinit.getBoundingClientRect().width>326){
                document.getElementById('crm_expansion').style.display='none';
            }else{
                document.getElementById('crm_expansion').style.display='block';
            }
            if(eventinit.getBoundingClientRect().width<70){
                document.getElementById('crm_shrink').style.display='none';
            }else{
                document.getElementById('crm_shrink').style.display='block';
            }
            document.getElementById('chara_menu_list').style.display='block';
            document.getElementById('chara_rename_box').style.display='none';
            document.getElementById('chara_right_menu').style.visibility='hidden';
            document.getElementById('chara_right_menu').style.display='block';
            document.getElementById('chara_right_menu').style.width='100px';
            var rect=document.getElementById('chessboard').getBoundingClientRect();
            var boardscrect=document.getElementById('puw_inbody_07').getBoundingClientRect();
            var menurect=document.getElementById('chara_right_menu').getBoundingClientRect();
            var mouseX=e.clientX;
            var mouseY=e.clientY;
            var positionX=rect.left;
            var positionY=rect.top;
            var offsetX=mouseX-positionX;
            if(offsetX>((boardscrect.width+document.getElementById('puw_inbody_07').scrollLeft)-(menurect.width+20))){
                offsetX=((boardscrect.width+document.getElementById('puw_inbody_07').scrollLeft)-(menurect.width+20));
                if(offsetX<0){
                    offsetX=0;
                }
            }
            var offsetY=mouseY-positionY;
            if(offsetY>((boardscrect.height+document.getElementById('puw_inbody_07').scrollTop)-(menurect.height+20))){
                offsetY=((boardscrect.height+document.getElementById('puw_inbody_07').scrollTop)-(menurect.height+20));
                if(offsetY<0){
                    offsetY=0;
                }
            }
            document.getElementById('chara_right_menu').style.left=offsetX+'px';
            document.getElementById('chara_right_menu').style.top=offsetY+'px';
            document.getElementById('chara_right_menu').style.visibility='visible';
            event_CRM_flag[0]=true;
            event_CRM_flag[1]=eventinit.id;
        }
    }
}
// コマ用 右クリックメニュー イベント処理
eventRightMenu=function(e){
    var eventinit=e.target;
    if(event_CRM_flag[0]==true){
		var chessman_key=getEventChessmanKey();
        if(eventinit.id=='crm_rroll'){ //右回転
            if(e.button==0){
                document.getElementById('chara_right_menu').style.display='none';
                if(chessman_key==-1){
                    return false;
                }
				chessman_array[chessman_key][8]=parseInt(chessman_array[chessman_key][8])+90;
				if(chessman_array[chessman_key][8]>=360){
					chessman_array[chessman_key][8]=0;
				}
                drawAllChessman(chessman_array);
                saveChessmanData();
            }
        }else if(eventinit.id=='crm_lroll'){ //左回転
            if(e.button==0){
                document.getElementById('chara_right_menu').style.display='none';
                if(chessman_key==-1){
                    return false;
                }
				chessman_array[chessman_key][8]=parseInt(chessman_array[chessman_key][8])-90;
				if(chessman_array[chessman_key][8]<0){
					chessman_array[chessman_key][8]=chessman_array[chessman_key][8]+360;
				}
                drawAllChessman(chessman_array);
                saveChessmanData();
            }
        }else if(eventinit.id=='crm_expansion'){ //拡大
            if(e.button==0){
                document.getElementById('chara_right_menu').style.display='none';
                if(chessman_key==-1){
                    return false;
                }
				chessman_array[chessman_key][2]=parseInt(chessman_array[chessman_key][2])-16;
				chessman_array[chessman_key][3]=parseInt(chessman_array[chessman_key][3])-16;
				chessman_array[chessman_key][4]=parseInt(chessman_array[chessman_key][4])+32;
				chessman_array[chessman_key][5]=parseInt(chessman_array[chessman_key][5])+32;
                drawAllChessman(chessman_array);
                saveChessmanData();
            }
        }else if(eventinit.id=='crm_shrink'){ //縮小
            if(e.button==0){
                document.getElementById('chara_right_menu').style.display='none';
                if(chessman_key==-1){
                    return false;
                }
				chessman_array[chessman_key][2]=parseInt(chessman_array[chessman_key][2])+16;
				chessman_array[chessman_key][3]=parseInt(chessman_array[chessman_key][3])+16;
				chessman_array[chessman_key][4]=parseInt(chessman_array[chessman_key][4])-32;
				chessman_array[chessman_key][5]=parseInt(chessman_array[chessman_key][5])-32;
                drawAllChessman(chessman_array);
                saveChessmanData();
            }
        }else if(eventinit.id=='crm_delete'){ //削除
            if(e.button==0){
                document.getElementById('chara_right_menu').style.display='none';
                mouse.id=event_CRM_flag[1];
                dropChessman();
                event_CRM_flag[0]=false;
                event_CRM_flag[1]='';
            }
        }else if(eventinit.id=='crm_fore'){ //最前面
            if(e.button==0){
                document.getElementById('chara_right_menu').style.display='none';
                if(chessman_key==-1){
                    return false;
                }
				var target_column=chessman_array.splice(chessman_key,1);
				chessman_array.push(target_column[0]);
                drawAllChessman(chessman_array);
                saveChessmanData();
            }
        }else if(eventinit.id=='crm_back'){ //最背面
            if(e.button==0){
                document.getElementById('chara_right_menu').style.display='none';
                if(chessman_key==-1){
                    return false;
                }
				var target_column=chessman_array.splice(chessman_key,1);
				chessman_array.unshift(target_column[0]);
                drawAllChessman(chessman_array);
                saveChessmanData();
            }
        }else if(eventinit.id=='crm_rename'){ //名称変更
            if(e.button==0){
				openRenameBox();
				document.getElementById('rename_box_input').focus();
            }
        }else if(eventinit.id=='rename_box_input'){ //名称変更インプットにフォーカス
        }else{
            e.preventDefault();
            document.getElementById('chara_right_menu').style.display='none';
			document.getElementById('chara_rename_box').style.display='none';
            event_CRM_flag[0]=false;
            event_CRM_flag[1]='';
        }
    }
}
eventCickDownChessBoard=function(e){
    var eventinit=document.getElementById("mapsurface");
    var d_map_da=[]; // Y,X
    for(var i=0;i<100;i++){
        d_map_da[i]=[];
        for(var j=0;j<100;j++){
            d_map_da[i][j]=map_data_array[i][j];
        }
    }
    if(mapping_tool_flag[0]==true){
        var rect=eventinit.getBoundingClientRect();
        var ox=e.clientX-rect.left;
        var oy=e.clientY-rect.top;
        var result_x=Math.floor(ox/32);
        var result_y=Math.floor(oy/32);
        var line_num=mapping_tool_flag[1]%100;
        var deco_num=Math.floor(mapping_tool_flag[1]/100);
        if(map_data_array[result_y][result_x]!=null){
            var t_line_num=map_data_array[result_y][result_x]%100;
            var t_deco_num=Math.floor(map_data_array[result_y][result_x]/100);
            if(deco_num!=0){
                change_map_data_flag=true;
                if(deco_num!=t_deco_num){
                    map_data_array[result_y][result_x]=t_line_num+deco_num*100;
                    changeMapChipImage(mapping_tool_flag[1],result_x,result_y);
                }else{
                    map_data_array[result_y][result_x]=t_line_num;
                    changeMapChipImage(t_line_num,result_x,result_y);
                }
            }else if(line_num!=0){
                change_map_data_flag=true;
                map_data_array[result_y][result_x]=line_num;
                changeMapChipImage(line_num,result_x,result_y);
            }else{
                change_map_data_flag=true;
                map_data_array[result_y][result_x]=0;
                changeMapChipImage(0,result_x,result_y);
            }
        }
    }
}
/*////////////////////////////////////////////////////////////////
function
////////////////////////////////////////////////////////////////*/
function pushBoardLabelTab(tab_key){
    if(tab_key==0){
        document.getElementById('bl_tab_0').classList.add("bl_buttom_actived");
        document.getElementById('bl_tab_1').classList.remove("bl_buttom_actived");
        document.getElementById('boardgrid').classList.add("c_bg_a");
        document.getElementById('boardgrid').classList.remove("c_bg_b");
        document.getElementById('boardgrid').style.zIndex=1;
        document.getElementById('mapsurface').style.zIndex=2;
        document.getElementById('chessboard').style.zIndex=3;
        document.getElementById('trashbox').style.display='inline-block';
        document.getElementById('stand_img_box').style.display='block';
        var elementPopUpWindow=document.getElementById('puw_19');
        if(elementPopUpWindow.style.display!='none'){
            pushMainMenu(19);
        }
        mapping_tool_flag[0]=false;
    }else if(tab_key==1){
        document.getElementById('bl_tab_0').classList.remove("bl_buttom_actived");
        document.getElementById('bl_tab_1').classList.add("bl_buttom_actived");
        document.getElementById('boardgrid').classList.remove("c_bg_a");
        document.getElementById('boardgrid').classList.add("c_bg_b");
        document.getElementById('boardgrid').style.zIndex=2;
        document.getElementById('mapsurface').style.zIndex=3;
        document.getElementById('chessboard').style.zIndex=1;
        document.getElementById('trashbox').style.display='none';
        document.getElementById('stand_img_box').style.display='none';
        var elementPopUpWindow=document.getElementById('puw_19');
        if(elementPopUpWindow.style.display=='none'){
            pushMainMenu(19);
        }
        mapping_tool_flag[0]=true;
    }
}
// コマ用 名称変更ボックス オープン処理
function openRenameBox(){
	var chessman_key=getEventChessmanKey();
	var chessman_name='';
	if(chessman_key!=-1){
		chessman_name=chessman_array[chessman_key][7];
	}
	document.getElementById('rename_box_input').value=chessman_name;
	document.getElementById('chara_right_menu').style.width='200px';
	document.getElementById('chara_menu_list').style.display='none';
	document.getElementById('chara_rename_box').style.display='block';
	return true;
}
// コマ用 名称変更処理
function changeChessmanName(e){
	if(e!=null){ // Mozilla
		keycode=e.which;
	}else{ // IE
		keycode=event.keyCode;
	}
	if(keycode==13){
		document.getElementById('chara_right_menu').style.display='none';
		var chessman_key=getEventChessmanKey();
		if(chessman_key==-1){
			return false;
		}
		chessman_array[chessman_key][7]=document.getElementById('rename_box_input').value;
		drawAllChessman(chessman_array);
		saveChessmanData();
		return true;
	}
	return false;
}
/*////////////////////////////////////////////////////////////////
events
////////////////////////////////////////////////////////////////*/
drag_image.onload=function(){
    setTimeout(function(){
        document.getElementById('loading_box').style.display='none';
    },1000);
};
mapping_image.onload=function(){
    loadedimgmapping_flag=1;
    initMappingTool();
};
/*////////////////////////////////////////////////////////////////
after load process
////////////////////////////////////////////////////////////////*/
(function(){
    drag_image.src='<?=URL_ROOT;?>images/dragimage.png';
    // PC用イベント処理
    document.addEventListener("dragstart",function(e){
        var eventinit=e.target;
        mouse.id=eventinit.id;
        if((mouse.id.indexOf('chessmanNtB')===0)||
           (mouse.id.indexOf('chessmanOtB')===0)||
           (mouse.id.indexOf('rangeNtB')===0)||
           (mouse.id.indexOf('rangeOtB')===0)||
           (mouse.id.indexOf('chesscharNtB')===0)||
           (mouse.id.indexOf('chesscharOtB')===0)){
            mouse.flg=1;
        }else if(mouse.id.indexOf('chessboard')!=-1){
            mouse.flg=2;
        }else{
            return false;
        }
        var rect=e.target.getBoundingClientRect();
        mouse.ox=e.clientX-rect.left;
        mouse.oy=e.clientY-rect.top;
        mouse.gx=e.clientX;
        mouse.gy=e.clientY;
        if(mouse.id.indexOf('chesscharNtB')!=-1){
            mouse.rx=32;
            mouse.ry=32;
        }else{
            mouse.rx=eventinit.width;
            mouse.ry=eventinit.height;
        }
        if(mouse.flg==1){
            mouse.go=eventinit.src;
            mouse.at=eventinit.alt;
            e.dataTransfer.setDragImage(drag_image,16,16);
        }
    });
    document.addEventListener("dragover",function(e){
        e.preventDefault();
        if(mouse.flg==1){
            var rect=document.getElementById("chessboard").getBoundingClientRect();
        }else if(mouse.flg==2){
            var rect=document.getElementById("underboard").getBoundingClientRect();
        }else{
            return false;
        }
        mouse.ox=e.clientX-rect.left;
        mouse.oy=e.clientY-rect.top;
        mouse.gx=e.clientX;
        mouse.gy=e.clientY;
    });
    // スマホ用イベント処理
    document.getElementById('chessboard').addEventListener("touchstart",function(e){
        var eventinit=e.target;
        if((eventinit.id.indexOf('chessmanOtB')!=-1)||
           (eventinit.id.indexOf('rangeOtB')!=-1)||
           (eventinit.id.indexOf('chesscharOtB')!=-1)){
    
            long_touch_timer=setTimeout(function(){openRightMenu(e);},500);
        }
    });
    document.addEventListener("touchend",function(e){
        var eventinit=e.target;
        if(event_CRM_flag[0]==false){
            if((mouse.flg!=3)){
                if((eventinit.id.indexOf('chessmanNtB')===0)||
                   (eventinit.id.indexOf('chessmanOtB')===0)||
                   (eventinit.id.indexOf('rangeNtB')===0)||
                   (eventinit.id.indexOf('rangeOtB')===0)||
                   (eventinit.id.indexOf('chesscharNtB')===0)||
                   (eventinit.id.indexOf('chesscharOtB')===0)){
                   
                    mouse.id=eventinit.id;
                    if(mouse.id.indexOf('chesscharNtB')!=-1){
                        mouse.rx=32;
                        mouse.ry=32;
                    }else{
                        mouse.rx=eventinit.width;
                        mouse.ry=eventinit.height;
                    }
                    mouse.go=eventinit.src;
                    mouse.at=eventinit.alt;
                    eventinit.style.opacity='0.5';
                    mouse.flg=3;
                }
            }else if(mouse.flg==3){
                if(eventinit.id.indexOf('trashbox')!=-1){
                    dropChessman(e);
                    mouse.flg=0;
                }else{
                    var rect=document.getElementById("chessboard").getBoundingClientRect();
                    var touchObj=e.changedTouches[0];
                    if(((touchObj.clientX-rect.left)>=0)&&
                       ((touchObj.clientX-rect.left)<=rect.width)&&
                       ((touchObj.clientY-rect.top)>=0)&&
                       ((touchObj.clientY-rect.top)<=rect.height)){
                        mouse.ox=touchObj.clientX-rect.left;
                        mouse.oy=touchObj.clientY-rect.top;
                        mouse.gx=touchObj.clientX;
                        mouse.gy=touchObj.clientY;
                        placeChessboard(e);
                    }
                    mouse.flg=0;
                }
                if(document.getElementById(mouse.id)!=null){
                    document.getElementById(mouse.id).style.opacity='1';
                }
            }
        }
        return false;
    });
    document.addEventListener("mousedown",eventRightMenu);
    document.getElementById('mapsurface').addEventListener("mousedown",eventCickDownChessBoard);
    document.getElementById('chessboard').addEventListener("contextmenu",openRightMenu);
    document.addEventListener("touchcancel",function(){clearTimeout(long_touch_timer);});
    document.addEventListener("touchmove",function(){clearTimeout(long_touch_timer);});
    drawCoordinate('#666');
})();
</script>