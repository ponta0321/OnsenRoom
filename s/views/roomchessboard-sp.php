<div id="underboard">
    <div id="boardsurface">
        <img id="boardimage" src="" width="<?=$game_board_bgimg_width;?>" height="<?=$game_board_bgimg_height;?>" border="0" style="display:none;" />
    </div>
    <div id="boardnumber" style="width:<?=$game_boardwidth*32;?>px;height:<?=$game_boardheight*32;?>px;">
        <canvas id="cvs_boardnumber" width="3200" height="3200" style="position:absolute;top:0;left:0;"></canvas>
    </div>
    <div id="boardgrid" class="c_bg_a" style="width:<?=$game_boardwidth*32;?>px;height:<?=$game_boardheight*32;?>px;z-index:1;"></div>
    <canvas id="mapsurface" style="position:absolute;top:0;left:0;z-index:2;"></canvas>
    <div
        id="chessboard"
        draggable="false"
        ondrop="placeChessboard(event);event.preventDefault();"
        ondragenter="event.preventDefault();"
        ondragover="event.preventDefault();"
        style="width:<?=$game_boardwidth*32;?>px;height:<?=$game_boardheight*32;?>px;z-index:3;">
    </div>
</div>
<span id="text_ruler" class="cm_pnumb" style="visibility:hidden;"></span>
<script>
/*////////////////////////////////////////////////////////////////
variable
////////////////////////////////////////////////////////////////*/
var event_CRM_flag=[false,''];
var mouse={id:null,flg:null,ox:null,oy:null,gx:null,gy:null,rx:null,ry:null,go:null,at:null};
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
            document.getElementById('chara_right_menu-bg').style.display='block';
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
                document.getElementById('chara_right_menu-bg').style.display='none';
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
                document.getElementById('chara_right_menu-bg').style.display='none';
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
                document.getElementById('chara_right_menu-bg').style.display='none';
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
                document.getElementById('chara_right_menu-bg').style.display='none';
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
                document.getElementById('chara_right_menu-bg').style.display='none';
                mouse.id=event_CRM_flag[1];
                dropChessman();
                event_CRM_flag[0]=false;
                event_CRM_flag[1]='';
            }
        }else if(eventinit.id=='crm_fore'){ //最前面
            if(e.button==0){
                document.getElementById('chara_right_menu-bg').style.display='none';
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
                document.getElementById('chara_right_menu-bg').style.display='none';
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
            document.getElementById('chara_right_menu-bg').style.display='none';
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
            if(deco_num!=t_deco_num){
                change_map_data_flag=true;
                map_data_array[result_y][result_x]=t_line_num+deco_num*100;
                changeMapChipImage(mapping_tool_flag[1],result_x,result_y);
            }else if(line_num!=t_line_num){
                change_map_data_flag=true;
                map_data_array[result_y][result_x]=line_num+t_deco_num*100;
                changeMapChipImage(mapping_tool_flag[1],result_x,result_y);
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
        document.getElementById('id_mappingtool_box').style.display='none';
        document.getElementById('id_chessman_box').style.display='block';
        mapping_tool_flag[0]=false;
    }else if(tab_key==1){
        document.getElementById('bl_tab_0').classList.remove("bl_buttom_actived");
        document.getElementById('bl_tab_1').classList.add("bl_buttom_actived");
        document.getElementById('boardgrid').classList.remove("c_bg_a");
        document.getElementById('boardgrid').classList.add("c_bg_b");
        document.getElementById('boardgrid').style.zIndex=2;
        document.getElementById('mapsurface').style.zIndex=3;
        document.getElementById('chessboard').style.zIndex=1;
        document.getElementById('id_mappingtool_box').style.display='block';
        document.getElementById('id_chessman_box').style.display='none';
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
		document.getElementById('chara_right_menu-bg').style.display='none';
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
        document.getElementById('now_loaded').style.left=Math.floor((window.innerWidth-204)/2)+'px';
        document.getElementById('now_loaded').style.top=Math.floor((window.innerHeight-80)/2)+'px';
        document.getElementById('now_loaded').style.display='block';
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
    document.getElementById('chessboard').addEventListener("touchstart",function(e){
        var eventinit=e.target;
        if((eventinit.id.indexOf('chessmanOtB')!=-1)||
           (eventinit.id.indexOf('rangeOtB')!=-1)||
           (eventinit.id.indexOf('chesscharOtB')!=-1)){
    
            long_touch_timer=setTimeout(function(){openRightMenu(e);},500);
        }
    });
    document.addEventListener("touchend",function(e){
        clearTimeout(long_touch_timer);
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