<?php 
/*========================================================================
CLASS
========================================================================*/
class classFileLock{
    // パラメータ
    var $base_name='lockfile';
    var $try_time=10;
    var $time_out=60;
    var $current_name='lockfile';
    // コンストラクト
    function __construct($dir=DIR_ROOT,$b_n='lockfile',$t_t=10,$t_o=60){
        $this->base_name=$b_n;
        $this->try_time=$t_t;
        $this->time_out=$t_o;
        $this->createlfile($dir);
    }
    // メソッド
	public function flock($dir){
        $dir=rtrim($dir,'/').'/';
	    for($i=0;$i<$this->try_time;$i++){
            $this->current_name=$this->base_name.time();
			if(!file_exists($dir)){
				return false;
			}
	        if(@rename($dir.$this->base_name,$dir.$this->current_name)){
	            return true;
	        }else{
                sleep(1);
            }
	    }
	    $file_list=glob($dir.$this->base_name.'*',GLOB_NOSORT);
	    foreach($file_list as $file){
	        if(preg_match('/^'.preg_quote($dir.$this->base_name,'/').'([0-9]+)/',$file,$matches)){
	            if((time()-(int)$matches[1])>$this->time_out){
                    $this->current_name=$this->base_name.time();
					if(@rename($matches[0],$dir.$this->current_name)){
                        return true;
                    }
                }
	            break;
	        }
	    }
	    return false;
	}
	public function unflock($dir){
        $dir=rtrim($dir,'/').'/';
	    return @rename($dir.$this->current_name,$dir.$this->base_name);
	}
	public function createlfile($dir){
        $dir=rtrim($dir,'/').'/';
		if(!file_exists($dir)){
			return false;
		}
	    if(count(glob($dir.$this->base_name.'*',GLOB_NOSORT))==0){
            if(!touch($dir.$this->base_name)){
                return false;
            }
        }
        return true;
	}
}
class classCharacter{
    // パラメータ
    public $room_id='';
    public $id='';
    public $owner_id='';
    public $registration_date=0;
    public $update_time=0;
    public $game_type='';
    public $name='';
    public $image='';
    public $hp='';
    public $mhp='';
    public $mp='';
    public $mmp='';
    public $condition='';
    public $detail_a='';
    public $detail_b='';
    public $detail_c='';
    public $detail_x='';
    public $macro='';
    public $designated=0;
    public $tag='';
    public $mass='';
    public $outer_url=''; // 外部URL
    public $stand=array(); // 立ち絵  0=name 1=img 2=width 3=height
    public $stand_image='';
    public $created_in_room=0;
    
    // コンストラクト
    function __construct(){
       $this->update_time=time();
    }
    function getParamArray(){
        return array(array('char_id','id'),
                     array('owner_id','owner_id'),
                     array('registration_date','registration_date'),
                     array('update_time','update_time'),
                     array('game_type','game_type'),
                     array('char_name','name'),
                     array('char_image','image'),
                     array('char_hp','hp'),
                     array('char_mhp','mhp'),
                     array('char_mp','mp'),
                     array('char_mmp','mmp'),
                     array('char_memo','condition'),
                     array('detail_a','detail_a'),
                     array('detail_b','detail_b'),
                     array('detail_c','detail_c'),
                     array('macro','macro'),
                     array('designated','designated'),
                     array('tag','tag'),
                     array('outer_url','outer_url'),
                     array('stand_image','stand_image'),
                     array('created_in_room','created_in_room'));
    }
    function getParam($param_tag,$param_value){
        $change_word=array(array('<','＜'),
                           array('>','＞'),
                           array('&','＆'),
                           array("'",'’'),
                           array('\\','￥'));
        foreach($change_word as $cw_value){
            $param_value=str_replace($cw_value[0],$cw_value[1],$param_value);
        }
        $param_value=preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '',$param_value); // add. 2016.10.06
        if($param_tag==='room_id'){
            return $this->room_id=htmlentities($param_value);
        }elseif($param_tag==='id'){
            return $this->id=htmlentities($param_value);
        }elseif($param_tag==='owner_id'){
            return $this->owner_id=htmlentities($param_value);
        }elseif($param_tag==='registration_date'){
            return $this->registration_date=(int)$param_value;
        }elseif($param_tag==='update_time'){
            return $this->update_time=(int)$param_value;
        }elseif($param_tag==='game_type'){
            return $this->game_type=$param_value;
        }elseif($param_tag==='name'){
            return $this->name=$param_value;
        }elseif($param_tag==='image'){
            return $this->image=htmlentities($param_value);
        }elseif($param_tag==='hp'){
            return $this->hp=$param_value;
        }elseif($param_tag==='mhp'){
            return $this->mhp=$param_value;
        }elseif($param_tag==='mp'){
            return $this->mp=$param_value;
        }elseif($param_tag==='mmp'){
            return $this->mmp=$param_value;
        }elseif($param_tag==='condition'){
            return $this->condition=$param_value;
        }elseif($param_tag==='detail_a'){
            return $this->detail_a=$param_value;
        }elseif($param_tag==='detail_b'){
            return $this->detail_b=$param_value;
        }elseif($param_tag==='detail_c'){
            return $this->detail_c=$param_value;
        }elseif($param_tag==='detail_x'){
            return $this->detail_x=$param_value;
        }elseif($param_tag==='macro'){
            $str=str_replace('$','',$param_value);
            $str=str_replace('＄','',$str);
            return $this->macro=$str;
        }elseif($param_tag==='designated'){
            return $this->designated=(int)$param_value;
        }elseif($param_tag==='tag'){
            return $this->tag=$param_value;
        }elseif($param_tag==='outer_url'){
            return $this->outer_url=$param_value;
        }elseif($param_tag==='created_in_room'){
            return $this->created_in_room=(int)$param_value;
        }
    }
    function updateRegistrationData(){
        $now_time=time();
        $this->registration_date=$now_time;
        return $this->update_time=$now_time;
    }
    function updateTime(){
        return $this->update_time=time();
    }
    function getTags(){
        $tags_array=$this->getTagArray();
        $details=array(str_replace('　',' ',str_replace('：',':',$this->detail_a)),
                       str_replace('　',' ',str_replace('：',':',$this->detail_b)),
                       str_replace('　',' ',str_replace('：',':',$this->detail_c)),
                       str_replace('　',' ',str_replace('：',':',$this->detail_x)));
        foreach($details as $detail_one){
            $match=array();
            if(preg_match_all('/\[(.+?):(.*?)\]/u',$detail_one,$match)){
                for($i=0;$i<count($match[0]);$i++){
                    $tag_name=trim($match[1][$i]);
                    $tag_name=str_replace(' ','',$tag_name);
                    $tag_name=str_replace('$','',$tag_name);
                    $tag_name=str_replace('＄','',$tag_name);
                    $tag_name=str_replace('#','',$tag_name);
                    $tag_name=str_replace('＃','',$tag_name);
                    $tag_value=trim($match[2][$i]);
                    $tag_value=str_replace('$','',$tag_value);
                    $tag_value=str_replace('＄','',$tag_value);
                    $tag_value=str_replace('#','',$tag_value);
                    $tag_value=str_replace('＃','',$tag_value);
                    $uncharted_tag=true;
                    foreach($tags_array as $t_key => $t_value){
                        if($t_value[0]==$tag_name){
                            $tags_array[$t_key][1]=$tag_value;
                            $uncharted_tag=false;
                            break;
                        }
                    }
                    if($uncharted_tag==true){
                        $tags_array[]=array($tag_name,$tag_value);
                    }
                }
            }
        }
        $str_tags='';
        foreach($tags_array as $t_value){
            if((!empty($t_value[0]))&&(isset($t_value[1]))){
                if($str_tags!=''){
                    $str_tags.='^';
                }
                $str_tags.=$t_value[0].'|'.$t_value[1];
            }
        }
        return $this->tag=$str_tags;
    }
    function editTags($target,$modi,$flag=0){
        // $flag: 0=編集 1=削除
        $tags_array=array();
        $tags_rowarray=explode('^',(string)$this->tag);
        for($i=0;$i<count($tags_rowarray);$i++){
            $tag_colarray=explode('|',$tags_rowarray[$i]);
            $tag_name='';
            if(isset($tag_colarray[0])){
                $tag_name=$tag_colarray[0];
            }
            $tag_value='';
            if(isset($tag_colarray[1])){
                $tag_value=$tag_colarray[1];
            }
            if((isset($tag_name))&&(isset($tag_value))){
                $tags_array[]=array($tag_name,$tag_value);
            }
        }
        foreach($tags_array as $t_key => $t_value){
            if($t_value[0]==$target){
                if($flag==0){
                    $tags_array[$t_key][1]=$modi;
                }elseif($flag==1){
                    unset($tags_array[$t_key]);
                }
            }
        }
        $str_tags='';
        foreach($tags_array as $t_value){
            if((isset($t_value[0]))&&(isset($t_value[1]))){
                if($str_tags!=''){
                    $str_tags.='^';
                }
                $str_tags.=$t_value[0].'|'.$t_value[1];
            }
        }
        return $this->tag=$str_tags;
    }
    function getTagArray(){
        $tags_array=array();
        $tags_rowarray=explode('^',(string)$this->tag);
        for($i=0;$i<count($tags_rowarray);$i++){
            $tag_colarray=explode('|',$tags_rowarray[$i]);
            $tag_name='';
            if(isset($tag_colarray[0])){
                $tag_name=$tag_colarray[0];
            }
            $tag_value='';
            if(isset($tag_colarray[1])){
                $tag_value=$tag_colarray[1];
            }
            if((isset($tag_name))&&(isset($tag_value))){
                $tags_array[]=array($tag_name,$tag_value);
            }
        }
        return $tags_array;
    }
    function getMasses(){
        $str_masses='';
        $details=array(str_replace('　',' ',str_replace('：',':',$this->detail_a)),str_replace('　',' ',str_replace('：',':',$this->detail_b)),str_replace('　',' ',str_replace('：',':',$this->detail_c)));
        foreach($details as $detail_one){
            $match=array();
            if(preg_match_all('/\{(.+?)\}([\s\S]+?)\{\/\}/u',$detail_one,$match)){
                for($i=0;$i<count($match[0]);$i++){
                    $mass_name=trim($match[1][$i]);
                    $mass_name=str_replace(' ','',$mass_name);
                    $mass_name=str_replace('$','',$mass_name);
                    $mass_name=str_replace('＄','',$mass_name);
                    $mass_name=str_replace('#','',$mass_name);
                    $mass_name=str_replace('＃','',$mass_name);
                    $mass_value=trim($match[2][$i]);
                    $mass_value=str_replace('$','',$mass_value);
                    $mass_value=str_replace('＄','',$mass_value);
                    $mass_value=str_replace('#','',$mass_value);
                    $mass_value=str_replace('＃','',$mass_value);
                    if((isset($mass_name))&&(isset($mass_value))){
                        if($str_masses!=''){
                            $str_masses.='^';
                        }
                        $str_masses.=$mass_name.'|'.$mass_value;
                    }
                }
            }
        }
        return $this->mass=$str_masses;
    }
    // 新しいキャラ画像のファイル名を生成する
    function createFileName($owner_id=''){
        if(empty($owner_id)){
            if(empty($this->owner_id)){
                $owner_id='owner_unknown';
            }else{
                $owner_id=$this->owner_id;
            }
        }
        return 'p/'.basename($owner_id).'/i/chara'.((time()*100)+mt_rand(0,99));
    }
    // キャラクター保存先
    function getCharDir($room_id='',$owner_id='',$dir_root=DIR_ROOT){
        if(empty($room_id)){
            if(empty($this->room_id)){
                $room_id='room_unknown';
            }else{
                $room_id=$this->room_id;
            }
        }
        if(empty($owner_id)){
            if(empty($this->owner_id)){
                $owner_id='owner_unknown';
            }else{
                $owner_id=$this->owner_id;
            }
        }
        return $dir_root.'r/n/'.basename($room_id).'/p/'.basename($owner_id).'/c/';
    }
    // キャラクター画像保存先
    function getImageDir($room_id='',$owner_id='',$dir_root=DIR_ROOT){
        if(empty($room_id)){
            if(empty($this->room_id)){
                $room_id='room_unknown';
            }else{
                $room_id=$this->room_id;
            }
        }
        if(empty($owner_id)){
            if(empty($this->owner_id)){
                $owner_id='owner_unknown';
            }else{
                $owner_id=$this->owner_id;
            }
        }
        return $dir_root.'r/n/'.basename($room_id).'/p/'.basename($owner_id).'/i/';
    }
    // ディレクトリの確認／作成
    function createSaveDir($save_dir){
        $result=true;
        if(!is_dir($save_dir)){
            $result=mkdir($save_dir,0755,true);
        }
        return $result;
    }
    // ロビーサーバーから取得したキャラクター情報をキャラクタークラスに格納する
    function getDataFromLobby($room_id,$char_data_from_lobby_server){
        if(!empty($room_id)){
            $this->room_id=(string)$room_id;
        }
        if(!empty($char_data_from_lobby_server['char_id'])){
            $this->id=(string)$char_data_from_lobby_server['char_id'];
        }
        if(!empty($char_data_from_lobby_server['owner_id'])){
            $this->owner_id=(string)$char_data_from_lobby_server['owner_id'];
        }
        if(!empty($char_data_from_lobby_server['registration_date'])){
            $this->registration_date=(int)$char_data_from_lobby_server['registration_date'];
        }
        if(!empty($char_data_from_lobby_server['update_time'])){
            $this->update_time=(int)$char_data_from_lobby_server['update_time'];
        }
        if(!empty($char_data_from_lobby_server['game_type'])){
            $this->game_type=(string)$char_data_from_lobby_server['game_type'];
        }
        if(!empty($char_data_from_lobby_server['char_name'])){
            $this->name=(string)$char_data_from_lobby_server['char_name'];
        }
        if(!empty($char_data_from_lobby_server['char_image'])){
            $this->image=(string)$char_data_from_lobby_server['char_image'];
        }
        if(!empty($char_data_from_lobby_server['char_hp'])){
            $this->hp=(string)$char_data_from_lobby_server['char_hp'];
        }
        if(!empty($char_data_from_lobby_server['char_mhp'])){
            $this->mhp=(string)$char_data_from_lobby_server['char_mhp'];
        }
        if(!empty($char_data_from_lobby_server['char_mp'])){
            $this->mp=(string)$char_data_from_lobby_server['char_mp'];
        }
        if(!empty($char_data_from_lobby_server['char_mmp'])){
            $this->mmp=(string)$char_data_from_lobby_server['char_mmp'];
        }
        if(!empty($char_data_from_lobby_server['char_memo'])){
            $this->condition=(string)$char_data_from_lobby_server['char_memo'];
        }
        if(!empty($char_data_from_lobby_server['detail_a'])){
            $this->detail_a=(string)$char_data_from_lobby_server['detail_a'];
        }
        if(!empty($char_data_from_lobby_server['detail_b'])){
            $this->detail_b=(string)$char_data_from_lobby_server['detail_b'];
        }
        if(!empty($char_data_from_lobby_server['detail_c'])){
            $this->detail_c=(string)$char_data_from_lobby_server['detail_c'];
        }
        if(!empty($char_data_from_lobby_server['macro'])){
            $this->macro=(string)$char_data_from_lobby_server['macro'];
        }
        if(!empty($char_data_from_lobby_server['designated'])){
            $this->designated=(string)$char_data_from_lobby_server['designated'];
        }
        if(!empty($char_data_from_lobby_server['tag'])){
            $this->tag=(string)$char_data_from_lobby_server['tag'];
        }
        if(!empty($char_data_from_lobby_server['outer_url'])){
            $this->outer_url=(string)$char_data_from_lobby_server['outer_url'];
        }
        if(!empty($char_data_from_lobby_server['stand_image'])){
            $this->stand_image=(string)$char_data_from_lobby_server['stand_image'];
            $this->stand=$this->standStringToArray((string)$char_data_from_lobby_server['stand_image']);
        }
    }
    // ロビーサーバーから取得したキャラクター情報を保存する。
    function saveDataFromLobby($room_id,$char_data_from_lobby_server,$forced_load_flag=0){
        $result=false;
        if(empty($room_id)){
            return $result;
        }elseif(isset($char_data_from_lobby_server['error'])){
            return $result;
        }elseif(empty($char_data_from_lobby_server['char_id'])){
            return $result;
        }elseif(empty($char_data_from_lobby_server['owner_id'])){
            return $result;
        }elseif(!isset($char_data_from_lobby_server['update_time'])){
            return $result;
        }elseif(!isset($char_data_from_lobby_server['stand_image'])){
            return $result;
        }
        $save_dir=$this->getCharDir($room_id,$char_data_from_lobby_server['owner_id']);
        if(!$this->createSaveDir($save_dir)){
            return $result;
        }
        $save_file=$save_dir.$char_data_from_lobby_server['char_id'].'.xml';
        $exfilelock=new classFileLock($save_dir,$char_data_from_lobby_server['char_id'].'_lockfile',5);
        if($exfilelock->flock($save_dir)){
            if(file_exists($save_file)){
                if($dom=simplexml_load_file($save_file)){
                    if($forced_load_flag==1){
                        $dom->update_time=-1;
                    }
                    if((int)$char_data_from_lobby_server['update_time']>(int)$dom->update_time){
                        foreach($char_data_from_lobby_server as $key => $value){
                            if(isset($dom->{$key})){
                                $dom->{$key}=htmlspecialchars($value,ENT_XML1);
                            }else{
                                $dom->addChild($key,htmlspecialchars($value,ENT_XML1));
                            }
                        }
                        if(isset($dom->expressions)){
                            unset($dom->expressions);
                        }
                        $stand_image_array=$this->standStringToArray($char_data_from_lobby_server['stand_image']);
                        if(count($stand_image_array)>0){
                            foreach($stand_image_array as $value){
                                $expression=$dom->addChild('expressions');
                                $expression->addChild('name',htmlspecialchars($value[0],ENT_XML1));
                                $expression->addChild('eimg',htmlspecialchars($value[1],ENT_XML1));
                                $expression->addChild('w',htmlspecialchars($value[2],ENT_XML1));
                                $expression->addChild('h',htmlspecialchars($value[3],ENT_XML1));
                            }
                        }
                        if($dom->asXML($save_file)!==false){
                            $this->getDataFromLobby($room_id,$char_data_from_lobby_server);
                            $result=true;
                        }
                        unset($dom);
                    }
                }else{
                    return false;
                }
            }else{
                $this->updateRegistrationData();
                $dom=new DomDocument('1.0');
                $dom->encoding="UTF-8";
                $domRoot=$dom->appendChild($dom->createElement('data'));
                foreach($char_data_from_lobby_server as $key => $value){
                    $domRoot->appendChild($dom->createElement($key,htmlspecialchars($value,ENT_XML1)));
                }
                $stand_image_array=$this->standStringToArray($char_data_from_lobby_server['stand_image']);
                if(count($stand_image_array)>0){
                    foreach($stand_image_array as $value){
                        $expression=$domRoot->appendChild($dom->createElement('expressions'));
                        $expression->appendChild($dom->createElement('name',htmlspecialchars($value[0],ENT_XML1)));
                        $expression->appendChild($dom->createElement('eimg',htmlspecialchars($value[1],ENT_XML1)));
                        $expression->appendChild($dom->createElement('w',htmlspecialchars($value[2],ENT_XML1)));
                        $expression->appendChild($dom->createElement('h',htmlspecialchars($value[3],ENT_XML1)));
                    }
                }
                if($dom->save($save_file)!==false){
                    $this->getDataFromLobby($room_id,$char_data_from_lobby_server);
                    $result=true;
                }
                unset($dom);
            }
        }
        $exfilelock->unflock($save_dir);
        return $result;
    }
    // ロビーサーバーから取得した画像データを保存する。
    function getImageFromLobby($image_path,$room_id='',$owner_id='',$lobby_url=LOBBY_URL_ROOT){
        $result=false;
        if(empty($image_path)){
            return $result;
        }
        if(empty($room_id)){
            if(empty($this->room_id)){
                return $result;
            }else{
                $room_id=$this->room_id;
            }
        }
        if(empty($owner_id)){
            if(empty($this->owner_id)){
                return $result;
            }else{
                $owner_id=$this->owner_id;
            }
        }
        $save_dir=$this->getImageDir($room_id,$owner_id);
        if(!$this->createSaveDir($save_dir)){
            return $result;
        }
        $save_file=$save_dir.basename($image_path);
        if(file_exists($save_file)){
            return true;
        }
        $target_image_url=$lobby_url.$image_path;
        $true_image_flag=false;
		$context=array(
			'ssl'=>array(
				'verify_peer'=>false,
				'verify_peer_name'=>false
			)
		);
        if($image_data=file_get_contents($target_image_url,FILE_BINARY,stream_context_create($context))){
            if($image_info=getimagesizefromstring($image_data)){
                if(strpos($image_info['mime'],'image/')!==false){
                    $true_image_flag=true;
               }
            }
        }
        if($true_image_flag==true){
            if(file_put_contents($save_file,$image_data,LOCK_EX)!==false){
                $result=true;
            }
        }
        return $result;
    }
    function save($dir_root=DIR_ROOT){
        $result=false;
        $err_check_array=array('room_id','id','owner_id','game_type','name');
        foreach($err_check_array as $eca_value){
            if(empty($this->{$eca_value})){
                return $result;
            }
        }
        $err_check_array=array('room_id','id','owner_id');
        foreach($err_check_array as $eca_value){
            if(!ctype_alnum($this->{$eca_value})){
                return $result;
            }
        }
        $save_dir=$this->getCharDir();
        if(!$this->createSaveDir($save_dir)){
            return $result;
        }
        $save_file=$save_dir.$this->id.'.xml';
        $this->getTags();
        $exfilelock=new classFileLock($save_dir,$this->id.'_lockfile',5);
        if($exfilelock->flock($save_dir)){
            $param_array=$this->getParamArray();
            if(file_exists($save_file)){
                $this->updateTime();
                $dom=simplexml_load_file($save_file);
                foreach($param_array as $value){
                    if(isset($dom->{$value[0]})){
                        $dom->{$value[0]}=$this->{$value[1]};
                    }else{
                        $dom->addChild($value[0],$this->{$value[1]});
                    }
                }
                if($dom->asXML($save_file)!==false){
                    $result=true;
                }
                unset($dom);
            }else{
                $this->updateRegistrationData();
                $dom=new DomDocument('1.0');
                $dom->encoding="UTF-8";
                $dom->formatOutput=true;
                $domRoot=$dom->appendChild($dom->createElement('data'));
                foreach($param_array as $value){
                    $domChild=$domRoot->appendChild($dom->createElement($value[0]));
                    if($this->{$value[1]}===null){
                        $domChild->appendChild($dom->createTextNode(''));
                    }else{
                        $domChild->appendChild($dom->createTextNode($this->{$value[1]}));
                    }
                }
                if($dom->save($save_file)!==false){
                    $result=true;
                }
                unset($dom);
            }
            
        }
        $exfilelock->unflock($save_dir);
        return $result;
    }
    function load($room_id,$char_id,$owner_id){
        if(empty($room_id)){
            return false;
        }elseif(empty($char_id)){
            return false;
        }elseif(empty($owner_id)){
            return false;
        }
        $load_file=$this->getCharDir($room_id,$owner_id).$char_id.'.xml';
        if(file_exists($load_file)){
            if($dom=simplexml_load_file($load_file)){
                $this->room_id=$room_id;
                $param_array=$this->getParamArray();
                foreach($param_array as $value){
                    if(isset($dom->{$value[0]})){
                        $this->{$value[1]}=(string)$dom->{$value[0]};
                    }
                }
                // 立ち絵 0=name 1=img 2=width 3=height
                $this->stand=array(); 
                if(isset($dom->expressions)){
                    $i=0;
                    foreach($dom->expressions as $value){
                        $this->stand[$i]=array((string)$value->name,
                                               (string)$value->eimg,
                                               (int)$value->w,
                                               (int)$value->h);
                        $i++;
                    }
                }
            }else{
                return false;
            } 
        }else{
            return false;
        }         
        return true;
    }
    // ロビーサーバーから取得したキャラクター一覧情報をキャラクター一覧配列に変換して保存し、返す。
    function putCharListFLSInCharArray($room_id,$owner_id,$char_list_from_lobby_server){
        $characterlist_array=array();
        if(empty($room_id)){
            return $characterlist_array;
        }elseif(empty($owner_id)){
            return $characterlist_array;
        }elseif(isset($char_list_from_lobby_server['error'])){
            return $characterlist_array;
        }elseif(!isset($char_list_from_lobby_server['charlist'])){
            return $characterlist_array;
        }
        // 0  1         2    3                 4            5     6      7     8       9          10    11
        // id、game_type、name、registration_date、update_time、image、ranking、iine、owner_id、designated、macro、stand_image
        foreach($char_list_from_lobby_server['charlist'] as $value){
            if(isset($value['id'])){
                $characterlist_array[$value['id']][0]=$value['id'];
                $characterlist_array[$value['id']][1]='';
                if(isset($value['game_type'])){
                    $characterlist_array[$value['id']][1]=$value['game_type'];
                }
                $characterlist_array[$value['id']][2]='';
                if(isset($value['name'])){
                    $characterlist_array[$value['id']][2]=$value['name'];
                }
                $characterlist_array[$value['id']][3]='';
                $characterlist_array[$value['id']][4]='';
                $characterlist_array[$value['id']][5]='';
                if(isset($value['image'])){
                    $characterlist_array[$value['id']][5]=$value['image'];
                }
                $characterlist_array[$value['id']][6]='';
                $characterlist_array[$value['id']][7]='';
                $characterlist_array[$value['id']][8]='';
                $characterlist_array[$value['id']][9]='';
                $characterlist_array[$value['id']][10]='';
                if(isset($value['macro'])){
                    $characterlist_array[$value['id']][10]=$value['macro'];
                }
                $characterlist_array[$value['id']][11]='';
                if(isset($value['stand_image'])){
                    $characterlist_array[$value['id']][11]=$value['stand_image'];
                }
            }
        }
        if(count($characterlist_array)>0){
            $char_dir=$this->getCharDir($room_id,$owner_id);
            foreach($characterlist_array as $c_key => $c_value){
                $filename=$char_dir.$c_value[0].'.xml';
                if(file_exists($filename)){
                    if($dom=simplexml_load_file($filename)){
                        if(isset($dom->macro)){
                            $characterlist_array[$c_key][10]=(string)$dom->macro;
                        }
                        if(isset($dom->stand_image)){
                            $characterlist_array[$c_key][11]=(string)$dom->stand_image;
                        }
                    }
                    unset($dom);
                }
            }
            if(!$this->saveCharacterList($room_id,$owner_id,$characterlist_array)){
                return array();
            }
        }
        return $characterlist_array;
    }
    // キャラクター一覧情報を保存する。
    function saveCharacterList($room_id,$owner_id,$characterlist_array){
        // 0  1         2    3                 4            5     6      7     8       9          10    11
        // id、game_type、name、registration_date、update_time、image、ranking、iine、owner_id、designated、macro、stand_image
        $html_v='<?php'."\n";
        foreach($characterlist_array as $key => $value){
            $html_v.='$characterlist_array[\''.$key.'\']=array(';
            $html_v.='\''.escapePHP($value[0],"'").'\',';
            $html_v.='\''.escapePHP($value[1],"'").'\',';
            $html_v.='\''.escapePHP($value[2],"'").'\',';
            $html_v.='\''.escapePHP($value[3],"'").'\',';
            $html_v.='\''.escapePHP($value[4],"'").'\',';
            $html_v.='\''.escapePHP($value[5],"'").'\',';
            $html_v.='\''.escapePHP($value[6],"'").'\',';
            $html_v.='\''.escapePHP($value[7],"'").'\',';
            $html_v.='\''.escapePHP($value[8],"'").'\',';
            $html_v.='\''.escapePHP($value[9],"'").'\',';
            $html_v.='\''.escapePHP($value[10],"'").'\',';
            $html_v.='\''.escapePHP($value[11],"'").'\');'."\n";
        }
        $char_dir=$this->getCharDir($room_id,$owner_id);
        if(!is_dir($char_dir)){
            if(!mkdir($char_dir,0755,true)){
                return false;
            }
        }
        if(repeat_put_file($char_dir.'characterlist.php',$html_v,LOCK_EX)===false){
            return false;
        }
        return true;
    }
    // キャラクター一覧情報を読み込んで返す。 存在しない場合はfalseを返す。
    function loadCharacterList($room_id,$owner_id){
        if(empty($room_id)){
            return false;
        }elseif(empty($owner_id)){
            return false;
        }
        $characterlist_file=$this->getCharDir($room_id,$owner_id).'characterlist.php';
        if(!file_exists($characterlist_file)){
            return false;
        }elseif(!existsFile($characterlist_file)){
			return false;
		}
        // 0  1         2    3                 4            5     6      7     8       9          10    11
        // id、game_type、name、registration_date、update_time、image、ranking、iine、owner_id、designated、macro、stand_image
        $characterlist_array=array();
        //eval(changeEvalDataPhpFile($characterlist_file));
        if(!include($characterlist_file)){
            return false;
        }
        return $characterlist_array;
    }
    // キャラクター一覧から現在のキャラの箇所を編集する。
    function editCharacterList($room_id,$owner_id){
        if($characterlist_array=$this->loadCharacterList($room_id,$owner_id)){
            if(isset($characterlist_array[$this->id])){
                $characterlist_array[$this->id]=array(
                    $this->id,
                    $this->game_type,
                    $this->name,
                    $this->registration_date,
                    $this->update_time,
                    $this->image,
                    '',
                    '',
                    $this->owner_id,
                    $this->designated,
                    $this->macro,
                    $this->stand_image,
                );
            }
            // 0  1         2    3                 4            5     6      7     8       9          10    11
            // id、game_type、name、registration_date、update_time、image、ranking、iine、owner_id、designated、macro、stand_image
            $html_v='<?php'."\n";
            foreach($characterlist_array as $key => $value){
                $html_v.='$characterlist_array[\''.$key.'\']=array(';
                $html_v.='\''.escapePHP($value[0],"'").'\',';
                $html_v.='\''.escapePHP($value[1],"'").'\',';
                $html_v.='\''.escapePHP($value[2],"'").'\',';
                $html_v.='\''.escapePHP($value[3],"'").'\',';
                $html_v.='\''.escapePHP($value[4],"'").'\',';
                $html_v.='\''.escapePHP($value[5],"'").'\',';
                $html_v.='\''.escapePHP($value[6],"'").'\',';
                $html_v.='\''.escapePHP($value[7],"'").'\',';
                $html_v.='\''.escapePHP($value[8],"'").'\',';
                $html_v.='\''.escapePHP($value[9],"'").'\',';
                $html_v.='\''.escapePHP($value[10],"'").'\',';
                $html_v.='\''.escapePHP($value[11],"'").'\');'."\n";
            }
            $char_dir=$this->getCharDir($room_id,$owner_id);
            if(!is_dir($char_dir)){
                if(!mkdir($char_dir,0755,true)){
                    return false;
                }
            }
            if(repeat_put_file($char_dir.'characterlist.php',$html_v,LOCK_EX)===false){
                return false;
            }
        }else{
            return false;
        }
        return $characterlist_array;
    }
    // キャラクター一覧配列をJSON用配列に変換して返す。
    function convertCharListArray($characterlist_array){
        $origin_array=array();
        foreach($characterlist_array as $value){
            $macro_array=array();
            if(!empty($value[10])){
                $macro_rowarray=explode('^',$value[10]);
                foreach($macro_rowarray as $mr_value){
                    $macro_colarray=explode('|',$mr_value);
                    if(isset($macro_colarray[1])){
                        $macro_array[]=array(
                            'name'=>str_replace('"','”',$macro_colarray[0]),
                            'mat'=>str_replace('"','”',$macro_colarray[1])
                        );
                    }
                }
            }
            $stand_image_array=array();
            if(!empty($value[11])){
                $stand_img_record=explode('^',$value[11]);
                foreach($stand_img_record as $sir_value){
                    $stand_img_column=explode('|',$sir_value);
                    if(isset($stand_img_column[3])){
                        $stand_image_array[]=array(
                            'name'=>$stand_img_column[0],
                            'eimg'=>$stand_img_column[0],
                            'w'=>$stand_img_column[0],
                            'h'=>$stand_img_column[1]
                        );
                    }
                }
            }
            $origin_array['charlist'][]=array(
                'id'=>$value[0],
                'game_type'=>$value[1],
                'name'=>str_replace('"','”',$value[2]),
                'image'=>$value[5],
                'owner_id'=>$value[8],
                'macro'=>$macro_array,
                'stand_image'=>$stand_image_array
            );
        }
        return $origin_array;
    }
    // キャラクター一覧配列をJSONに変換して返す。
    function convertCharListJsonFromArray($characterlist_array){
        return json_encode($this->convertCharListArray($characterlist_array));
    }
    function setImage($request,$new_image,$img_name='',$dir_root=DIR_ROOT){
        $err_check_array=array('id','owner_id');
        foreach($err_check_array as $eca_value){
            if(empty($this->{$eca_value})){
                return false;
            }
        }
        $err_check_array=array('id','owner_id');
        foreach($err_check_array as $eca_value){
            if(!ctype_alnum($this->{$eca_value})){
                return false;
            }
        }
        $dir_save=$this->getCharDir();
        if(!is_dir($dir_save)){
            @mkdir($dir_save,0755,true);
        }
        $filename=$dir_save.$this->id.'.xml';
        if(file_exists($filename)){
            $changed_flag=false;
            $before_img='';
            $this->updateTime();
            $dom=simplexml_load_file($filename);
            if($request=='face'){
                if(isset($dom->char_image)){
                    $before_img=(string)$dom->char_image;
                }
                $dom->char_image=$new_image;
                $this->image=$new_image;
                $changed_flag=true;
            }elseif(preg_match('/^stand([0-9]+)$/',$request,$match_array)){
                $ex_no=(int)$match_array[1];
                // 追加
                if($ex_no==0){
                    if($img_info=@getimagesize($dir_root.$new_image)){
                        if(($img_info[0]>0)&&($img_info[1]>0)){
                            $expression=$dom->addChild('expressions');
                            $expression->addChild('name',$img_name);
                            $expression->addChild('eimg',$new_image);
                            $expression->addChild('w',$img_info[0]);
                            $expression->addChild('h',$img_info[1]);
                            $this->stand[]=array($img_name,
                                                 $new_image,
                                                 $img_info[0],
                                                 $img_info[1]);
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                // 変更
                }else{
                    $before_img='';
                    if(isset($dom->expressions[($ex_no-1)]->eimg)){
                        $before_img=(string)$dom->expressions[($ex_no-1)]->eimg;
                    }
                    if($img_name!=''){
                        $dom->expressions[($ex_no-1)]->name=$img_name;
                        $this->stand[($ex_no-1)][0]=$img_name;
                    }
                    if($new_image!=''){
                        if($img_info=@getimagesize($dir_root.$new_image)){
                            if(($img_info[0]>0)&&($img_info[1]>0)){
                                $changed_flag=true;
                                $dom->expressions[($ex_no-1)]->eimg=$new_image;
                                $dom->expressions[($ex_no-1)]->w=$img_info[0];
                                $dom->expressions[($ex_no-1)]->h=$img_info[1];
                                $this->stand[($ex_no-1)][1]=$new_image;
                                $this->stand[($ex_no-1)][2]=$img_info[0];
                                $this->stand[($ex_no-1)][3]=$img_info[1];
                            }
                        }
                    }
                }
            }elseif(preg_match('/^dstand([0-9]+)$/',$request,$match_array)){
                $ex_no=(int)$match_array[1];
                if($ex_no==0){
                    return false;
                // 削除
                }else{
                    unset($dom->expressions[($ex_no-1)]);
                    unset($this->stand[($ex_no-1)]);
                    $this->stand=array_values($this->stand);
                }
            
            }else{
                return false;
            }
            $this->stand_image=$this->standArrayToString();
            $dom->stand_image=$this->stand_image;
            if($dom->asXML($filename)){
                if(($new_image!='')&&
                   ($new_image!=$before_img)&&
                   ($changed_flag==true)&&
                   (file_exists($dir_root.$new_image))&&
                   (file_exists($dir_root.$before_img))){
                       @unlink($dir_root.$before_img); 
                }
                unset($dom);
                return true;
            }else{
                if(($new_image!='')&&
                   ($new_image!=$before_img)&&
                   ($changed_flag==true)&&
                   (file_exists($dir_root.$new_image))&&
                   (file_exists($dir_root.$before_img))){
                       @unlink($dir_root.$new_image); 
                }
                unset($dom);
                return false;
            }
        }
        return false;
    }
    function standArrayToString(){
        $result='';
        if(isset($this->stand[0][0])){
            foreach($this->stand as $value){
                if($result!=''){
                    $result.='^';
                }
                $result.=$value[0].'|'.$value[1].'|'.$value[2].'|'.$value[3];
            }
        }
        return $result;
    }
    function standStringToArray($stand_image){
        $stand_image_array=array();
        if(!empty($stand_image)){
            $row=explode('^',$stand_image);
            foreach($row as $row_value){
                $column=explode('|',$row_value);
                if(isset($column[3])){
                    // 立ち絵 0=name 1=img 2=width 3=height
                    $stand_image_array[]=array($column[0],$column[1],$column[2],$column[3]);
                }
            }
        }
        return $stand_image_array;
    }
}
class classRoomList{
    public $stime=0; // 現時刻 クラス作成時・メソッド呼び出し時に時間を更新する
    public $room=array(); // ルームデータ配列
    
    // コンストラクタ
    function __construct(){
        $this->stime=time();
    }
    // メソッド
    // ルームリストにルームを保存
    function save($server,
                  $id,
                  $name,
                  $creater,
                  $creater_ip,
                  $password='',
                  $voice_id='',
                  $created_time=0,
                  $update_time=0,
                  $expiration_time=0,
                  $ssl_flag=0,
                  $password_flag=0,
                  $tour_flag=0,
                  $parties='',
                  $visitors='',
                  $dicebot='',
                  $dir_root=DIR_ROOT){

        $this->stime=time();
        if(empty($server)){
            return false;
        }elseif(empty($id)){
            return false;
        }elseif(empty($name)){
            return false;
        }elseif(empty($creater)){
            return false;
        }elseif(empty($creater_ip)){
            return false;
        }
        $ra=array(); // roomlist.phpのルームデータ配列の初期化
        $file=$dir_root.'r/roomlist.php';
        if(file_exists($file)){
			if(!existsFile($file)){
				return false;
			}
            eval(changeEvalDataPhpFile($file));
        }
        $key=0;
        $saved_flag=false;
        $this->room=array(); // ルームデータ配列の初期化
        foreach($ra as $value){
            $this->room[$key]['server']=$value[0]; // サーバードメイン
            $this->room[$key]['id']=$value[1]; // ルームID 及び ファイル名
            $this->room[$key]['name']=$value[2]; // ルーム名
            $this->room[$key]['creater']=$value[3]; // ルームを作成したプレイヤーのプレイヤーID
            $this->room[$key]['creater_ip']=$value[4]; // ルームを作成したプレイヤーのIPアドレス
            $this->room[$key]['password']=$value[5]; // パスワード
            $this->room[$key]['voice_id']=$value[6]; // discordのルームID
            $this->room[$key]['created_time']=$value[7]; // 作成日時
            $this->room[$key]['update_time']=$value[8]; // 最終更新時間
            $this->room[$key]['expiration_time']=$value[9]; // ルームの有効期限
            $this->room[$key]['ssl_flag']=$value[10]; // サーバーのSSL有無フラグ 0=http 1=https
            $this->room[$key]['password_flag']=$value[11]; // パスワードの有無フラグ 0=なし 1=あり
            $this->room[$key]['tour_flag']=$value[12]; // 見学の有無フラグ 0=可 1=不可
            $this->room[$key]['parties']=$value[13]; // 参加者人数
            $this->room[$key]['visitors']=$value[14]; // 見学者人数
            $this->room[$key]['dicebot']=$value[15]; // ダイスボット
            if(($value[0]==$server)&&($value[1]==$id)){
                $saved_flag=$key;
            }
            $key++;
        }
        if($saved_flag!==false){
            $key=$saved_flag;
        }
        $this->room[$key]['server']=$server;
        $this->room[$key]['id']=$id;
        $this->room[$key]['name']=$name;
        $this->room[$key]['creater']=$creater;
        $this->room[$key]['creater_ip']=$creater_ip;
        $this->room[$key]['password']=$password;
        $this->room[$key]['voice_id']=$voice_id;
        $this->room[$key]['created_time']=$created_time;
        $this->room[$key]['update_time']=$update_time;
        $this->room[$key]['expiration_time']=$expiration_time;
        $this->room[$key]['ssl_flag']=$ssl_flag;
        $this->room[$key]['password_flag']=$password_flag;
        $this->room[$key]['tour_flag']=$tour_flag;
        $this->room[$key]['parties']=$parties;
        $this->room[$key]['visitors']=$visitors;
        $this->room[$key]['dicebot']=$dicebot;
        $html='<?php'."\n";
        foreach($this->room as $value){
            $html.='$ra[]=array(';
            $html.='\''.escapePHP($value['server'],"'").'\',';
            $html.='\''.escapePHP($value['id'],"'").'\',';
            $html.='\''.escapePHP($value['name'],"'").'\',';
            $html.='\''.escapePHP($value['creater'],"'").'\',';
            $html.='\''.escapePHP($value['creater_ip'],"'").'\',';
            $html.='\''.escapePHP($value['password'],"'").'\',';
            $html.='\''.escapePHP($value['voice_id'],"'").'\',';
            $html.='\''.escapePHP($value['created_time'],"'").'\',';
            $html.='\''.escapePHP($value['update_time'],"'").'\',';
            $html.='\''.escapePHP($value['expiration_time'],"'").'\',';
            $html.='\''.escapePHP($value['ssl_flag'],"'").'\',';
            $html.='\''.escapePHP($value['password_flag'],"'").'\',';
            $html.='\''.escapePHP($value['tour_flag'],"'").'\',';
            $html.='\''.escapePHP($value['parties'],"'").'\',';
            $html.='\''.escapePHP($value['visitors'],"'").'\',';
            $html.='\''.escapePHP($value['dicebot'],"'").'\');'."\n";
        }
        if(repeat_put_file($file,$html,LOCK_EX)){
            return true;
        }
        return false;
    }
    // ルームリストの読み込み
    function load($dir_root=DIR_ROOT){
        $this->stime=time();
        $this->room=array(); // ルームデータ配列の初期化
        $file=$dir_root.'r/roomlist.php';
        if(file_exists($file)){
            if(include($file)){
                $key=0;
				if(isset($ra)){
					foreach((array)$ra as $value){
						$this->room[$key]['server']=$value[0]; // サーバードメイン
						$this->room[$key]['id']=$value[1]; // ルームID 及び ファイル名
						$this->room[$key]['name']=$value[2]; // ルーム名
						$this->room[$key]['creater']=$value[3]; // ルームを作成したプレイヤーのプレイヤーID
						$this->room[$key]['creater_ip']=$value[4]; // ルームを作成したプレイヤーのIPアドレス
						$this->room[$key]['password']=$value[5]; // パスワード
						$this->room[$key]['voice_id']=$value[6]; // discordのルームID
						$this->room[$key]['created_time']=$value[7]; // 作成日時
						$this->room[$key]['update_time']=$value[8]; // 最終更新時間
						$this->room[$key]['expiration_time']=$value[9]; // ルームの有効期限
						$this->room[$key]['ssl_flag']=$value[10]; // サーバーのSSL有無フラグ 0=http 1=https
						$this->room[$key]['password_flag']=$value[11]; // パスワードの有無フラグ 0=なし 1=あり
						$this->room[$key]['tour_flag']=$value[12]; // 見学の有無フラグ 0=可 1=不可
						$this->room[$key]['parties']=$value[13]; // 参加者人数
						$this->room[$key]['visitors']=$value[14]; // 見学者人数
						$this->room[$key]['dicebot']=$value[15]; // ダイスボット
						$key++;
					}
				}
            }else{
                return false;
            }
        }
        return true;
    }
    // ルームリストのルーム内容の更新
    function update($id,$str_players_in_room='',$game_dicebot='g99',$dir_root=DIR_ROOT,$this_domain=THIS_DOMAIN){
        $this->stime=time();
        if(empty($id)){
            return false;
        }
        $ra=array(); // roomlist.phpのルームデータ配列の初期化
        $file=$dir_root.'r/roomlist.php';
        if(file_exists($file)){
			if(!existsFile($file)){
				return false;
			}
            eval(changeEvalDataPhpFile($file));
        }
        $num_participants=0;
        $num_observers=0;
        if(!empty($str_players_in_room)){
            $login_player_list=explode('^',$str_players_in_room);
            foreach((array)$login_player_list as $login_player_record){
                $login_player_data=explode('|',$login_player_record);
                if(!empty($login_player_data[0])){
                    if(($this->stime-(int)$login_player_data[1])<=1800){
                        if((int)$login_player_data[2]==0){
                            $num_participants++;
                        }else{
                            $num_observers++;
                        }
                    }
                }
            }
        }
        $key=0;
        $saved_flag=false;
        $this->room=array(); // ルームデータ配列の初期化
        foreach($ra as $value){
            //if(($this->stime-(int)$value[9])>0){
                $this->room[$key]['server']=$value[0]; // サーバードメイン
                $this->room[$key]['id']=$value[1]; // ルームID 及び ファイル名
                $this->room[$key]['name']=$value[2]; // ルーム名
                $this->room[$key]['creater']=$value[3]; // ルームを作成したプレイヤーのプレイヤーID
                $this->room[$key]['creater_ip']=$value[4]; // ルームを作成したプレイヤーのIPアドレス
                $this->room[$key]['password']=$value[5]; // パスワード
                $this->room[$key]['voice_id']=$value[6]; // discordのルームID
                $this->room[$key]['created_time']=$value[7]; // 作成日時
                $this->room[$key]['expiration_time']=$value[9]; // ルームの有効期限
                $this->room[$key]['ssl_flag']=$value[10]; // サーバーのSSL有無フラグ 0=http 1=https
                $this->room[$key]['password_flag']=$value[11]; // パスワードの有無フラグ 0=なし 1=あり
                $this->room[$key]['tour_flag']=$value[12]; // 見学の有無フラグ 0=可 1=不可
                if(($value[0]==$this_domain)&&($value[1]==$id)){
                    $this->room[$key]['update_time']=$this->stime; // 最終更新時間
                    $this->room[$key]['parties']=$num_participants; // 参加者人数
                    $this->room[$key]['visitors']=$num_observers; // 見学者人数
                    $this->room[$key]['dicebot']=$game_dicebot; // ダイスボット
                }else{
                    $this->room[$key]['update_time']=$value[8]; // 最終更新時間
                    $this->room[$key]['parties']=$value[13]; // 参加者人数
                    $this->room[$key]['visitors']=$value[14]; // 見学者人数
                    $this->room[$key]['dicebot']=$value[15]; // ダイスボット
                }
            //}
            $key++;
        }
        $html='<?php'."\n";
        foreach($this->room as $value){
            $html.='$ra[]=array(';
            $html.='\''.escapePHP($value['server'],"'").'\',';
            $html.='\''.escapePHP($value['id'],"'").'\',';
            $html.='\''.escapePHP($value['name'],"'").'\',';
            $html.='\''.escapePHP($value['creater'],"'").'\',';
            $html.='\''.escapePHP($value['creater_ip'],"'").'\',';
            $html.='\''.escapePHP($value['password'],"'").'\',';
            $html.='\''.escapePHP($value['voice_id'],"'").'\',';
            $html.='\''.escapePHP($value['created_time'],"'").'\',';
            $html.='\''.escapePHP($value['update_time'],"'").'\',';
            $html.='\''.escapePHP($value['expiration_time'],"'").'\',';
            $html.='\''.escapePHP($value['ssl_flag'],"'").'\',';
            $html.='\''.escapePHP($value['password_flag'],"'").'\',';
            $html.='\''.escapePHP($value['tour_flag'],"'").'\',';
            $html.='\''.escapePHP($value['parties'],"'").'\',';
            $html.='\''.escapePHP($value['visitors'],"'").'\',';
            $html.='\''.escapePHP($value['dicebot'],"'").'\');'."\n";
        }
        if(!repeat_put_file($file,$html,LOCK_EX)){
            return false;
        }
        return true;
    }
    // ルームリストからルームの削除
    function delete($server,$id,$dir_root=DIR_ROOT){
        $this->stime=time();
        if(empty($server)){
            return false;
        }elseif(empty($id)){
            return false;
        }
        $ra=array(); // roomlist.phpのルームデータ配列の初期化
        $file=$dir_root.'r/roomlist.php';
        if(file_exists($file)){
			if(!existsFile($file)){
				return false;
			}
            eval(changeEvalDataPhpFile($file));
        }
        $key=0;
        $this->room=array(); // ルームデータ配列の初期化
        foreach($ra as $value){
            if(($value[0]==$server)&&($value[1]==$id)){
                // 削除対象なので追加しない
            }else{
                $this->room[$key]['server']=$value[0]; // サーバードメイン
                $this->room[$key]['id']=$value[1]; // ルームID 及び ファイル名
                $this->room[$key]['name']=$value[2]; // ルーム名
                $this->room[$key]['creater']=$value[3]; // ルームを作成したプレイヤーのプレイヤーID
                $this->room[$key]['creater_ip']=$value[4]; // ルームを作成したプレイヤーのIPアドレス
                $this->room[$key]['password']=$value[5]; // パスワード
                $this->room[$key]['voice_id']=$value[6]; // discordのルームID
                $this->room[$key]['created_time']=$value[7]; // 作成日時
                $this->room[$key]['update_time']=$value[8]; // 最終更新時間
                $this->room[$key]['expiration_time']=$value[9]; // ルームの有効期限
                $this->room[$key]['ssl_flag']=$value[10]; // サーバーのSSL有無フラグ 0=http 1=https
                $this->room[$key]['password_flag']=$value[11]; // パスワードの有無フラグ 0=なし 1=あり
                $this->room[$key]['tour_flag']=$value[12]; // 見学の有無フラグ 0=可 1=不可
                $this->room[$key]['parties']=$value[13]; // 参加者人数
                $this->room[$key]['visitors']=$value[14]; // 見学者人数
                $this->room[$key]['dicebot']=$value[15]; // ダイスボット
                $key++;
            }
        }
        $html='<?php'."\n";
        $key=0;
        foreach($this->room as $value){
            $html.='$ra[]=array(';
            $html.='\''.escapePHP($value['server'],"'").'\',';
            $html.='\''.escapePHP($value['id'],"'").'\',';
            $html.='\''.escapePHP($value['name'],"'").'\',';
            $html.='\''.escapePHP($value['creater'],"'").'\',';
            $html.='\''.escapePHP($value['creater_ip'],"'").'\',';
            $html.='\''.escapePHP($value['password'],"'").'\',';
            $html.='\''.escapePHP($value['voice_id'],"'").'\',';
            $html.='\''.escapePHP($value['created_time'],"'").'\',';
            $html.='\''.escapePHP($value['update_time'],"'").'\',';
            $html.='\''.escapePHP($value['expiration_time'],"'").'\',';
            $html.='\''.escapePHP($value['ssl_flag'],"'").'\',';
            $html.='\''.escapePHP($value['password_flag'],"'").'\',';
            $html.='\''.escapePHP($value['tour_flag'],"'").'\',';
            $html.='\''.escapePHP($value['parties'],"'").'\',';
            $html.='\''.escapePHP($value['visitors'],"'").'\',';
            $html.='\''.escapePHP($value['dicebot'],"'").'\');'."\n";
            $key++;
        }
        if(!repeat_put_file($file,$html,LOCK_EX)){
            return false;
        }
        return true;
    }
    // 古いルームをルームリストから一括削除
    function deleteAllOld($dir_root=DIR_ROOT){
        $this->stime=time();
        $ra=array(); // roomlist.phpのルームデータ配列の初期化
        $file=$dir_root.'r/roomlist.php';
        if(file_exists($file)){
			if(!existsFile($file)){
				return false;
			}
            eval(changeEvalDataPhpFile($file));
        }
        $key=0;
        $this->room=array(); // ルームデータ配列の初期化
        foreach($ra as $value){
            if($this->stime>(int)$value[9]){
                // 削除対象なので追加しない
            }else{
                $this->room[$key]['server']=$value[0]; // サーバードメイン
                $this->room[$key]['id']=$value[1]; // ルームID 及び ファイル名
                $this->room[$key]['name']=$value[2]; // ルーム名
                $this->room[$key]['creater']=$value[3]; // ルームを作成したプレイヤーのプレイヤーID
                $this->room[$key]['creater_ip']=$value[4]; // ルームを作成したプレイヤーのIPアドレス
                $this->room[$key]['password']=$value[5]; // パスワード
                $this->room[$key]['voice_id']=$value[6]; // discordのルームID
                $this->room[$key]['created_time']=$value[7]; // 作成日時
                $this->room[$key]['update_time']=$value[8]; // 最終更新時間
                $this->room[$key]['expiration_time']=$value[9]; // ルームの有効期限
                $this->room[$key]['ssl_flag']=$value[10]; // サーバーのSSL有無フラグ 0=http 1=https
                $this->room[$key]['password_flag']=$value[11]; // パスワードの有無フラグ 0=なし 1=あり
                $this->room[$key]['tour_flag']=$value[12]; // 見学の有無フラグ 0=可 1=不可
                $this->room[$key]['parties']=$value[13]; // 参加者人数
                $this->room[$key]['visitors']=$value[14]; // 見学者人数
                $this->room[$key]['dicebot']=$value[15]; // ダイスボット
                $key++;
            }
        }
        $html='<?php'."\n";
        $key=0;
        foreach($this->room as $value){
            $html.='$ra[]=array(';
            $html.='\''.escapePHP($value['server'],"'").'\',';
            $html.='\''.escapePHP($value['id'],"'").'\',';
            $html.='\''.escapePHP($value['name'],"'").'\',';
            $html.='\''.escapePHP($value['creater'],"'").'\',';
            $html.='\''.escapePHP($value['creater_ip'],"'").'\',';
            $html.='\''.escapePHP($value['password'],"'").'\',';
            $html.='\''.escapePHP($value['voice_id'],"'").'\',';
            $html.='\''.escapePHP($value['created_time'],"'").'\',';
            $html.='\''.escapePHP($value['update_time'],"'").'\',';
            $html.='\''.escapePHP($value['expiration_time'],"'").'\',';
            $html.='\''.escapePHP($value['ssl_flag'],"'").'\',';
            $html.='\''.escapePHP($value['password_flag'],"'").'\',';
            $html.='\''.escapePHP($value['tour_flag'],"'").'\',';
            $html.='\''.escapePHP($value['parties'],"'").'\',';
            $html.='\''.escapePHP($value['visitors'],"'").'\',';
            $html.='\''.escapePHP($value['dicebot'],"'").'\');'."\n";
            $key++;
        }
        if(!repeat_put_file($file,$html,LOCK_EX)){
            return false;
        }
        return true;
    }
}