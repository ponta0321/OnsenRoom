<?php
class classBCDiceAPIv2{
	function curl($url,$post_array=null){
		try{
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			if(is_array($post_array)){
				$post_count=0;
				$post_fields='';
				foreach($post_array as $key => $value){
					if(empty($key)) continue;
					if($post_count>0){
						$post_fields.='&';
					}
					$post_fields.=urlencode($key).'='.urlencode($value);
					$post_count++;
				}
				curl_setopt($ch,CURLOPT_POST,$post_count);
				curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
			}
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			$result=curl_exec($ch);
			curl_close($ch);
			if($result===false) return false;
			return json_decode($result,true);
		}catch(Exception $e){
			return false;
		}
	}
	function normalizEP($url){
		return trim(rtrim($url,'/'));
	}
	function list($ep){
		return self::curl(self::normalizEP($ep).'/v2/game_system');
	}
	function info($ep,$id){
		return self::curl(self::normalizEP($ep).'/v2/game_system/'.$id);
	}
	function diceRoll($ep,$id,$command){
		return self::curl(self::normalizEP($ep).'/v2/game_system/'.$id.'/roll',array('command'=>$command));
	}
	function table($ep,$id,$table){
		return self::curl(self::normalizEP($ep).'/v2/original_table',array('table'=>$table));
	}
}