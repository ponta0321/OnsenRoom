<?php
    $chessman_box_width=52;
    $range_color=array('r','b','g');
    $range_name=array(array('角','s'),array('菱',''));
    $chessman_count=0;
    $html='';
    foreach($chessman_list as $tag_name => $tag_value){
        foreach($tag_value as $value){
            if(!empty($value[8])){
                $html.='<div class="chessrangecup_plate"><div class="chessrange_cup">';
            }else{
                $html.='<div class="chessman_cup">';
            }
            $html.='<img id="'.$value[1].'" src="'.$value[0].'" class="chessman" draggable="true" width="'.$value[3].'" height="'.$value[4].'" style="top:'.$value[6].'px;left:'.$value[5].'px;" border="0" '.(empty($value[7])?'':'alt="'.$value[7].'"').' /></div>';
            if(!empty($value[8])){
                $html.=$value[8].'</div>';
                $chessman_box_width=$chessman_box_width+64;
            }else{
                $chessman_box_width=$chessman_box_width+52;
            }
            $chessman_count++;
        }
    }
    $html='<div class="chessman_box_slider"><div class="chessman_box" style="width:'.$chessman_box_width.'px;">'.$html;
    $html.='</div></div>';
    echo $html;