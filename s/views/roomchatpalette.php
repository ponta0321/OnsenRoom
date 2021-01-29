<table style="border-collapse:collapse;border-spacing:0;width:100%;height:100%;">
    <tr><td style="width:100%;height:100%;vertical-align:bottom;">
        <div id="macro_list" class="tab_block" style="height:100%;overflow:auto;">
            <div class="ss_list"><p class="font_gray">&nbsp;マクロはありません。</p></div>
        </div>
    </td></tr>
</table>
<script>
    function setMacroList(call_name_id){ // マクロリストの反映
        if(call_name_id==null){
            call_name_id=document.getElementById('id_call_name_id').value;
        }
        var html_v='';
        if(obj_character_list.charlist){
            for(var key in obj_character_list.charlist){
                if(obj_character_list.charlist[key].id==call_name_id){
                    document.getElementById('id_call_name_id').value=obj_character_list.charlist[key].id;
                    for(var sm_key in obj_character_list.charlist[key].macro){
                        html_v+='<div class="ss_list mclli" style="cursor:pointer;" id="mc_list_'+sm_key+'" onClick="setCommandOnCS(\''+obj_character_list.charlist[key].id+'\','+sm_key+');">';
                        html_v+='<p class="m"><span class="m">'+obj_character_list.charlist[key].name+'</span></p>';
                        html_v+='<p class="s"><span class="s">：</span></p>';
                        html_v+='<p class="m"><span class="m">'+obj_character_list.charlist[key].macro[sm_key].name+'</span></p>';
                        html_v+='<p class="s"><span class="s">：</span></p>';
                        html_v+='<p><input type="text" style="cursor:pointer;" value="'+obj_character_list.charlist[key].macro[sm_key].mat+'" readonly /></p>';
                        html_v+='</div>';
                    }
                    break;
                }
            }
        }
        if(html_v!=''){
            document.getElementById('macro_list').innerHTML=html_v;
        }else{
            document.getElementById('macro_list').innerHTML='<div class="ss_list"><p class="font_gray">&nbsp;マクロはありません。</p></div>';
            return false;
        }
        return true;
    }
    // setMacroList();
    function setCommandOnCS(chara_id,mc_id){ // チャットスペースへ転送
        var temp_call_name=document.getElementById('id_call_name').value;
        if(obj_character_list.charlist){
            for(var key in obj_character_list.charlist){
                if(obj_character_list.charlist[key].id==chara_id){
                    document.getElementById('id_comment').value='$'+obj_character_list.charlist[key].macro[mc_id].name;
                    break;
                }
            }
        }
        var ss_list_list=document.getElementsByClassName('mclli');
        if(ss_list_list.length>0){
            for(var i=0;i<ss_list_list.length;i++){
                if(ss_list_list[i]['id']==('mc_list_'+mc_id)){
                    ss_list_list[i].style.backgroundColor='#FFF0E0';
                }else{
                    ss_list_list[i].style.backgroundColor='#FFFFFF';
                }
            }
        }
        return true;
    }
</script>