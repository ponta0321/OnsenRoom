<?php
function basicDiceCommand($command,&$dc,&$ds,&$sf){
    $result=0; // 0=該当なし 1=ダイスロール 2=数値のみ 3=エラー(指定できない数:ロール数) 4=エラー(指定できない数:面) 5=エラー(指定できない数:加算値)
    $dc=0;
    $ds=0;
    $sf=0; // 0=ソートしない 1=昇順ソート 2=降順ソート
    $match=array();
    
    if(preg_match('/^([0-9]+?)d([ad]?)([0-9]+)$/i',$command,$match)){
        $dc=(int)$match[1];
        $ds=(int)$match[3];
        $result=1;
        if(($dc<1)||($dc>DICE_ROLL_LIMIT)){
            $dc=0;$ds=0;
            $result=3;
        }elseif(($ds>DICE_SURFACE_LIMIT)||($ds<2)){
            $dc=0;$ds=0;
            $result=4;
        }
        if(($match[2]=='a')||($match[2]=='A')){ // add.2016.11.03
            $sf=1;
        }elseif(($match[2]=='d')||($match[2]=='D')){
            $sf=2;
        }
    }elseif(preg_match('/^([0-9]+)d([ad]?)$/i',$command,$match)){
        $dc=(int)$match[1];
        $ds=6;
        $result=1;
        if(($dc<1)||($dc>DICE_ROLL_LIMIT)){
            $dc=0;$ds=0;
            $result=3;
        }
        if(($match[2]=='a')||($match[2]=='A')){ // add.2016.11.03
            $sf=1;
        }elseif(($match[2]=='d')||($match[2]=='D')){
            $sf=2;
        }
    }elseif(preg_match('/^d([ad]?)([0-9]+)$/i',$command,$match)){
        $dc=1;
        $ds=(int)$match[2];
        $result=1;
        if(($ds>DICE_SURFACE_LIMIT)||($ds<2)){
            $dc=0;$ds=0;
            $result=4;
        }
        if(($match[1]=='a')||($match[1]=='A')){ // add.2016.11.03
            $sf=1;
        }elseif(($match[1]=='d')||($match[1]=='D')){
            $sf=2;
        }
    }elseif(preg_match('/^([0-9]+)$/i',$command,$match)){
        $dc=(int)$match[1];
        $ds=0;
        $result=2;
        if($dc>ADDITION_VALUE_LIMIT){
            $dc=0;
            $result=5;
        }
    }
    return $result;
}
$check_flag=false;
$recheck_flag=false;
$rollcommand=0;
$plus_number=0;
$roll_dice_array=array(); // 0=+- 1=number　2=surface 3=sort
if(!preg_match('/^[0-9]+$/i',$chipcommand)){
    $chipcommand='+'.$chipcommand;
    do{
        $match=array();
        if(preg_match('/^([\-+]{1,2})(.+?)([\-+]{1,2}.+)$/i',$chipcommand,$match)){
            $check_flag=true;
            $recheck_flag=true;
        }elseif(preg_match('/^([\-+]{1,2})(.+)$/i',$chipcommand,$match)){
            $check_flag=true;
            $recheck_flag=false;
        }else{
            $rollcommand=0;
            $check_flag=false;
            $recheck_flag=false;
        }
        if($check_flag==true){
            $count_number=0;$dice_surface=0;$sort_flag=0;
            $c_res=basicDiceCommand((string)$match[2],$count_number,$dice_surface,$sort_flag);
            if($c_res==0){
                $rollcommand=0;
                $recheck_flag=false;
            }elseif($c_res==1){
                $rollcommand=1;
                if(((string)$match[1]=='+')||((string)$match[1]=='++')||((string)$match[1]=='--')){
                    $plusorminus='+';
                }else{
                    $plusorminus='-';
                }
                $roll_dice_array[]=array($plusorminus,$count_number,$dice_surface,$sort_flag);
            }elseif($c_res==2){
                if(((string)$match[1]=='+')||((string)$match[1]=='++')||((string)$match[1]=='--')){
                    $plus_number=$plus_number+$count_number;
                }else{
                    $plus_number=$plus_number-$count_number;
                }
            }elseif($c_res==3){
                $rollcommand=1;
                $result_comment='エラー';
                $command_error_mes='(振る個数は1～'.DICE_ROLL_LIMIT.'で設定してください)';
                $recheck_flag=false;
            }elseif($c_res==4){
                $rollcommand=1;
                $result_comment='エラー';
                $command_error_mes='(面の数は2～'.DICE_SURFACE_LIMIT.'で設定してください)';
                $recheck_flag=false;
            }elseif($c_res==5){
                $rollcommand=1;
                $result_comment='エラー';
                $command_error_mes='(加減算値は1～'.ADDITION_VALUE_LIMIT.'で設定してください)';
                $recheck_flag=false;
            }
        }
        if($recheck_flag==true){
            $chipcommand=(string)$match[3];
        }else{
            break;
        }
    }while(1);
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
            foreach($roll_dice_array as $rd_value){
                if(!empty($rd_value[0])){
                    if($total_one_roll=rollDice($rd_value[1],$rd_value[2],$dice,DICE_ROLL_LIMIT,DICE_SURFACE_LIMIT)){
                        if($rd_value[3]!=0){
                            if(1<count($dice)){
                                foreach($dice as $sort_key => $sort_value){
                                    $sort_result[$sort_key]=(int)$sort_value[1];
                                }
                                if($rd_value[3]==1){
                                    @array_multisort($sort_result,SORT_ASC,SORT_NUMERIC,$dice);
                                }elseif($rd_value[3]==2){
                                    @array_multisort($sort_result,SORT_DESC,SORT_NUMERIC,$dice);
                                }
                                unset($sort_result);
                                unset($sort_value);
                                unset($sort_key);
                            }
                        }
                        for($i=0;$i<count($dice);$i++){
                            if($result_detail_comment!=''){
                                $result_detail_comment.=',';
                            }
                            $result_detail_comment.=$dice[$i][1];
                            $dammy_dice[]=$dice[$i];
                        }
                        if($rd_value[0]=='+'){
                            $total_roll=$total_roll+$total_one_roll;
                        }else{
                            $total_roll=$total_roll-$total_one_roll;
                        }
                        $dice_roll_flag=true;
                    }else{
                        $result_comment='エラー';
                    }
                }
            }
            $total_roll=$total_roll+$plus_number;
            // 判定結果の決定　 1:>= 2:<= 3:<> 4:> 5:< 6:=
            if($result_comment=='エラー'){
                // なにもしない
            }elseif($auto_failure==true){
                $result_comment='自動失敗 ';
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
            if($repeat_flag!=0){
                $comment.='<br>→ '.($count_loop+1).'回目：';
            }
            $comment.=$result_comment.$command_error_mes;;
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
        if($count_number==0){
            $count_number=1;
        }
    }else{
        $comment.=$result_comment.$command_error_mes;;
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