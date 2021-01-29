<div style="width:98%;margin:0 auto 5px auto;">
    <input type="button" value="カウントダウン" onClick="sendTimerEvent('down');" <?=$observer_flag!=1?'':'disabled';?> />&nbsp;
    <input type="button" value="カウントアップ" onClick="sendTimerEvent('up');" <?=$observer_flag!=1?'':'disabled';?> />&nbsp;
    <input type="button" value="更新" <?=$observer_flag!=1?'':'disabled';?> />
</div>
<table class="counter_table">
    <tr><th class="btl">名称</th><th class="btl">値</th><th class="btrl">step</th><th style="visibility:hidden;">&nbsp;</th><th style="visibility:hidden;">&nbsp;</th>
    </tr>
<?php for($i=0;$i<30;$i++){ ?>
    <tr>
        <td class="<?=$i!=29?'bl':'bbl';?>"><input id="tmr_nm_<?=$i;?>" class="<?=$i%2==0?'t_t_w':'t_t_o';?>" style="color:#AAA;" onFocus="doNotUpdate('tmr_<?=$i;?>');" onblur="setDataTimerArray(<?=$i;?>,0);" value="" <?=$observer_flag!=1?'':'disabled';?> /></td>
        <td class="<?=$i!=29?'bl':'bbl';?>" style="width:43px;"><input id="tmr_vl_<?=$i;?>" class="<?=$i%2==0?'t_t_w':'t_t_o';?>" type="number" onFocus="doNotUpdate('tmr_<?=$i;?>');" onblur="setDataTimerArray(<?=$i;?>,1);" value="0" <?=$observer_flag!=1?'':'disabled';?> /></td>
        <td class="<?=$i!=29?'brl':'brbl';?>" style="width:28px;"><input id="tmr_st_<?=$i;?>" class="<?=$i%2==0?'t_t_w':'t_t_o';?>" type="number" onFocus="doNotUpdate('tmr_<?=$i;?>');" onblur="setDataTimerArray(<?=$i;?>,2);" value="1" <?=$observer_flag!=1?'':'disabled';?> /></td>
        <td style="width:18px;"><input id="tmr_rs_<?=$i;?>" type="button" onClick="resetTimeData(<?=$i;?>);" style="width:18px;" value="リ" <?=$observer_flag!=1?'':'disabled';?> /></td>
        <td style="width:18px;"><input id="tmr_dl_<?=$i;?>" type="button" onClick="deleteTimeData(<?=$i;?>);" style="width:18px;" value="消" <?=$observer_flag!=1?'':'disabled';?> /></td>
    </tr>
<?php } ?>
</table>
<script>
var timer_data_array=[];
<?php
    for($i=0;$i<30;$i++){
        echo 'timer_data_array['.$i.']=[\'\',0,1];';
    }
?>
function resetTimeData(tmr_row){
    if(timer_data_array[tmr_row][1]!=0){
        timer_data_array[tmr_row][1]=0;
        document.getElementById('tmr_nm_'+tmr_row).style.color='#AAA';
        document.getElementById('tmr_vl_'+tmr_row).value=0;
        sendTimeData(tmr_row);
    }
}
function deleteTimeData(tmr_row){
    if((timer_data_array[tmr_row][0]!='')||(timer_data_array[tmr_row][1]!=0)||(timer_data_array[tmr_row][2]!=1)){
        timer_data_array[tmr_row][0]='';
        timer_data_array[tmr_row][1]=0;
        timer_data_array[tmr_row][2]=1;
        document.getElementById('tmr_nm_'+tmr_row).value='';
        document.getElementById('tmr_nm_'+tmr_row).style.color='#AAA';
        document.getElementById('tmr_vl_'+tmr_row).value=0;
        document.getElementById('tmr_st_'+tmr_row).value=1;
        sendTimeData(tmr_row);
    }
}
function setDataTimerArray(tmr_row,tmr_column){
    if(tmr_column==0){
        timer_data_array[tmr_row][tmr_column]=document.getElementById('tmr_nm_'+tmr_row).value;
    }else if(tmr_column==1){
        var tmr_count=parseInt(document.getElementById('tmr_vl_'+tmr_row).value);
        timer_data_array[tmr_row][tmr_column]=tmr_count;
        if(tmr_count>0){
            document.getElementById('tmr_nm_'+tmr_row).style.color='#000';
        }else if(tmr_count<0){
            document.getElementById('tmr_nm_'+tmr_row).style.color='#F88';
        }else{
            document.getElementById('tmr_nm_'+tmr_row).style.color='#AAA';
        }
    }else if(tmr_column==2){
        var tmr_count=parseInt(document.getElementById('tmr_st_'+tmr_row).value);
        timer_data_array[tmr_row][tmr_column]=tmr_count;
    }
    sendTimeData(tmr_row);
}
function sendTimeData(tmr_row){
    sendSettingData({
        game_timer_no:tmr_row,
        game_timer_name:timer_data_array[tmr_row][0],
        game_timer_value:timer_data_array[tmr_row][1],
        game_timer_step:timer_data_array[tmr_row][2]
    });
    console.log(tmr_row+' / '+timer_data_array[tmr_row][0]+' / '+timer_data_array[tmr_row][1]+' / '+timer_data_array[tmr_row][2]);
}
function sendTimerEvent(event_name){
    sendSettingData({
        game_timer_event:event_name
    });
}
function changeTimerValue(str_game_timer){
    var record_array=str_game_timer.split("^");
    var d_timer_data_array=[];
    var colum_array=[];
    var timer_name='';
    var timer_count=0;
    var timer_step=0;
    for(var i=0;i<30;i++){
        timer_name='';
        timer_count=0;
        timer_step=0;
        if(record_array[i]){
            colum_array=record_array[i].split('|');
            if(colum_array[0]){
                timer_name=colum_array[0];
            }
            if(colum_array[1]){
                timer_count=parseInt(colum_array[1]);
            }
            if(colum_array[2]){
                timer_step=parseInt(colum_array[2]);
            }
        }
        d_timer_data_array[i]=[timer_name,timer_count,timer_step];
    }
    if(timer_data_array.toString()!=d_timer_data_array.toString()){
        var active_element=document.activeElement;
        var active_element_id='';
        if(active_element.id!=null){
            active_element_id=active_element.id;
        }
        for(var i=0;i<d_timer_data_array.length;i++){
            timer_name='';
            timer_count=0;
            timer_step=1;
            if(d_timer_data_array[i]){
                if(d_timer_data_array[i][0]){
                    timer_name=d_timer_data_array[i][0];
                }
                if(d_timer_data_array[i][1]){
                    timer_count=parseInt(d_timer_data_array[i][1]);
                }
                if(d_timer_data_array[i][2]){
                    timer_step=parseInt(d_timer_data_array[i][2]);
                }
            }
            if(noneUpdateSettingId!='tmr_'+i){
                if(timer_data_array[i][0]!=d_timer_data_array[i][0]){
                    if(active_element_id!='tmr_nm_'+i){
                        if(document.getElementById('tmr_nm_'+i).value!=timer_name){
                            document.getElementById('tmr_nm_'+i).value=timer_name;
                        }
                    }
                }
                if(timer_data_array[i][1]!=d_timer_data_array[i][1]){
                    if(active_element_id!='tmr_vl_'+i){
                        if(document.getElementById('tmr_vl_'+i).value!=timer_count){
                            document.getElementById('tmr_vl_'+i).value=timer_count;
                            if(timer_count>0){
                                document.getElementById('tmr_nm_'+i).style.color='#000';
                            }else if(timer_count<0){
                                document.getElementById('tmr_nm_'+i).style.color='#F88';
                            }else{
                                document.getElementById('tmr_nm_'+i).style.color='#AAA';
                            }
                        }
                    }
                }
                if(timer_data_array[i][2]!=d_timer_data_array[i][2]){
                    if(active_element_id!='tmr_st_'+i){
                        if(document.getElementById('tmr_st_'+i).value!=timer_step){
                            document.getElementById('tmr_st_'+i).value=timer_step;
                        }
                    }
                }
            }
        }
        timer_data_array=d_timer_data_array;
    }
}
</script>