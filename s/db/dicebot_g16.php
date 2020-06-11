<?php
$dmg_rate_array=array();
$dmg_rate_array[0]=array( '*','0','0','0','1','2','2','3','3','4','4');

$dmg_rate_array[1]=array( '*','0','0','0','1','2','3','3','3','4','4');
$dmg_rate_array[2]=array( '*','0','0','0','1','2','3','4','4','4','4');
$dmg_rate_array[3]=array( '*','0','0','1','1','2','3','4','4','4','5');
$dmg_rate_array[4]=array( '*','0','0','1','2','2','3','4','4','5','5');
$dmg_rate_array[5]=array( '*','0','1','1','2','2','3','4','5','5','5');
$dmg_rate_array[6]=array( '*','0','1','1','2','3','3','4','5','5','5');
$dmg_rate_array[7]=array( '*','0','1','1','2','3','4','4','5','5','6');
$dmg_rate_array[8]=array( '*','0','1','2','2','3','4','4','5','6','6');
$dmg_rate_array[9]=array( '*','0','1','2','3','3','4','4','5','6','7');
$dmg_rate_array[10]=array('*','1','1','2','3','3','4','5','5','6','7');

$dmg_rate_array[11]=array('*','1','2','2','3','3','4','5','6','6','7');
$dmg_rate_array[12]=array('*','1','2','2','3','4','4','5','6','6','7');
$dmg_rate_array[13]=array('*','1','2','3','3','4','4','5','6','7','7');
$dmg_rate_array[14]=array('*','1','2','3','4','4','4','5','6','7','8');
$dmg_rate_array[15]=array('*','1','2','3','4','4','5','5','6','7','8');
$dmg_rate_array[16]=array('*','1','2','3','4','4','5','6','7','7','8');
$dmg_rate_array[17]=array('*','1','2','3','4','5','5','6','7','7','8');
$dmg_rate_array[18]=array('*','1','2','3','4','5','6','6','7','7','8');
$dmg_rate_array[19]=array('*','1','2','3','4','5','6','7','7','8','9');
$dmg_rate_array[20]=array('*','1','2','3','4','5','6','7','8','9','10');

$dmg_rate_array[21]=array('*','1','2','3','4','6','6','7','8','9','10');
$dmg_rate_array[22]=array('*','1','2','3','5','6','6','7','8','9','10');
$dmg_rate_array[23]=array('*','2','2','3','5','6','7','7','8','9','10');
$dmg_rate_array[24]=array('*','2','3','4','5','6','7','7','8','9','10');
$dmg_rate_array[25]=array('*','2','3','4','5','6','7','8','8','9','10');
$dmg_rate_array[26]=array('*','2','3','4','5','6','8','8','9','9','10');
$dmg_rate_array[27]=array('*','2','3','4','6','6','8','8','9','9','10');
$dmg_rate_array[28]=array('*','2','3','4','6','6','8','9','9','10','10');
$dmg_rate_array[29]=array('*','2','3','4','6','7','8','9','9','10','10');
$dmg_rate_array[30]=array('*','2','4','4','6','7','8','9','10','10','10');

$dmg_rate_array[31]=array('*','2','4','5','6','7','8','9','10','10','11');
$dmg_rate_array[32]=array('*','3','4','5','6','7','8','10','10','10','11');
$dmg_rate_array[33]=array('*','3','4','5','6','8','8','10','10','10','11');
$dmg_rate_array[34]=array('*','3','4','5','6','8','9','10','10','11','11');
$dmg_rate_array[35]=array('*','3','4','5','7','8','9','10','10','11','12');
$dmg_rate_array[36]=array('*','3','5','5','7','8','9','10','11','11','12');
$dmg_rate_array[37]=array('*','3','5','6','7','8','9','10','11','12','12');
$dmg_rate_array[38]=array('*','3','5','6','7','8','10','10','11','12','13');
$dmg_rate_array[39]=array('*','4','5','6','7','8','10','11','11','12','13');
$dmg_rate_array[40]=array('*','4','5','6','7','9','10','11','11','12','13');

$dmg_rate_array[41]=array('*','4','6','6','7','9','10','11','12','12','13');
$dmg_rate_array[42]=array('*','4','6','7','7','9','10','11','12','13','13');
$dmg_rate_array[43]=array('*','4','6','7','8','9','10','11','12','13','14');
$dmg_rate_array[44]=array('*','4','6','7','8','10','10','11','12','13','14');
$dmg_rate_array[45]=array('*','4','6','7','9','10','10','11','12','13','14');
$dmg_rate_array[46]=array('*','4','6','7','9','10','10','12','13','13','14');
$dmg_rate_array[47]=array('*','4','6','7','9','10','11','12','13','13','15');
$dmg_rate_array[48]=array('*','4','6','7','9','10','12','12','13','13','15');
$dmg_rate_array[49]=array('*','4','6','7','10','10','12','12','13','14','15');
$dmg_rate_array[50]=array('*','4','6','8','10','10','12','12','13','15','15');

if(preg_match('/^k([0-9]+?)([\-+]{1,2})([0-9]+?)@([0-9]+)$/i',$chipcommand,$match)){
    $rollcommand=2;
    $dmg_rate=(int)$match[1];
    $count_number=2;
    $dice_surface=6;
    if(((string)$match[2]=='+')||((string)$match[2]=='++')||((string)$match[2]=='--')){
        $plus_number=(int)$match[3];
    }else{
        $plus_number=-(int)$match[3];
    }
    $critical_rate=(int)$match[4];
    $special_method=true;
}elseif(preg_match('/^k([0-9]+?)@([0-9]+)$/i',$chipcommand,$match)){
    $rollcommand=2;
    $dmg_rate=(int)$match[1];
    $count_number=2;
    $dice_surface=6;
    $plus_number=false;
    $critical_rate=(int)$match[2];
    $special_method=true;
}elseif(preg_match('/^k([0-9]+?)([\-+]{1,2})([0-9]+)$/i',$chipcommand,$match)){
    $rollcommand=2;
    $dmg_rate=(int)$match[1];
    $count_number=2;
    $dice_surface=6;
    if(((string)$match[2]=='+')||((string)$match[2]=='++')||((string)$match[2]=='--')){
        $plus_number=(int)$match[3];
    }else{
        $plus_number=-(int)$match[3];
    }
    $critical_rate=13;
    $special_method=true;
}elseif(preg_match('/^k([0-9]+)$/i',$chipcommand,$match)){
    $rollcommand=2;
    $dmg_rate=(int)$match[1];
    $count_number=2;
    $dice_surface=6;
    $plus_number=false;
    $critical_rate=13;
    $special_method=true;
}
if($rollcommand!=0){
    $roll_first_comment=$call_name.'さんの'.$chipcomment.'ロール('.$typecommand.') → ';
    $dammy_dice=array();
    $comment='';
    // ロール処理
    if($result_comment!='エラー'){
        $dice_result=0;
        $count_loop=0;
        do{
            $result_detail_comment='';
            $result_comment='';
            $total_roll=0;
            $auto_failure=false;
            $dice=array();
            $roll_count=0;
            $temp_dice=array();
            if($critical_rate<3){
                $result_comment='エラー';
                $command_error_mes='(C値は3～12で設定してください)';
            }elseif(!empty($dmg_rate_array[$dmg_rate][0])){
                while(1){
                    $roll_count++;
                    $decision_roll=rollDice($count_number,$dice_surface,$dice,DICE_ROLL_LIMIT,DICE_SURFACE_LIMIT);
                    $temp_dice[]=$dice[0];
                    $temp_dice[]=$dice[1];
                    if($decision_roll==2){
                        break;
                    }elseif($decision_roll<$critical_rate){
                        $total_roll=$total_roll+$dmg_rate_array[$dmg_rate][($decision_roll-2)];
                        break;
                    }else{
                        $total_roll=$total_roll+$dmg_rate_array[$dmg_rate][($decision_roll-2)];
                    }
                }
                if(($roll_count==1)&&($decision_roll==2)){
                    $auto_failure=true;
                }
                $i=0;
                foreach($temp_dice as $value){
                    if($i!=0){
                        $result_detail_comment.=',';
                    }
                    $result_detail_comment.=$value[1];
                    $dammy_dice[]=$value;
                    $i++;
                }
                if($plus_number!==false){
                    $total_roll=$total_roll+$plus_number;
                }
                $dice_roll_flag=true;
            }else{
                $result_comment='エラー';
                $command_error_mes='(威力値は0～50で設定してください)';
            }
            // 判定結果の決定　 1:>= 2:<= 3:<> 4:> 5:< 6:=
            if($result_comment=='エラー'){
                // なにもしない
            }elseif($auto_failure==true){
                $result_comment='自動失敗 ';
            }else{
                $result_comment=$total_roll;
            }
            // コメント作成
            /*
            if($count_loop>0){
                $comment.='、';
            }*/
            if($repeat_flag!=0){
                $comment.='<br>→ '.($count_loop+1).'回目：';
            }
            $comment.=$result_comment.$command_error_mes;
            if(!empty($result_detail_comment)){
                $comment.=' ('.$result_detail_comment.')';
            }
            // ループ関係処理
            if($count_loop==0){
                $dice_result=abs($total_roll);
            }
            $count_loop++;
            if($result_comment==='エラー'){
                break;
            }
        }while($count_loop<$repeat_flag);
    }else{
        $comment.=$result_comment.$command_error_mes;
    }
    // コメント完成
    $comment=$roll_first_comment.$comment;
    $dice=array();
    $dammy_dice_count=0;
    foreach($dammy_dice as $dd_value){
        $dice[$dammy_dice_count]=$dd_value;
        $dammy_dice_count++;
    }
    $system_comment_flag=true;
}