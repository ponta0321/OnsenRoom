<?php
    require('../s/common/core.php');
    require(DIR_ROOT.'s/common/function.php');
    require(DIR_ROOT.'s/common/exefunction.php');
    $uploadsuccess_flag=false;
    $upload_err_msg='';
    $image_save_url='';
    $music_save_url='';
    $cardset_save_url='';
    $boarddata_save_url='';
    $room_id='';
    $room_dir='';
    $room_file='';
    $room_mirror_file='';
    if(!empty($_POST['room_file'])){
        $room_id=basename($_POST['room_file']);
        $room_dir=DIR_ROOT.'r/n/'.$room_id.'/';
        $room_file=$room_dir.'data.xml';
        $room_mirror_file=$room_dir.'data-mirror.xml';
        if(!file_exists($room_file)){
            echo 'uploadbgiIsN=不明なエラー(3)';
            exit;
        }
    }else{
        echo 'uploadbgiIsN=アップロードされたデータが大き過ぎる等の理由により処理を中断しました。';
        exit;
    }
    // アップロードリクエスト処理
    $upload_state='';
    if(!empty($_POST['upload_state'])){
        $upload_state=$_POST['upload_state'];
    }else{
        echo 'uploadbgiIsN=不明なエラー(1)';
        exit;
    }
    $exfilelock=new classFileLock($room_dir,$room_id.'_lockfile',5);
    if($exfilelock->flock($room_dir)){
        // ルームファイルの読み込み
        if(($room_xml=autoloadXmlFile($room_file,$room_mirror_file))===false){
            $exfilelock->unflock($room_dir);
            exit;
        }
        $delete_uploadfiles='';
        $room_save_basepath=DIR_ROOT.'r/n/'.$room_id.'/';
        // 背景画像のアップロード
        if($upload_state=='image'){
            if(!empty($_FILES['upload_image']['name'])){
                $image_save_file='uploadimage1'.((time()*100)+mt_rand(0,99));
                $image_save_path=$room_save_basepath.$image_save_file;
                $image_save_url=URL_ROOT.'r/n/'.$room_id.'/'.$image_save_file;
                if(uploadImage($_FILES['upload_image'],$image_save_path,$upload_err_msg,UPLOAD_BI_LIMIT_SIZE)){
                    if(empty($room_xml->head->game_syncboardsize)){
                        if($image_size_info=getimagesize($image_save_path)){
                            $magnification=1;
                            if($image_size_info[0]<544){
                                $magnification=ceil(544/$image_size_info[0]);
                            }
                            $room_xml->head->game_boardwidth=ceil(($image_size_info[0]*$magnification)/32);
                            $room_xml->head->game_boardheight=ceil(($image_size_info[1]*$magnification)/32);
                            adjustMappingData($room_xml);
                        }
                    }
                    $room_xml->head->game_backimage=$image_save_url;
                    $room_xml->head->game_backimagelist=$image_save_url;
                    $room_xml->asXML($room_file);
                    $delete_uploadfiles=glob($room_save_basepath.'uploadimage1*');
                    if(count($delete_uploadfiles)>100){
                        if(sort($delete_uploadfiles)){
                            for($i=0;$i<(count($delete_uploadfiles)-100);$i++){
                                @unlink($delete_uploadfiles[$i]);
                            }
                        }
                    }
                    $uploadsuccess_flag=true;
                }
            }else{
                $upload_err_msg='アップロードするファイルが指定されていません。';
            }
        // メモ用画像のアップロード
        }elseif(strpos($upload_state,'game_memo')!==false){
            $memoimage_no=substr($upload_state,9);
            if(!empty($_FILES['upload_image']['name'])){
                $delete_uploadfiles=glob($room_save_basepath.'uploadimagemm'.$memoimage_no.'m*');
                $image_save_file='uploadimagemm'.$memoimage_no.'m'.((time()*100)+mt_rand(0,99));
                $image_save_path=$room_save_basepath.$image_save_file;
                $image_save_url=URL_ROOT.'r/n/'.$room_id.'/'.$image_save_file;
                if(uploadImage($_FILES['upload_image'],$image_save_path,$upload_err_msg,UPLOAD_BI_LIMIT_SIZE,250)){
					$node_memo=$room_xml->head->memo;
					$write_flag=false;
					if($node_memo){
						for($i=0;$i<count($node_memo);$i++){
							if($node_memo[$i]['id']==$memoimage_no){
								$node_memo[$i]->txt=$image_save_url;
								$write_flag=true;
								break;
							}
						}
					}
					if($write_flag===false){
						$upload_err_msg='アップロード先の共通メモ'.$memoimage_no.'は存在しません。';
					}else{
						$room_xml->asXML($room_file);
						$uploadsuccess_flag=true;
					}
                    foreach($delete_uploadfiles as $value){
                        @unlink($value);
                    }
                }
            }else{
                $upload_err_msg='アップロードするファイルが指定されていません。';
            }
        // BGMのアップロード
        }elseif($upload_state=='music'){
            if(!empty($_FILES['upload_music']['name'])){
                /*
                if(isset($room_xml->head->game_musiclist)){
                    $musiclist=(string)$room_xml->head->game_musiclist;
                }else{
                    $musiclist='';
                }
                */
                $delete_uploadfiles=glob($room_save_basepath.'uploadmusic*');
                $music_save_file='uploadmusic'.((time()*100)+mt_rand(0,99));
                $music_save_path=$room_save_basepath.$music_save_file;
                $music_save_url=URL_ROOT.'r/n/'.$room_id.'/'.$music_save_file;
                if(uploadMusic($_FILES['upload_music'],$music_save_path,$upload_err_msg,UPLOAD_MS_LIMIT_SIZE)){
                    //$musiclist=$music_save_url.'|'.$music_save_file;
                    //$room_xml->head->game_musiclist=$musiclist;
                    if(isset($room_xml->head->game_music)){
                        $room_xml->head->game_music=$music_save_url;
                    }else{
                        $room_xml->head->addChild('game_music',$music_save_url);
                    }
                    if(isset($room_xml->head->game_music_state)){
                        $room_xml->head->game_music_state='play';
                    }else{
                        $room_xml->head->addChild('game_music_state','play');
                    }
                    $room_xml->asXML($room_file);
                    $uploadsuccess_flag=true;
                    foreach($delete_uploadfiles as $value){
                        @unlink($value);
                    }
                }
            }else{
                $upload_err_msg='アップロードするファイルが指定されていません。';
            }
        // カードセットのアップロード
        }elseif(strpos($upload_state,'cardset')!==false){
            $cardset_no=substr($upload_state,7);
            if(!empty($_FILES['upload_cardset']['name'])){
                $delete_uploadfiles=glob($room_save_basepath.'uploadcardset'.$cardset_no.'*');
                $cardset_save_file='uploadcardset'.$cardset_no.((time()*100)+mt_rand(0,99)).'.xml';
                $cardset_save_path=$room_save_basepath.$cardset_save_file;
                $cardset_save_url=URL_ROOT.'r/n/'.$room_id.'/'.$cardset_save_file;
                if(uploadXmlData($_FILES['upload_cardset'],$cardset_save_path,$upload_err_msg,UPLOAD_CS_LIMIT_SIZE)){
                    // カードセットファイルのロード
                    if($cardset_xml=simplexml_load_file($cardset_save_path)){
                        // 必要データのチェック
                        if(loadCardset($upload_err_msg,$cardset_no,$room_xml,$cardset_xml,$cardset_save_url)){
                            $room_xml->asXML($room_file);
                            $uploadsuccess_flag=true;
                        }
                    }else{
                        $upload_err_msg='アップロードしたファイルはカードセットデータではない又はデータが正しくありません。';
                    }
                    foreach($delete_uploadfiles as $value){
                        @unlink($value);
                    }
                }
            }else{
                $upload_err_msg='アップロードするファイルが指定されていません。';
            }
        // ボードデータのアップロード
        }elseif($upload_state=='boarddata'){
            if(!empty($_FILES['upload_boarddata']['name'])){
                $delete_uploadfiles=glob($room_save_basepath.'uploadboarddata*');
                $boarddata_save_file='uploadboarddata'.((time()*100)+mt_rand(0,99)).'.xml';
                $boarddata_save_path=$room_save_basepath.$boarddata_save_file;
                $boarddata_save_url=URL_ROOT.'r/n/'.$room_id.'/'.$boarddata_save_file;
                if(uploadXmlData($_FILES['upload_boarddata'],$boarddata_save_path,$upload_err_msg)){
                    // ボードデータファイルのロード
                    if($boarddata_xml=simplexml_load_file($boarddata_save_path)){
                        // 必要データのチェック
                        if(loadBoardData($upload_err_msg,$room_id,$room_xml,$boarddata_xml)){
                            $room_xml->asXML($room_file);
                            $uploadsuccess_flag=true;
                        }
                    }else{
                        $upload_err_msg='アップロードしたファイルはボード情報データではない又はデータが正しくありません。';
                    }
                    foreach($delete_uploadfiles as $value){
                        @unlink($value);
                    }
                }
            }else{
                $upload_err_msg='アップロードするファイルが指定されていません。';
            }
        // マップチップのアップロード
        }elseif($upload_state=='mapchip'){
            if(!empty($_FILES['upload_image']['name'])){
                $mapchip_save_file='uploadmapchip'.((time()*100)+mt_rand(0,99));
                $mapchip_save_path=$room_save_basepath.$mapchip_save_file;
                $mapchip_save_url=URL_ROOT.'r/n/'.$room_id.'/'.$mapchip_save_file;
                if(uploadImage($_FILES['upload_image'],$mapchip_save_path,$upload_err_msg,UPLOAD_BI_LIMIT_SIZE)){
                    if(isset($room_xml->head->game_mapchip)){
                        $room_xml->head->game_mapchip=$mapchip_save_url;
                    }else{
                        $room_xml->head->addChild('game_mapchip',$mapchip_save_url);
                    }
                    $room_xml->asXML($room_file);
                    $delete_uploadfiles=glob($room_save_basepath.'uploadmapchip*');
                    if(count($delete_uploadfiles)>100){
                        if(sort($delete_uploadfiles)){
                            for($i=0;$i<(count($delete_uploadfiles)-100);$i++){
                                @unlink($delete_uploadfiles[$i]);
                            }
                        }
                    }
                    $uploadsuccess_flag=true;
                }
            }else{
                $upload_err_msg='アップロードするファイルが指定されていません。';
            }
        }else{
            $upload_err_msg='アップロードするファイル種別が指定されていません。';
        }
        unset($room_xml);
    }else{
        $upload_err_msg='アクセスが集中したため送信に失敗しました。';
    }
    if($uploadsuccess_flag==true){
        if(!empty($image_save_file)){
            echo 'uploadbgiIsK='.$image_save_url;
        }elseif(!empty($music_save_file)){
            echo 'uploadbgiIsK='.$music_save_url;
        }elseif(!empty($cardset_save_url)){
            echo 'uploadbgiIsK='.$cardset_save_url;
        }elseif(!empty($boarddata_save_url)){
            echo 'uploadbgiIsK='.$boarddata_save_url;
        }elseif(!empty($mapchip_save_url)){
            echo 'uploadbgiIsK='.$mapchip_save_url;
        }else{
            echo 'uploadbgiIsN=不明なエラー';
        }
    }else{
        echo 'uploadbgiIsN='.$upload_err_msg;
    }
    $exfilelock->unflock($room_dir);