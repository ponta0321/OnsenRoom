<div style="margin:5px 0 10px 0;text-align:right;">
    <input type="button" id="all_reset" value="全て消去" onClick="putChessmanReset(0);" />&nbsp;
    <input type="button" id="chessman_reset" value="コマ全消去" onClick="putChessmanReset(1);" />&nbsp;
    <input type="button" id="marking_reset" value="マーキング全消去" onClick="putChessmanReset(2);" />
</div>
<?php 
    foreach($chessman_list as $tag_name => $tag_value){
        echo '<div id="cm_tag_'.urlencode($tag_name).'_button" class="chessma_box_ac" onClick="putAcChessMan(\'cm_tag_'.urlencode($tag_name).'_button\',\'cm_tag_'.urlencode($tag_name).'_box\',\''.$tag_name.'\');">'.$tag_name.'&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）</div>';
        echo '<div id="cm_tag_'.urlencode($tag_name).'_box" class="chessman_box">';
        foreach($tag_value as $value){
            if(!empty($value[8])){
                echo '<div class="chessrangecup_plate"><div class="chessrange_cup">';
            }else{
                echo '<div class="chessman_cup">';
            }
            echo '<img id="'.$value[1].'" src="'.$value[0].'" class="chessman" draggable="true" width="'.$value[3].'" height="'.$value[4].'" style="top:'.$value[6].'px;left:'.$value[5].'px;" border="0" '.(empty($value[7])?'':'alt="'.$value[7].'"').' /></div>';
            if(!empty($value[8])){
                echo $value[8].'</div>';
            }
        }
        echo '</div>';
    }
?>
<script>
    function putAcChessMan(tag_button_id,tag_box_id,tag_name){
        if(document.getElementById(tag_box_id).style.display=="none"){
            document.getElementById(tag_box_id).style.display="block";
            document.getElementById(tag_button_id).innerHTML=tag_name+'&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）';
        }else{
            document.getElementById(tag_box_id).style.display="none";
            document.getElementById(tag_button_id).innerHTML=tag_name+'&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）';
        }
    }
    <?php
        foreach($chessman_list as $tag_name => $tag_value){
            echo 'putAcChessMan(\'cm_tag_'.urlencode($tag_name).'_button\',\'cm_tag_'.urlencode($tag_name).'_box\',\''.$tag_name.'\');';
        }
    ?>
</script>