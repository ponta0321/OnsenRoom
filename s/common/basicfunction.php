<?php 
/*========================================================================
FUNCTION
 function.php と exefunction.php あるいは class から呼び出される関数
========================================================================*/
function changeIDformRoomName($room_name){
/*-------------------------------------------------------------------
-- 機能 --
ルーム名をmd5に変換する

-- バージョン --
v1.00

-- 更新履歴 --
2016.09.01 制作
-------------------------------------------------------------------*/
    return md5($room_name);
}
function escapePHP($str,$charlist="'\\"){
/*-------------------------------------------------------------------
-- 機能 --
PHPデータ作成時のエスケープ処理

-- バージョン --
v1.00

-- 更新履歴 --
2016.10.30 制作
-------------------------------------------------------------------*/
    $charlist="'\\";
    return addcslashes($str,$charlist);
}
function repeat_put_file($file,$data,$flag=LOCK_EX,$context=null,$repeat=3){
/*-------------------------------------------------------------------
-- 機能 --
file_put_contentsを成功するまで繰り返す

-- バージョン --
v1.00

-- 更新履歴 --
2016.06.24 制作
-------------------------------------------------------------------*/
    $result=false;
    $i=0;
    do{
        $result=file_put_contents($file,$data,$flag,$context);
        $i++;
    }while(($i<3)&&($result===false));
    return $result;
}
function changeEvalDataPhpFile($php_file){
/*-------------------------------------------------------------------
-- 機能 --
PHPファイルをeval用の文字列で出力する

-- バージョン --
v1.00

-- 更新履歴 --
2017.01.23 制作
-------------------------------------------------------------------*/
    $eval_data='';
    if(!empty($php_file)){
        if(file_exists($php_file)){
            $eval_data=@file_get_contents($php_file);
            $eval_data=str_replace(array('<?php','?>'),'',$eval_data);
        }
    }
    return $eval_data;
}
function checkAuth($get_code,$auth_code=AUTHENTICATION_CODE){
/*-------------------------------------------------------------------
-- 機能 --
認証コードチェックを行う

-- バージョン --
v1.00

-- 更新履歴 --
2018.02.17 制作
-------------------------------------------------------------------*/
    if($get_code==$auth_code){
        return true;
    }
    return false;
}
function existsFile($file_name){
/*-------------------------------------------------------------------
-- 機能 --
ファイルの存在確認をする

-- バージョン --
v1.00

-- 更新履歴 --
2018.05.21 制作
-------------------------------------------------------------------*/
	if(file_exists($file_name)){
		if(filesize($file_name)>0){
			return true;
		}
	}
    return false;
}
function getClientIP(){
/*-------------------------------------------------------------------
-- 機能 --
クライアントのIPを返す

-- バージョン --
v1.00

-- 更新履歴 --
2018.11.14 制作
-------------------------------------------------------------------*/
	$ip=$_SERVER['REMOTE_ADDR'];
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		if(preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s',$_SERVER['HTTP_X_FORWARDED_FOR'],$matches)){
			foreach($matches[0] as $value){
				if(!preg_match('#^(10|172\.16|192\.168)\.#',$value)){
					$ip=$value;
					break;
				}
			}
		}
	}elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
		if(preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_CLIENT_IP'])){
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
	}elseif(isset($_SERVER['HTTP_CF_CONNECTING_IP'])){
		if(preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_CF_CONNECTING_IP'])){
			$ip=$_SERVER['HTTP_CF_CONNECTING_IP'];
		}
	}elseif(isset($_SERVER['HTTP_X_REAL_IP'])){
		if(preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_X_REAL_IP'])){
			$ip=$_SERVER['HTTP_X_REAL_IP'];
		}
	}
	if(empty($ip)){
		$ip='';
	}
	return $ip;
}