<?php
if(preg_match('/^([0-9]+?)dx([0-9]+?)([\-+]{1,2})([0-9]+)$/i',$chipcommand,$match)){
    $rollcommand=2;
    $count_number=(int)$match[1];
    $dice_surface=10;
    $critical_rate=(int)$match[2];
    if(((string)$match[3]=='+')||((string)$match[3]=='++')||((string)$match[3]=='--')){
        $plus_number=(int)$match[4];
    }else{
        $plus_number=-(int)$match[4];
    }
}elseif(preg_match('/^([0-9]+?)dx([0-9]+)$/i',$chipcommand,$match)){
    $rollcommand=2;
    $count_number=(int)$match[1];
    $dice_surface=10;
    $critical_rate=(int)$match[2];
    $plus_number=false;
}elseif(preg_match('/^([0-9]+?)dx([\-+]{1,2})([0-9]+)$/i',$chipcommand,$match)){
    $rollcommand=2;
    $count_number=(int)$match[1];
    $dice_surface=10;
    $critical_rate=11;
    if(((string)$match[2]=='+')||((string)$match[2]=='++')||((string)$match[2]=='--')){
        $plus_number=(int)$match[3];
    }else{
        $plus_number=-(int)$match[3];
    }
}elseif(preg_match('/^([0-9]+?)dx$/i',$chipcommand,$match)){
    $rollcommand=2;
    $count_number=(int)$match[1];
    $dice_surface=10;
    $critical_rate=11;
    $plus_number=false;
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
            $dammy_count_number=$count_number;
            if($critical_rate<=1){
                $result_comment='エラー';
                $command_error_mes='(クリティカル値は2～10で設定してください)';
            }elseif($count_number>DICE_ROLL_LIMIT){
                $result_comment='エラー';
                $command_error_mes='(振る個数は1～'.DICE_ROLL_LIMIT.'で設定してください)';
            }else{
                while(1){
                    $critical_check=0;
                    $max_rate=0;
                    if($dammy_count_number>=1){
                        $roll_count++;
                        $decision_roll=rollDice($dammy_count_number,$dice_surface,$dice,DICE_ROLL_LIMIT,DICE_SURFACE_LIMIT);
                        foreach($dice as $value){
                            $dammy_dice[]=$value;
                            // クリティカルチェック
                            if($critical_rate<=$value[1]){
                                $critical_check++;
                                $max_rate=10;
                            }
                            // 最大値チェック
                            if($max_rate<$value[1]){
                                $max_rate=$value[1];
                            }
                        }
                    }
                    $total_roll=$total_roll+$max_rate;
                    if($result_detail_comment!=''){
                        $result_detail_comment.=',';
                    }
                    $result_detail_comment.=$max_rate.'[';
                    $i=0;
                    foreach($dice as $value){
                        if($i!=0){
                            $result_detail_comment.=',';
                        }
                        $result_detail_comment.=$value[1];
                        $i++;
                    }
                    $result_detail_comment.=']';
                    if($critical_check<1){
                        break;
                    }else{
                        $dammy_count_number=$critical_check;
                    }
                }
                if($plus_number!==false){
                    $total_roll=$total_roll+$plus_number;
                }
                if(($roll_count<=0)){
                    $auto_failure=true;
                    $total_roll=0;
                }elseif(($roll_count<=1)&&($max_rate<=1)){
                    $auto_failure=true;
                    $total_roll=0;
                }
                $dice_roll_flag=true;
            }
            // 判定結果の決定　 1:>= 2:<= 3:<> 4:> 5:< 6:=
            if($result_comment=='エラー'){
                // なにもしない
            }elseif($auto_failure==true){
                $result_comment='ファンブル ';
            }elseif($success_or_failure_flag==1){
                if($total_roll>=$success_or_failure_value){
                    $result_comment='成功 '.$total_roll;
                }else{
                    $result_comment='失敗 '.$total_roll;
                }
            }elseif($success_or_failure_flag==2){
                if($total_roll<=$success_or_failure_value){
                    $result_comment='成功 '.$total_roll;
                }else{
                    $result_comment='失敗 '.$total_roll;
                }
            }elseif($success_or_failure_flag==3){
                if($total_roll!=$success_or_failure_value){
                    $result_comment='成功 '.$total_roll;
                }else{
                    $result_comment='失敗 '.$total_roll;
                }
            }elseif($success_or_failure_flag==4){
                if($total_roll>$success_or_failure_value){
                    $result_comment='成功 '.$total_roll;
                }else{
                    $result_comment='失敗 '.$total_roll;
                }
            }elseif($success_or_failure_flag==5){
                if($total_roll<$success_or_failure_value){
                    $result_comment='成功 '.$total_roll;
                }else{
                    $result_comment='失敗 '.$total_roll;
                }
            }elseif($success_or_failure_flag==6){
                if($total_roll==$success_or_failure_value){
                    $result_comment='成功 '.$total_roll;
                }else{
                    $result_comment='失敗 '.$total_roll;
                }
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