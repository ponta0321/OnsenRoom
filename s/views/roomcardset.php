<div class="room_setting_box">
    <p><label>初期配置の間隔</label></p>
    <p>
        <select id="default_card_position_select" style="padding:2px 4px;min-width:80px;font-size:10px;">
           <option value="0" selected="selected">狭い</option>
           <option value="1">やや広い</option>
           <option value="2">広い</option>
        </select>&nbsp;
        <input type="button" value="初期位置に再配置する" style="padding:0 4px;font-size:10px;" onClick="relocationCardPosition();" />
    </p>
    <p class="m_top_1em"><label>カードセットの間隔</label></p>
    <p>幅:&nbsp;<input id="cardset_position_x" type="number" min="0" max="9999" value="30" style="padding:2px 4px;width:40px;font-size:10px;" />&nbsp;
       高さ:&nbsp;<input id="cardset_position_y" type="number" min="0" max="9999" value="30" style="padding:2px 4px;width:40px;font-size:10px;" />&nbsp;
    <input type="button" value="セット" style="padding:0 4px;font-size:10px;" onClick="setDefaultCardPosition();" /></p>
</div>
<?php
    for($cardset_num=1;$cardset_num<5;$cardset_num++){
?>
<div class="room_setting_box_wb">
    <p><label>カードセット<?=$cardset_num;?></label></p>
    <?php
        $cardset_url='';
        if(!empty($xml->head->{'cardset'.$cardset_num}->url)){
            $cardset_url=(string)$xml->head->{'cardset'.$cardset_num}->url;
        }
    ?>
    <input id="game_cardset<?=$cardset_num;?>" type="hidden" value="<?=$cardset_url;?>" />
    <p><select id="game_cardset<?=$cardset_num;?>_select" style="padding:2px 4px;width:140px;font-size:10px;">
        <?php
            $csl_array=$cardset_list_array;
            $uploaded_file_flag=false;
            foreach(glob(DIR_ROOT.'r/n/'.$base_room_file.'/uploadcardset'.$cardset_num.'*') as $value){
                $uploadimage_url=str_replace(DIR_ROOT,URL_ROOT,$value);
                $uploaded_file_flag=true;
                break;
            }
            if($uploaded_file_flag==true){
                $csl_array[]=array($uploadimage_url,'アップロードデータ');
            }else{
                $csl_array[]=array('_nothing_cardset_','アップロードデータ');
            }
            $str_backimage_option='';
            foreach($csl_array as $value){
                if($value[1]=='アップロードデータ'){
                    if($uploaded_file_flag==true){
                        if($value[0]==$cardset_url){
                            $str_backimage_option.='<option id="id_uploadcardset'.$cardset_num.'_o" value="'.$value[0].'" selected="selected">アップロードデータ</option>';
                        }else{
                            $str_backimage_option.='<option id="id_uploadcardset'.$cardset_num.'_o" value="'.$value[0].'" >アップロードデータ</option>';
                        }
                    }else{
                        $str_backimage_option.='<option id="id_uploadcardset'.$cardset_num.'_o" value="_nothing_cardset_" disabled="disabled" >アップロードデータ</option>';
                    }
                }elseif($value[0]==$cardset_url){
                    $str_backimage_option.='<option value="'.$value[0].'" selected="selected">'.$value[1].'</option>';
                }else{
                    $str_backimage_option.='<option value="'.$value[0].'">'.$value[1].'</option>';
                }
            }
            echo $str_backimage_option;
        ?>
    </select>&nbsp;
    <input type="button" value="セット＆初期化" style="padding:0 4px;font-size:10px;" onClick="setCardset<?=$cardset_num;?>();" /></p>
    <form id="upload_cardset<?=$cardset_num;?>_form" style="margin-top:5px;" enctype="multipart/form-data" method="post" action="<?=$global_page_url;?>">
        <p><label>カードセットのアップロード<span class="font_blue small"><?=displaySimpleByte(UPLOAD_CS_LIMIT_SIZE);?>までのXML）</span></label></p>
        <p><input type="file" name="upload_cardset" accept="application/xml" style="font-size:10px;" /></p>
        <p><input type="button" name="submit_button" onClick="uploadBgiFile('upload_cardset<?=$cardset_num;?>_form');" style="margin-top:10px;padding:0 4px;font-size:10px;" value="アップロード＆セット＆初期化" /></p>
        <input type="hidden" name="upload_state" value="cardset<?=$cardset_num;?>" />
        <input type="hidden" name="room_file" value="<?=$base_room_file;?>" />
        <?php if($observer_flag!=1){ ?> 
        <input type="hidden" name="room_pass" value="<?=$room_pass;?>" />
        <?php } ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=UPLOAD_CS_LIMIT_SIZE;?>" />
    </form>
</div>
<script>
function setCardset<?=$cardset_num;?>(){
	document.getElementById('game_cardset<?=$cardset_num;?>').value=document.getElementById('game_cardset<?=$cardset_num;?>_select').value;
	setModSettingData('game_cardset<?=$cardset_num;?>');
}
</script>
<?php
    }
?>