<div class="room_setting_box">
    <div id="id_ss_label_setting" class="r_char_sheet_acb" onClick='pushSSAc("id_ss_box_setting")'>設定&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
    <div id="id_ss_box_setting" style="padding:0 0 5px 5px;">
        <p style="margin:2px 0;"><label>シナリオセットの選択</label></p>
        <p style="margin:3px 0;"><select id="ss_gam_select" style="padding:0 4px;font-size:10px;">
        <?php
            $temp_html='';
            if(0<couting($scenariosetlist_array)){
                foreach($scenariosetlist_array as $ss_key => $ss_column){
                    $temp_html.='<option value="'.$ss_column[0].'">';
                    if(!empty($ss_column[1])){
                        $temp_html.='【'.$ss_column[1].'】';
                    }
                    $temp_html.=$ss_column[2];
                    $temp_html.='</option>';
                }
                echo $temp_html;
                unset($scenariosetlist_array);
            }
        ?>
        </select></p>
        <p style="margin:3px 0;"><input type="button" onClick="pushRequestSS();" style="padding:0 4px;font-size:10px;" value="読み込む" /></p>
        <div class="room_setting_box_wb">
            <p style="margin:2px 0;"><label>ローカルから読み込む</label></p>
            <p style="margin:3px 0;"><input type="file" id="id_ss_file" accept="text/xml" style="font-size:10px;" /></p>
            <p style="margin:3px 0;"><input type="button" onClick="pushLoadSS();" style="padding:0 4px;font-size:10px;" value="読み込む" /></p>
        </div>
    </div>
    <div id="id_ss_label_base" class="r_char_sheet_acb" onClick='pushSSAc("id_ss_box_base")'>ベース&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
    <div id="id_ss_box_base" style="padding:0 0 5px 5px;">
        <p style="margin:2px 0;">タイトル：&nbsp;<span id="ss_ttl_area"></span></p>
        <div class="room_setting_box_wb">
            <p>目次・解説：<p>
            <pre id="ss_cnt_area" style="max-height:250px;overflow:auto;"></pre>
        </div>
    </div>
    <div id="id_ss_label_list" class="r_char_sheet_acb" onClick='pushSSAc("id_ss_box_list")'>リスト&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
    <div id="id_ss_box_list" style="max-height:250px;padding:0 0 5px 5px;overflow:auto;">
        <div class="ss_list">
            <p class="font_gray">リストはありません。</p>
        </div>
    </div>
    <div id="id_ss_label_scene" class="r_char_sheet_acb" onClick='pushSSAc("id_ss_box_scene")'>シーン&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）</div>
    <div id="id_ss_box_scene" style="padding:0 0 5px 5px;">
        <table class="ss_scene_table">
            <tr>
                <td class="t"><input type="button" id="ss_shift_first" onClick="pushSceneFirstSS();" style="padding:0 4px;font-size:10px;" value="最初 <<" disabled="disabled" /></td>
                <td class="c"><input type="button" id="ss_shift_before" onClick="pushSceneBeforeSS();" style="padding:0 4px;font-size:10px;" value="前 <" disabled="disabled" /></td>
                <td>No.&nbsp;<select id="ss_no_select" onChange="changeSceneNoSS();" style="padding:0 4px;font-size:10px;" disabled="disabled">
                </select></td>
                <td class="c"><input type="button" id="ss_shift_next" onClick="pushSceneNextSS();" style="padding:0 4px;font-size:10px;" value="> 次" disabled="disabled" /></td>
                <td class="t"><input type="button" id="ss_shift_last" onClick="pushSceneLastSS();" style="padding:0 4px;font-size:10px;" value=">> 最後" disabled="disabled" /></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:left">
                    <p>発言者</p>
                    <input id="ss_spk_area" type="text" value="" />
                    <p id="ss_sn_typ">コマンド</p>
                    <input id="ss_cmd_area" type="text" value="" />
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:right">
                    <input type="button" id="ss_exe_button" onClick="pushSceneExeSS();" style="padding:0 4px;font-size:10px;" value="実行" />&nbsp;&nbsp;
                    <input type="button" id="ss_exenxt_button" onClick="pushSceneExeNxtSS();" style="padding:0 4px;font-size:10px;" value="実行して次に進む" disabled="disabled" />&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:left">
                    <p>説明</p>
                    <textarea id="ss_des_area" readonly></textarea>
                </td>
            </tr>
        </table>
        <input type="hidden" id="ss_opn_scene" value="00000001" />
    </div>
</div>
<script>
    function loadSSData(xml){ // シナリオセットデータのロード
        var ttl_value=getDomValue(xml.getElementById('ttl'));
        if(ttl_value.length>0){
            window.localStorage.clear();
            window.localStorage.setItem('ttl',ttl_value);
            window.localStorage.setItem('own',getDomValue(xml.getElementById('own')));
            window.localStorage.setItem('gtp',getDomValue(xml.getElementById('gtp')));
            window.localStorage.setItem('dtl',getDomValue(xml.getElementById('dtl')));
            window.localStorage.setItem('cnt',getDomValue(xml.getElementById('cnt')));
            window.localStorage.setItem('hdf',getDomValue(xml.getElementById('hdf')));
            var scene_domlist=xml.getElementsByTagName('sc');
            var scene_idlist=new Array();
            if(scene_domlist.length>0){
                for(var i=0;i<scene_domlist.length;i++){
                    if(scene_domlist[i]['id']){
                        var dom_list=scene_domlist[i].getElementsByTagName('typ');
                        var typ_value=getDomValue(dom_list[0]);
                        dom_list=scene_domlist[i].getElementsByTagName('des');
                        var des_value=getDomValue(dom_list[0]);
                        dom_list=scene_domlist[i].getElementsByTagName('spk');
                        var spk_value=getDomValue(dom_list[0]);
                        dom_list=scene_domlist[i].getElementsByTagName('cmd');
                        var cmd_value=getDomValue(dom_list[0]);
                        var scene_valuelist=new Array(
                            typ_value,
                            des_value,
                            spk_value,
                            cmd_value
                        );
                        scene_idlist.push(scene_domlist[i]['id']);
                        window.localStorage.setItem('scene_'+scene_domlist[i]['id'],JSON.stringify(scene_valuelist));
                        //console.log(window.localStorage.getItem('scene_'+scene_domlist[i]['id']));
                    }
                }
            }
            window.localStorage.setItem('idlist',JSON.stringify(scene_idlist));
            //console.log(window.localStorage.getItem('idlist'));
            if(loadScenarioBase()){ // ベースデータの反映
                if(loadScenarioList()){ // ベースデータの反映
                    loadSceneConntents(scene_idlist[0]);
                }
            }else{
                displayWTL('読み込むデータにタイトルがないため、読み込みを中断しました。',50,50,255,0,0);
            }
        }else{
            displayWTL('読み込むデータはシナリオセットではないか不正なデータです。',50,50,255,0,0);
        }
    }
    function pushLoadSS(){ // 設定 ロ－カル読み込みボタン押
        var files=document.getElementById('id_ss_file').files;
        var reader=new FileReader();
        reader.onload=(function(theFile){
            return function(e){
                var parser=new DOMParser();
                var xml=parser.parseFromString(e.target.result,'text/xml');
                loadSSData(xml);
            };
        })(files[0]);
        reader.readAsText(files[0]);
    }
    function pushRequestSS(){ // 設定 選択 読み込みボタン押
        $.post('<?=URL_ROOT;?>exe/getssdata.php',{
            principal_id:CONST_PRINCIPAL_ID,
            player_ip:'<?=$player_ip;?>',
            sso:document.getElementById('ss_gam_select').value,
        },function(data){
            if(data.indexOf('ERR=')===0){
                openWTL(data);
            }else{
                var parser=new DOMParser();
                var xml=parser.parseFromString(data,'text/xml');
                loadSSData(xml);
            }
        },'html');
    }
    function loadScenarioBase(){ // ベースデータの反映
        var ttl_value=window.localStorage.getItem('ttl');
        var gtp_value=window.localStorage.getItem('gtp');
        var own_value=window.localStorage.getItem('own');
        var cnt_value=window.localStorage.getItem('cnt');
        if(ttl_value!=null){
            var title_value=ttl_value;
            if(gtp_value!=null){
                title_value='【'+gtp_value+'】'+title_value;
            }
            if(own_value!=null){
                title_value=title_value+'（制作者：'+displayNickName(own_value)+'）';
            }
            document.getElementById('ss_ttl_area').innerHTML=title_value;
            var contents_value='';
            if(cnt_value!=null){
                contents_value=cnt_value;
            }
            document.getElementById('ss_cnt_area').innerHTML=contents_value;
            return true;
        }
        return false;
    }
    function loadScenarioList(){ // リストデータの反映
        var opt_v='';
        var html_v='';
        var hdf_value=window.localStorage.getItem('hdf');
        var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
        if(scene_idlist){
            if(scene_idlist.length>0){
                for(var i=0;i<scene_idlist.length;i++){
                    var scene_datalist=JSON.parse(window.localStorage.getItem('scene_'+scene_idlist[i]));
                    if(scene_datalist){
                        var typ_value=scene_datalist[0];
                        var des_value=scene_datalist[1];
                        var spk_value=scene_datalist[2];
                        var cmd_value=scene_datalist[3];
                        if((typ_value!=null)&&(spk_value!=null)&&(cmd_value!=null)){
                            html_v+='<div class="ss_list sslli" id="ss_list_'+scene_idlist[i]+'">';
                            html_v+='<p class="m"><span class="m">'+scene_idlist[i]+'</span></p>';
                            html_v+='<p class="s"><span class="s">：</span></p>';
                            html_v+='<p class="m"><span class="m">';
                            if(typ_value=='0'){
                                html_v+='コマンド';
                            }else if(typ_value=='1'){
                                html_v+='背景変更';
                            }else if(typ_value=='2'){
                                html_v+='背景消去';
                            }else if(typ_value=='3'){
                                html_v+='BGM再生';
                            }else if(typ_value=='4'){
                                html_v+='BGM停止';
                            }else if(typ_value=='5'){
                                html_v+='ﾏｯﾌﾟ変更';
                            }else if(typ_value=='6'){
                                html_v+='ﾏｯﾌﾟ消去';
                            }else if(typ_value=='7'){
                                html_v+='解説';
                            }else if(typ_value=='8'){
                                html_v+='ﾏｽｸ配置';
                            }else if(typ_value=='9'){
                                html_v+='ｺﾏ全消去';
                            }else if(typ_value=='10'){
                                html_v+='ﾎﾞｰﾄﾞ保存';
                            }else if(typ_value=='11'){
                                html_v+='ﾎﾞｰﾄﾞ読込';
                            }
                            html_v+='</span></p>';
                            html_v+='<p class="s"><span class="s">/</span></p>';
                            if((hdf_value==0)||(spk_value=='システム')){
                                html_v+='<p class="m"><span class="m">'+spk_value+'</span></p>';
                            }else{
                                html_v+='<p class="m"><span class="m">＊＊＊＊</span></p>';
                            }
                            html_v+='<p class="s"><span class="s">：</span></p>';
                            if(typ_value==0){
                                if(hdf_value==0){
                                    html_v+='<p><input type="text" value="'+cmd_value+'" readonly /></p>';
                                }else{
                                    html_v+='<p><input type="text" value="'+des_value+'" readonly /></p>';
                                }
                            }else{
                                html_v+='<p><input type="text" value="'+des_value+'" readonly /></p>';
                            }
                            html_v+='<p class="m"><input id="sslc_'+scene_idlist[i]+'" type="button" onClick="pushListExeSS(\''+scene_idlist[i]+'\');" value="実行" /></p>';
                            html_v+='<p class="m"><input type="button" onClick="pushListOpnSS(\''+scene_idlist[i]+'\');" value="開く" /></p>';
                            html_v+='</div>';
                            opt_v+='<option value="'+scene_idlist[i]+'">'+scene_idlist[i]+'</option>';
                        }
                    }
                }
            }
        }
        if(html_v!=''){
            document.getElementById('ss_no_select').innerHTML=opt_v;
            document.getElementById('ss_no_select').disabled=false;
            document.getElementById('id_ss_box_list').innerHTML=html_v;
        }else{
            document.getElementById('ss_no_select').innerHTML='';
            document.getElementById('ss_no_select').disabled=true;
            document.getElementById('id_ss_box_list').innerHTML='<div class="ss_list"><p>&nbsp;シーンはありません。</p></div>';
            return false;
        }
        return true;
    }
    function loadSceneConntents(scene_id){ // シーンデータの反映
        var first_id='';
        var last_id='';
        var before_id='';
        var next_id='';
        var hdf_value=window.localStorage.getItem('hdf');
        var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
        if(scene_idlist){
            if(scene_idlist.length>0){
                if(scene_idlist[0]!=null){
                    first_id=scene_idlist[0];
                }
                if(scene_idlist[(scene_idlist.length-1)]!=null){
                    last_id=scene_idlist[(scene_idlist.length-1)];
                }
                for(var i=0;i<scene_idlist.length;i++){
                    if(scene_idlist[i]==scene_id){
                        if(scene_idlist[(i-1)]!=null){
                            before_id=scene_idlist[(i-1)];
                        }
                        if(scene_idlist[(i+1)]!=null){
                            next_id=scene_idlist[(i+1)];
                        }
                        var typ_value=0;
                        var des_value='';
                        var spk_value='システム';
                        var cmd_value='';
                        var scene_datalist=JSON.parse(window.localStorage.getItem('scene_'+scene_idlist[i]));
                        if(scene_datalist){
                            if((scene_datalist[0]!=null)&&(scene_datalist[1]!=null)&&(scene_datalist[2]!=null)&&(scene_datalist[3]!=null)){
                                typ_value=scene_datalist[0];
                                des_value=scene_datalist[1];
                                spk_value=scene_datalist[2];
                                cmd_value=scene_datalist[3];
                            }
                        }
                        break;
                    }
                }
            }
        }
        document.getElementById('ss_shift_first').disabled=first_id==''?true:false;
        document.getElementById('ss_shift_before').disabled=before_id==''?true:false;
        document.getElementById('ss_shift_next').disabled=next_id==''?true:false;
        document.getElementById('ss_shift_last').disabled=last_id==''?true:false;
        document.getElementById('ss_exenxt_button').disabled=next_id==''?true:false;
        document.getElementById('ss_no_select').value=scene_id;
        document.getElementById('ss_opn_scene').value=scene_id;
        if(typ_value=='7'){
            document.getElementById('ss_spk_area').value='ＧＭ';
        }else{
            document.getElementById('ss_spk_area').value=spk_value;
        }
        if(hdf_value==0){
            document.getElementById('ss_spk_area').style.visibility='visible';
        }else{
            document.getElementById('ss_spk_area').style.visibility='hidden';
        }
        if(typ_value=='0'){
            document.getElementById('ss_sn_typ').innerHTML='コマンド';
        }else if(typ_value=='1'){
            document.getElementById('ss_sn_typ').innerHTML='背景変更';
        }else if(typ_value=='2'){
            document.getElementById('ss_sn_typ').innerHTML='背景消去';
        }else if(typ_value=='3'){
            document.getElementById('ss_sn_typ').innerHTML='BGM再生';
        }else if(typ_value=='4'){
            document.getElementById('ss_sn_typ').innerHTML='BGM停止';
        }else if(typ_value=='5'){
            document.getElementById('ss_sn_typ').innerHTML='マップ変更';
        }else if(typ_value=='6'){
            document.getElementById('ss_sn_typ').innerHTML='マップ消去';
        }else if(typ_value=='7'){
            document.getElementById('ss_sn_typ').innerHTML='解説(コマンド)';
        }else if(typ_value=='8'){
            document.getElementById('ss_sn_typ').innerHTML='マスク配置';
        }else if(typ_value=='9'){
            document.getElementById('ss_sn_typ').innerHTML='コマ全消去';
        }else if(typ_value=='10'){
            document.getElementById('ss_sn_typ').innerHTML='ボード保存';
        }else if(typ_value=='11'){
            document.getElementById('ss_sn_typ').innerHTML='ボード読込';
        }
        document.getElementById('ss_cmd_area').value=cmd_value;
        if(hdf_value==0){
            document.getElementById('ss_cmd_area').style.visibility='visible';
        }else{
            document.getElementById('ss_cmd_area').style.visibility='hidden';
        }
        adjustDesHeightSS(des_value);
        
        var ss_list_list=document.getElementsByClassName('sslli');
        if(ss_list_list.length>0){
            for(var i=0;i<ss_list_list.length;i++){
                if(ss_list_list[i]['id']==('ss_list_'+scene_id)){
                    ss_list_list[i].style.backgroundColor='#FFF0E0';
                }else{
                    ss_list_list[i].style.backgroundColor='#FFFFFF';
                }
            }
        }
        return true;
    }
	function adjustDesHeightSS(des_value){
        var des_area_elm=document.getElementById("ss_des_area");
		des_area_elm.style.height="30px";
        document.getElementById('ss_des_area').innerHTML=des_value;
        des_area_elm.style.height=(des_area_elm.scrollHeight+6)+"px";
	}
    function pushListOpnSS(scene_id){ // リスト 開くボタン押
        loadSceneConntents(scene_id);
    }
    function pushListExeSS(scene_id){ // リスト 実行ボタン押
        var first_id='';
        var last_id='';
        var before_id='';
        var next_id='';
        var hdf_value=window.localStorage.getItem('hdf');
        var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
        if(scene_idlist){
            if(scene_idlist.length>0){
                if(scene_idlist[0]!=null){
                    first_id=scene_idlist[0];
                }
                if(scene_idlist[(scene_idlist.length-1)]!=null){
                    last_id=scene_idlist[(scene_idlist.length-1)];
                }
                for(var i=0;i<scene_idlist.length;i++){
                    if(scene_idlist[i]==scene_id){
                        if(scene_idlist[(i-1)]!=null){
                            before_id=scene_idlist[(i-1)];
                        }
                        if(scene_idlist[(i+1)]!=null){
                            next_id=scene_idlist[(i+1)];
                        }
                        var typ_value=0;
                        var des_value='';
                        var spk_value='システム';
                        var cmd_value='';
                        var scene_datalist=JSON.parse(window.localStorage.getItem('scene_'+scene_idlist[i]));
                        if(scene_datalist){
                            if((scene_datalist[0]!=null)&&(scene_datalist[1]!=null)&&(scene_datalist[2]!=null)&&(scene_datalist[3]!=null)){
                                typ_value=scene_datalist[0];
                                des_value=scene_datalist[1];
                                spk_value=scene_datalist[2];
                                cmd_value=scene_datalist[3];
                            }
                        }
                        break;
                    }
                }
            }
        }
        if(cmd_value.length<1){
            return false;
        }else if(cmd_value=='/bs'){
            saveLocalBD('<?=URL_ROOT;?>r/n/<?=$base_room_file;?>/data.xml','sslc_'+scene_id);
        }else if(cmd_value=='/bl'){
            loadLocalBD('<?=URL_ROOT;?>exe/putsettingdata.php','<?=$base_room_file;?>','<?=$principal_id;?>','sslc_'+scene_id);
        }else{
            send_speeker=spk_value.trim();
            if(send_speeker.length<1){
                return false;
            }
            document.getElementById('sslc_'+scene_id).disabled=true;
            $.post('<?=URL_ROOT;?>exe/putcomment.php',{
                comment:cmd_value,
                chat_color:'#000000',
                call_name:send_speeker,
                chat_type:1,
                observer_flag:<?=$observer_flag;?>,
                use_dicebot:document.getElementById('ind_dicebot').value,
                principal:'<?=$principal_id;?>',
                xml:'<?=$base_room_file;?>',
            },function(data){
                openWTL(data);
                document.getElementById('sslc_'+scene_id).disabled=false;
            },'html');
        }
        return true;
    }
    function pushSceneFirstSS(){ // シーン 最初ボタン押
        var scene_id=document.getElementById('ss_opn_scene').value;
        var first_id='';
        var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
        if(scene_idlist){
            if(scene_idlist.length>0){
                if(scene_idlist[0]!=null){
                    first_id=scene_idlist[0];
                }
            }
        }
        if(first_id!=''){
            loadSceneConntents(first_id);
        }
    }
    function pushSceneBeforeSS(){ // シーン 前ボタン押
        var scene_id=document.getElementById('ss_opn_scene').value;
        var before_id='';
        var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
        if(scene_idlist){
            if(scene_idlist.length>0){
                for(var i=0;i<scene_idlist.length;i++){
                    if(scene_idlist[i]==scene_id){
                        if(scene_idlist[(i-1)]!=null){
                            before_id=scene_idlist[(i-1)];
                        }
                        break;
                    }
                }
            }
        }
        if(before_id!=''){
            loadSceneConntents(before_id);
        }
        
    }
    function changeSceneNoSS(){ // シーン シーンNo変更
        loadSceneConntents(document.getElementById('ss_no_select').value);
    }
    function pushSceneNextSS(){ // シーン 次ボタン押
        var scene_id=document.getElementById('ss_opn_scene').value;
        var next_id='';
        var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
        if(scene_idlist){
            if(scene_idlist.length>0){
                for(var i=0;i<scene_idlist.length;i++){
                    if(scene_idlist[i]==scene_id){
                        if(scene_idlist[(i+1)]!=null){
                            next_id=scene_idlist[(i+1)];
                        }
                        break;
                    }
                }
            }
        }
        if(next_id!=''){
            loadSceneConntents(next_id);
        }
        
    }
    function pushSceneLastSS(){ // シーン 最後ボタン押
        var scene_id=document.getElementById('ss_opn_scene').value;
        var last_id='';
        var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
        if(scene_idlist){
            if(scene_idlist.length>0){
                if(scene_idlist[(scene_idlist.length-1)]!=null){
                    last_id=scene_idlist[(scene_idlist.length-1)];
                }
            }
        }
        if(last_id!=''){
            loadSceneConntents(last_id);
        }
        
    }
    function pushSceneExeSS(){ // シーン 実行ボタン押
        var send_comment=document.getElementById('ss_cmd_area').value;
        if(send_comment.length<1){
            displayWTL('コマンドが空白です。<br>解説のみの場合、「次」で進めてください。',50,50,255,0,0);
            return false;
        }else if(send_comment=='/bs'){
            saveLocalBD('<?=URL_ROOT;?>r/n/<?=$base_room_file;?>/data.xml','ss_cmd_area,ss_exe_button,ss_exe_button,ss_exenxt_button');
        }else if(send_comment=='/bl'){
            loadLocalBD('<?=URL_ROOT;?>exe/putsettingdata.php','<?=$base_room_file;?>','<?=$principal_id;?>','ss_cmd_area,ss_exe_button,ss_exe_button,ss_exenxt_button');
        }else{
            var send_speeker=document.getElementById('ss_spk_area').value;
            send_speeker=send_speeker.trim();
            if(send_speeker.length<1){
                displayWTL('発言者が空白です。',50,50,255,0,0);
                return false;
            }
            document.getElementById('ss_cmd_area').disabled=true;
            document.getElementById('ss_spk_area').disabled=true;
            document.getElementById('ss_exe_button').disabled=true;
            document.getElementById('ss_exenxt_button').disabled=true;
			setTimeout(function(){
					document.getElementById('ss_cmd_area').disabled=false;
					document.getElementById('ss_spk_area').disabled=false;
					document.getElementById('ss_exe_button').disabled=false;
				},10000);
            $.post('<?=URL_ROOT;?>exe/putcomment.php',{
                comment:send_comment,
                chat_color:'#000000',
                call_name:send_speeker,
                chat_type:1,
                observer_flag:<?=$observer_flag;?>,
                use_dicebot:document.getElementById('ind_dicebot').value,
                principal:'<?=$principal_id;?>',
                xml:'<?=$base_room_file;?>',
            },function(data){
                openWTL(data);
                document.getElementById('ss_cmd_area').disabled=false;
                document.getElementById('ss_spk_area').disabled=false;
                document.getElementById('ss_exe_button').disabled=false;
                var scene_id=document.getElementById('ss_opn_scene').value;
                var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
                if(scene_idlist){
                    if(scene_idlist.length>0){
                        if(scene_idlist[(scene_idlist.length-1)]!=scene_id){
                            document.getElementById('ss_exenxt_button').disabled=false;
                        }
                    }
                }
            },'html');
        }
        return true;
    }
    function pushSceneExeNxtSS(){ // シーン 実行して次に進むボタン押
        if(!pushSceneExeSS()){
            return false;
        }
        pushSceneNextSS();
        return true;
    }
    function pushSSAc(targetid){ // アコーディオン処理
        var tagid=['id_ss_label_setting','id_ss_label_base','id_ss_label_list','id_ss_label_scene'];
        var tagtargetid=['id_ss_box_setting','id_ss_box_base','id_ss_box_list','id_ss_box_scene'];
        var tagname=['設定','ベース','リスト','シーン'];
        if(document.getElementById(targetid).style.display=="none"){
            document.getElementById(targetid).style.display="block";
            for(i=0;i<3;i++){
                if(tagtargetid[i]==targetid){
                    document.getElementById(tagid[i]).innerHTML=tagname[i]+'&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）';
                }
            }
        }else{
            document.getElementById(targetid).style.display="none";
            for(i=0;i<3;i++){
                if(tagtargetid[i]==targetid){
                    document.getElementById(tagid[i]).innerHTML=tagname[i]+'&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）';
                }
            }
        }
    }
    (function(){
        if(loadScenarioBase()){ // ベースデータ（基本）の反映
            if(loadScenarioList()){ // ベースデータ（詳細）の反映
                var scene_idlist=JSON.parse(window.localStorage.getItem('idlist'));
                if(scene_idlist){
                    if(scene_idlist.length>0){
                        loadSceneConntents(scene_idlist[0]);
                    }
                }
            }
        }
    })();
</script>