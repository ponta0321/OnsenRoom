<style>
#cardboard{
    width:100%;
    height:100%;
    padding:0;
}
.card{
    position:absolute;
    display:inline-block;
    padding:0;
    margin:0;
}
.card_head{
    position:absolute;
    top:0;
    left:0;
    font-size:12px;
    background-color:rgba(255,255,255,0.8);
}
.card_body_v{
    display:block;
    top:0;
    left:0;
    width:100px;
    height:150px;
    border-radius:6px;
    -webkit-border-radius:6px;
    -moz-border-radius:6px;
    border:1px #AAA solid;
    margin-top:20px;
    padding:0;
    overflow:hidden;
}
.card_body_h{
    display:block;
    top:0;
    left:0;
    width:150px;
    height:100px;
    border-radius:6px;
    -webkit-border-radius:6px;
    -moz-border-radius:6px;
    border:1px #AAA solid;
    margin-top:20px;
    padding:0;
    overflow:hidden;
}
</style>
<div id="cardboard"></div>
<ul id="card_right_menu" class="right_menu" style="position:absolute;top:0;left:0;width:250px;visibility:hidden;z-index:9999;">
    <li id="cdrm_card_name">カード名称</li>
    <li id="cdrm_draw_1t" class="operate">カードを引く</li>
    <li id="cdrm_draw_c1" class="operate">選んで引く</li>
    <li id="cdrm_put_1t" class="operate">カードを場に出す</li>
    <li id="cdrm_put_c1" class="operate">選んで場に出す</li>
    <li id="cdrm_drop_1t" class="operate">カードを捨て札に置く</li>
    <li id="cdrm_drop_ab" class="operate">全てのカードを捨て札に置く</li>
    <li id="cdrm_drop_c1" class="operate">選んで捨て札に置く</li>
    <li id="cdrm_donate_1t" class="operate">カードを渡す</li>
    <li id="cdrm_back_1b" class="operate">カードを山札に戻す</li>
    <li id="cdrm_back_ab" class="operate">全てのカードを山札に戻す</li>
    <li id="cdrm_back_c1" class="operate">選んで山札に戻す</li>
    <li id="cdrm_shuffle" class="operate">シャッフルする</li>
    <li id="cdrm_public" class="operate">全員に見せる（公開）</li>
    <li id="cdrm_private" class="operate">自分だけ見る（非公開）</li>
    <li id="cdrm_hide" class="operate">カードを伏せる（非公開）</li>
    <li id="cdrm_front" class="operate">正面に向ける</li>
    <li id="cdrm_right" class="operate">右向きにする</li>
    <li id="cdrm_left" class="operate">左向きにする</li>
    <li id="cdrm_reverse" class="operate">逆向きにする</li>
</ul>
<script>
var default_card_position=[
	0,0,
	110,0,
	275,0,
	0,180,
	275,180,
	20,0,
	20,0,
	20,0,
	0,50,
	30,30
];
/*
0=山札（初期位置）X    1=山札（初期位置）Y
2=捨て札（初期位置）X   3=捨て札（初期位置）Y
4=場（初期位置）X      5=場（初期位置）Y
6=自分手札（初期位置）X 7=自分手札（初期位置）Y
8=他手札（初期位置）X   9=他手札（初期位置）Y
10=次カード間隔X（場）   11=次カード間隔Y（場）
12=次カード間隔X（自分） 13=次カード間隔Y（自分）
14=次カード間隔X（他）   15=次カード間隔Y（他）
16=次メンバー間隔X（他）  17=次カード間隔Y（他）
18=次カードセット間隔X   19=次カードセット間隔Y
*/
var event_CDRM_flag=[false,''];
// カード用 右クリックメニュー オープン処理
openCardRightMenu=function(e){
    var eventinit=e.target;
    if((document.getElementById('select_player_right_menu')==null)&&
       (document.getElementById('select_card_right_menu')==null)){
        if(event_CDRM_flag[0]==false){
            e.preventDefault();
            var img_id='ic_c'+eventinit.id.substr(4);
            var eCardImg=document.getElementById(img_id);
            var strCardImgAlt=eCardImg.alt;
            var arrayCardImgAlt=strCardImgAlt.split(","); // 0=ロケーション(l) 1=方向(d) 2=公開(v) 3=順位(z) 4=(sx) 5=(sy) 6=カード名称
            document.getElementById('cdrm_card_name').innerHTML=arrayCardImgAlt[6]; //カード名称
            if(arrayCardImgAlt[0]=='_0'){ //山札
                document.getElementById('cdrm_draw_1t').style.display='block'; //カードを引く
                document.getElementById('cdrm_draw_c1').style.display='block'; //選んで引く
                document.getElementById('cdrm_put_1t').style.display='block'; //カードを場に出す
                document.getElementById('cdrm_put_c1').style.display='block'; //選んで場に出す
                document.getElementById('cdrm_drop_1t').style.display='block'; //カードを捨て札に置く
                document.getElementById('cdrm_drop_ab').style.display='block'; //全てのカードを捨て札に置く
                document.getElementById('cdrm_drop_c1').style.display='block'; //選んで捨て札に置く
                document.getElementById('cdrm_donate_1t').style.display='none'; //カードを渡す
                document.getElementById('cdrm_back_1b').style.display='none'; //カードを山札に戻す
                document.getElementById('cdrm_back_ab').style.display='none'; //全てのカードを山札に戻す
                document.getElementById('cdrm_back_c1').style.display='none'; //選んで山札に戻す
                document.getElementById('cdrm_shuffle').style.display='block'; //シャッフルする
                if(arrayCardImgAlt[2]!='_1'){
                    document.getElementById('cdrm_public').style.display='block'; //全員に見せる（公開）
                }else{
                    document.getElementById('cdrm_public').style.display='none'; //全員に見せる（公開）
                }
                document.getElementById('cdrm_private').style.display='none'; //自分だけ見る（非公開）
                if(arrayCardImgAlt[2]!='_0'){
                    document.getElementById('cdrm_hide').style.display='block'; //カードを伏せる（非公開）
                }else{
                    document.getElementById('cdrm_hide').style.display='none'; //カードを伏せる（非公開）
                }
                if(arrayCardImgAlt[1]!=0){
                    document.getElementById('cdrm_front').style.display='block'; //正面に向ける
                }else{
                    document.getElementById('cdrm_front').style.display='none'; //正面に向ける
                }
                document.getElementById('cdrm_right').style.display='none'; //右向きにする
                document.getElementById('cdrm_left').style.display='none'; //左向きにする
                if(arrayCardImgAlt[1]!=180){
                    document.getElementById('cdrm_reverse').style.display='block'; //逆向きにする
                }else{
                    document.getElementById('cdrm_reverse').style.display='none'; //逆向きにする
                }
            }else if(arrayCardImgAlt[0]=='_1'){ //捨て札
                document.getElementById('cdrm_draw_1t').style.display='block'; //カードを引く
                document.getElementById('cdrm_draw_c1').style.display='block'; //選んで引く
                document.getElementById('cdrm_put_1t').style.display='block'; //カードを場に出す
                document.getElementById('cdrm_put_c1').style.display='block'; //選んで場に出す
                document.getElementById('cdrm_drop_1t').style.display='none'; //カードを捨て札に置く
                document.getElementById('cdrm_drop_ab').style.display='none'; //全てのカードを捨て札に置く
                document.getElementById('cdrm_drop_c1').style.display='none'; //選んで捨て札に置く
                document.getElementById('cdrm_donate_1t').style.display='none'; //カードを渡す
                document.getElementById('cdrm_back_1b').style.display='block'; //カードを山札に戻す
                document.getElementById('cdrm_back_ab').style.display='block'; //全てのカードを山札に戻す
                document.getElementById('cdrm_back_c1').style.display='block'; //選んで山札に戻す
                document.getElementById('cdrm_shuffle').style.display='block'; //シャッフルする
                if(arrayCardImgAlt[2]!='_1'){
                    document.getElementById('cdrm_public').style.display='block'; //全員に見せる（公開）
                }else{
                    document.getElementById('cdrm_public').style.display='none'; //全員に見せる（公開）
                }
                document.getElementById('cdrm_private').style.display='none'; //自分だけ見る（非公開）
                if(arrayCardImgAlt[2]!='_0'){
                    document.getElementById('cdrm_hide').style.display='block'; //カードを伏せる（非公開）
                }else{
                    document.getElementById('cdrm_hide').style.display='none'; //カードを伏せる（非公開）
                }
                if(arrayCardImgAlt[1]!=0){
                    document.getElementById('cdrm_front').style.display='block'; //正面に向ける
                }else{
                    document.getElementById('cdrm_front').style.display='none'; //正面に向ける
                }
                document.getElementById('cdrm_right').style.display='none'; //右向きにする
                document.getElementById('cdrm_left').style.display='none'; //左向きにする
                if(arrayCardImgAlt[1]!=180){
                    document.getElementById('cdrm_reverse').style.display='block'; //逆向きにする
                }else{
                    document.getElementById('cdrm_reverse').style.display='none'; //逆向きにする
                }
            }else if(arrayCardImgAlt[0]=='_2'){ //場
                document.getElementById('cdrm_draw_1t').style.display='block'; //カードを引く
                document.getElementById('cdrm_draw_c1').style.display='none'; //選んで引く
                document.getElementById('cdrm_put_1t').style.display='none'; //カードを場に出す
                document.getElementById('cdrm_put_c1').style.display='none'; //選んで場に出す
                document.getElementById('cdrm_drop_1t').style.display='block'; //カードを捨て札に置く
                document.getElementById('cdrm_drop_ab').style.display='block'; //全てのカードを捨て札に置く
                document.getElementById('cdrm_drop_c1').style.display='none'; //選んで捨て札に置く
                document.getElementById('cdrm_donate_1t').style.display='none'; //カードを渡す
                document.getElementById('cdrm_back_1b').style.display='block'; //カードを山札に戻す
                document.getElementById('cdrm_back_ab').style.display='block'; //全てのカードを山札に戻す
                document.getElementById('cdrm_back_c1').style.display='none'; //選んで山札に戻す
                document.getElementById('cdrm_shuffle').style.display='none'; //シャッフルする
                if(arrayCardImgAlt[2]!='_1'){
                    document.getElementById('cdrm_public').style.display='block'; //全員に見せる（公開）
                }else{
                    document.getElementById('cdrm_public').style.display='none'; //全員に見せる（公開）
                }
                document.getElementById('cdrm_private').style.display='none'; //自分だけ見る（非公開）
                if(arrayCardImgAlt[2]!='_0'){
                    document.getElementById('cdrm_hide').style.display='block'; //カードを伏せる（非公開）
                }else{
                    document.getElementById('cdrm_hide').style.display='none'; //カードを伏せる（非公開）
                }
                if(arrayCardImgAlt[1]!=0){
                    document.getElementById('cdrm_front').style.display='block'; //正面に向ける
                }else{
                    document.getElementById('cdrm_front').style.display='none'; //正面に向ける
                }
                if(arrayCardImgAlt[1]!=90){
                    document.getElementById('cdrm_right').style.display='block'; //右向きにする
                }else{
                    document.getElementById('cdrm_right').style.display='none'; //右向きにする
                }
                if(arrayCardImgAlt[1]!=270){
                    document.getElementById('cdrm_left').style.display='block'; //左向きにする
                }else{
                    document.getElementById('cdrm_left').style.display='none'; //左向きにする
                }
                if(arrayCardImgAlt[1]!=180){
                    document.getElementById('cdrm_reverse').style.display='block'; //逆向きにする
                }else{
                    document.getElementById('cdrm_reverse').style.display='none'; //逆向きにする
                }
            }else{ //手札
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_draw_1t').style.display='block'; //カードを引く
                }else{
                    document.getElementById('cdrm_draw_1t').style.display='none'; //カードを引く
                }
                document.getElementById('cdrm_draw_c1').style.display='none'; //選んで引く
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_put_1t').style.display='none'; //カードを場に出す
                }else{
                    document.getElementById('cdrm_put_1t').style.display='block'; //カードを場に出す
                }
                document.getElementById('cdrm_put_c1').style.display='none'; //選んで場に出す
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_drop_1t').style.display='none'; //カードを捨て札に置く
                }else{
                    document.getElementById('cdrm_drop_1t').style.display='block'; //カードを捨て札に置く
                }
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_drop_ab').style.display='none'; //全てのカードを捨て札に置く
                }else{
                    document.getElementById('cdrm_drop_ab').style.display='block'; //全てのカードを捨て札に置く
                }
                document.getElementById('cdrm_drop_c1').style.display='none'; //選んで捨て札に置く
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_donate_1t').style.display='none'; //カードを渡す
                }else{
                    document.getElementById('cdrm_donate_1t').style.display='block'; //カードを渡す
                }
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_back_1b').style.display='none'; //カードを山札に戻す
                }else{
                    document.getElementById('cdrm_back_1b').style.display='block'; //カードを山札に戻す
                }
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_back_ab').style.display='none'; //全てのカードを山札に戻す
                }else{
                    document.getElementById('cdrm_back_ab').style.display='block'; //全てのカードを山札に戻す
                }
                document.getElementById('cdrm_back_c1').style.display='none'; //選んで山札に戻す
                document.getElementById('cdrm_shuffle').style.display='none'; //シャッフルする
                if(arrayCardImgAlt[0]!='<?=$principal_id;?>'){
                    document.getElementById('cdrm_public').style.display='none'; //全員に見せる（公開）
                    document.getElementById('cdrm_private').style.display='none'; //自分だけ見る（非公開）
                    document.getElementById('cdrm_hide').style.display='none'; //カードを伏せる（非公開）
                    document.getElementById('cdrm_front').style.display='none'; //正面に向ける
                    document.getElementById('cdrm_reverse').style.display='none'; //逆向きにする
                }else{
                    if(arrayCardImgAlt[2]!='_1'){
                        document.getElementById('cdrm_public').style.display='block'; //全員に見せる（公開）
                    }else{
                        document.getElementById('cdrm_public').style.display='none'; //全員に見せる（公開）
                    }
                    if(arrayCardImgAlt[2]!='<?=$principal_id;?>'){
                        document.getElementById('cdrm_private').style.display='block'; //自分だけ見る（非公開）
                    }else{
                        document.getElementById('cdrm_private').style.display='none'; //自分だけ見る（非公開）
                    }
                    if(arrayCardImgAlt[2]!='_0'){
                        document.getElementById('cdrm_hide').style.display='block'; //カードを伏せる（非公開）
                    }else{
                        document.getElementById('cdrm_hide').style.display='none'; //カードを伏せる（非公開）
                    }
                    if(arrayCardImgAlt[1]!=0){
                        document.getElementById('cdrm_front').style.display='block'; //正面に向ける
                    }else{
                        document.getElementById('cdrm_front').style.display='none'; //正面に向ける
                    }
                    if(arrayCardImgAlt[1]!=180){
                        document.getElementById('cdrm_reverse').style.display='block'; //逆向きにする
                    }else{
                        document.getElementById('cdrm_reverse').style.display='none'; //逆向きにする
                    }
                }
                document.getElementById('cdrm_right').style.display='none'; //右向きにする
                document.getElementById('cdrm_left').style.display='none'; //左向きにする
            }
            var mouseX=e.clientX;
            var mouseY=e.clientY;
            var card_r_menu_w=document.getElementById('card_right_menu').offsetWidth;
            var card_r_menu_h=document.getElementById('card_right_menu').offsetHeight;
            var rect=document.getElementById('cardboard').getBoundingClientRect();
            var boardscrect=document.getElementById('puw_inbody_11').getBoundingClientRect();
            var positionX=rect.left;
            var positionY=rect.top;
            var offsetX=mouseX-positionX;
            if(offsetX>((boardscrect.width+document.getElementById('puw_inbody_11').scrollLeft)-(card_r_menu_w+5))){
                offsetX=((boardscrect.width+document.getElementById('puw_inbody_11').scrollLeft)-(card_r_menu_w+5));
                if(offsetX<0){
                    offsetX=0;
                }
            }
            var offsetY=mouseY-positionY;
            if(offsetY>((boardscrect.height+document.getElementById('puw_inbody_11').scrollLeft)-(card_r_menu_h+5))){
                offsetY=((boardscrect.height+document.getElementById('puw_inbody_11').scrollLeft)-(card_r_menu_h+5));
                if(offsetY<0){
                    offsetY=0;
                }
            }
            document.getElementById('card_right_menu').style.left=offsetX+'px';
            document.getElementById('card_right_menu').style.top=offsetY+'px';
            document.getElementById('card_right_menu').style.visibility='visible';
            event_CDRM_flag[0]=true;
            event_CDRM_flag[1]=eventinit.id;
        }
    }
}
// カード用 右クリックメニュー イベント処理
eventCardRightMenu=function(e){
    var eventinit=e.target;
    if(event_CDRM_flag[0]==true){
        if(eventinit.id=='cdrm_draw_1t'){ //カードを引く
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_draw_c1'){ //選んで引く
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                openSelectCardRightMenu(e,'cdrm_draw_c1',event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_put_1t'){ //カードを場に出す
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_put_c1'){ //選んで場に出す
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                openSelectCardRightMenu(e,'cdrm_put_c1',event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_drop_1t'){ //カードを捨て札に置く
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_drop_ab'){ //全てのカードを捨て札に置く
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_drop_c1'){ //選んで捨て札に置く
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                openSelectCardRightMenu(e,'cdrm_drop_c1',event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_donate_1t'){ //カードを渡す
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                openSelectPlayerRightMenu(e);
            }
        }else if(eventinit.id=='cdrm_back_1b'){ //カードを山札に戻す
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_back_ab'){ //全てカードを山札に戻す
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_back_c1'){ //選んで山札に戻す
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                openSelectCardRightMenu(e,'cdrm_back_c1',event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_shuffle'){ //シャッフルする
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_public'){ //全員に見せる（公開）
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_private'){ //自分だけ見る（非公開）
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_hide'){ //カードを伏せる（非公開）
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_front'){ //正面に向ける
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_right'){ //右向きにする
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_left'){ //左向きにする
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else if(eventinit.id=='cdrm_reverse'){ //逆向きにする
            if(e.button==0){
                document.getElementById('card_right_menu').style.visibility='hidden';
                saveCardEvent(eventinit.id,event_CDRM_flag[1]);
            }
        }else{
            e.preventDefault();
            document.getElementById('card_right_menu').style.visibility='hidden';
            event_CDRM_flag[0]=false;
            event_CDRM_flag[1]='';
        }
    }
}
// プレイヤー選択用 右クリックメニュー オープン処理
function openSelectPlayerRightMenu(e){
    var html_v='';
    html_v+='<ul id="select_player_right_menu" class="right_menu" style="position:absolute;top:0;left:0;width:250px;visibility:hidden;z-index:9999;">';
    html_v+='<li id="sprmt_title">誰にカードを渡しますか？</li>';
    html_v+='<li class="operate" onClick="deleteElement(\'select_player_right_menu\')">キャンセルする</li>';
    for(var key in listNickName){
        if(key!='<?=$principal_id;?>'){
            html_v+='<li class="operate" onClick="eventSelectPlayerRightMenu(\''+event_CDRM_flag[1]+'\',\''+key+'\')">'+listNickName[key][0]+'</li>';
        }
    }
    html_v+='</ul>';
	document.getElementById('puw_inbody_11').insertAdjacentHTML('beforebegin',html_v);
    var mouseX=e.pageX;
    var mouseY=e.pageY;
    var card_r_menu_w=document.getElementById('select_player_right_menu').offsetWidth;
    var card_r_menu_h=document.getElementById('select_player_right_menu').offsetHeight;
    var rect=document.getElementById('cardboard').getBoundingClientRect();
    var positionX=rect.left+window.pageXOffset;
    var positionY=rect.top+window.pageYOffset;
    var offsetX=mouseX-positionX;
    if(offsetX>(rect.width-(card_r_menu_w+5))){
        offsetX=(rect.width-(card_r_menu_w+5));
        if(offsetX<0){
            offsetX=0;
        }
    }
    var offsetY=mouseY-positionY;
    if(offsetY>(rect.height-(card_r_menu_h+5))){
        offsetY=(rect.height-(card_r_menu_h+5));
        if(offsetY<0){
            offsetY=0;
        }
    }
    document.getElementById('select_player_right_menu').style.left=offsetX+'px';
    document.getElementById('select_player_right_menu').style.top=offsetY+'px';
    document.getElementById('select_player_right_menu').style.visibility='visible';
}
// プレイヤー選択用 右クリックメニュー イベント処理
function eventSelectPlayerRightMenu(target_id,player_id){
    deleteElement('select_player_right_menu');
    sendCardEvent({
        event:'cdrm_donate_1t',
        target:target_id,
        param:player_id
    });
}
// カード選択用 右クリックメニュー オープン処理
function openSelectCardRightMenu(e,event_id,target_id){
    //event_id  cdrm_draw_c1=選んで引く  cdrm_put_c1=選んで場に出す  cdrm_drop_c1=選んで捨て札に置く
    var cardset_no=target_id.substr(7,1);
    var location_flag=target_id.substr(9);
    var eventinit=e.target;
    var html_v='';
    html_v+='<ul id="select_card_right_menu" class="right_menu" style="position:absolute;top:0;left:0;width:250px;visibility:hidden;z-index:9999;">';
    if(location_flag=='card_stock'){
        html_v+='<li id="sprmt_title">山札から選ぶ</li>';
        html_v+='<li class="operate" onClick="deleteElement(\'select_card_right_menu\')">キャンセルする</li>';
        for(var key in listCardStock[cardset_no]){
            html_v+='<li class="operate" onClick="eventSelectCardRightMenu(\''+event_id+'\',\'ic_d-'+key+'\',\'_0\')">'+listCardStock[cardset_no][key][0]+'</li>';
        }
        sendCardEvent({
            event:'cdrm_donate_1t',
            target:target_id,
        });
    }else if(location_flag=='discard_stock'){
        html_v+='<li id="sprmt_title">捨て札から選ぶ</li>';
        html_v+='<li class="operate" onClick="deleteElement(\'select_card_right_menu\')">キャンセルする</li>';
        for(var key in listDiscardStock[cardset_no]){
            html_v+='<li class="operate" onClick="eventSelectCardRightMenu(\''+event_id+'\',\'ic_d-'+key+'\',\'_1\')">'+listDiscardStock[cardset_no][key][0]+'</li>';
        }
        sendCardEvent({
            event:'reference_discard_stock',
            target:target_id
        });
    }
    html_v+='</ul>';
	document.getElementById('puw_inbody_11').insertAdjacentHTML('beforebegin',html_v);
    var mouseX=e.pageX;
    var mouseY=e.pageY;
    var card_r_menu_w=document.getElementById('select_card_right_menu').offsetWidth;
    var card_r_menu_h=document.getElementById('select_card_right_menu').offsetHeight;
    var rect=document.getElementById('cardboard').getBoundingClientRect();
    var positionX=rect.left+window.pageXOffset;
    var positionY=rect.top+window.pageYOffset;
    var offsetX=mouseX-positionX;
    if(offsetX>(rect.width-(card_r_menu_w+5))){
        offsetX=(rect.width-(card_r_menu_w+5));
        if(offsetX<0){
            offsetX=0;
        }
    }
    var offsetY=mouseY-positionY;
    if(offsetY>(rect.height-(card_r_menu_h+5))){
        offsetY=(rect.height-(card_r_menu_h+5));
        if(offsetY<0){
            offsetY=0;
        }
    }
    document.getElementById('select_card_right_menu').style.left=offsetX+'px';
    document.getElementById('select_card_right_menu').style.top=offsetY+'px';
    document.getElementById('select_card_right_menu').style.visibility='visible';
}
// カード選択用 右クリックメニュー イベント処理
function eventSelectCardRightMenu(event_id,target_id,l_flag){
    deleteElement('select_card_right_menu');
    sendCardEvent({
        event:event_id,
        target:target_id,
        param:l_flag
    });
}
// カードイベントの保存
function saveCardEvent(event_name,target_id){
    sendCardEvent({
        event:event_name,
        target:target_id
    });
}
// カード初期位置の決定
function initCardPosition(id,l,z){
    var cardset_no=id.substr(2,1);
    var element_id='ic_a-'+id;
    /*
    0=山札（初期位置）X    1=山札（初期位置）Y
    2=捨て札（初期位置）X   3=捨て札（初期位置）Y
    4=場（初期位置）X      5=場（初期位置）Y
    6=自分手札（初期位置）X 7=自分手札（初期位置）Y
    8=他手札（初期位置）X   9=他手札（初期位置）Y
    10=次カード間隔X（場）   11=次カード間隔Y（場）
    12=次カード間隔X（自分） 13=次カード間隔Y（自分）
    14=次カード間隔X（他）   15=次カード間隔Y（他）
    16=次メンバー間隔X（他）  17=次カード間隔Y（他）
    18=次カードセット間隔X   19=次カードセット間隔Y
    */
    var int_top=10;
    var int_left=10;
    var int_zindex=0;
    var int_top_b=0;
    var int_left_b=0;
    // カード初期位置の決定
    if(l=='_0'){ //山札
        int_left+=default_card_position[0];
        int_top+=default_card_position[1];
        int_zindex=2;
    }else if(l=='_1'){ //捨て札
        int_left+=default_card_position[2];
        int_top+=default_card_position[3];
        int_zindex=1;
    }else if(l=='_2'){ //場
        int_left+=default_card_position[4]+(parseInt(z)*default_card_position[10]);
        int_top+=default_card_position[5]+(parseInt(z)*default_card_position[11]);
        int_zindex=3;
    }else{ //手札
        if(l=='<?=$principal_id;?>'){
            int_left+=default_card_position[6]+(parseInt(z)*default_card_position[12]);
            int_top+=default_card_position[7]+(parseInt(z)*default_card_position[13]);
            int_zindex=100;
        }else{
            var myself_list_no=intNMListNo('<?=$principal_id;?>');
            var target_list_no=intNMListNo(l);
            if(myself_list_no>=target_list_no){
                target_list_no++;
            }
            int_left+=default_card_position[8]+(parseInt(z)*default_card_position[14])+(target_list_no*default_card_position[16]);
            int_top+=default_card_position[9]+(parseInt(z)*default_card_position[15])+(target_list_no*default_card_position[17]);
            int_zindex=10+intNMListNo(l);
        }
    }
    int_left+=((parseInt(cardset_no)-1)*default_card_position[18]);
    int_top+=((parseInt(cardset_no)-1)*default_card_position[19]);
    return [int_left,int_top,int_zindex];
}
// カード作成
function createCard(int_left,int_top,int_zindex,id,nm,sx,sy,sw,sh,dw,dh,dt,l,d,v,z){
    var html_v='';
    var cardset_no=id.substr(2,1);
    var element_id='ic_a-'+id;
    var img_id='ic_c-'+id;
    var ui_id='ic_d-'+id;
    var int_top_b=0;
    var int_left_b=0;
    var inheritance_position_flag=false;
    if(document.getElementById(element_id)!=null){
        var difference_flag=false;
        var eCardImg=document.getElementById(img_id);
        var strCardImgAlt=eCardImg.alt;
        var arrayCardImgAlt=strCardImgAlt.split(","); // 0=ロケーション(l) 1=方向(d) 2=公開(v) 3=順位(z) 4=(sx) 5=(sy)
        inheritance_position_flag=true;
        if(arrayCardImgAlt[0]!=l){
            difference_flag=true;
            inheritance_position_flag=false;
        }
        if(arrayCardImgAlt[1]!=d){
            difference_flag=true;
        }
        if(arrayCardImgAlt[2]!=v){
            difference_flag=true;
        }else{
            if((v=='_1')||(v=='<?=$principal_id;?>')){
                if(arrayCardImgAlt[4]!=sx){
                    difference_flag=true;
                }
                if(arrayCardImgAlt[5]!=sy){
                    difference_flag=true;
                }
            }
        }
        if(arrayCardImgAlt[3]!=z){
            difference_flag=true;
        }
        if(difference_flag==true){
            int_top_b=parseInt(document.getElementById('ic_a-'+id).style.top);
            int_left_b=parseInt(document.getElementById('ic_a-'+id).style.left);
            deleteElement(element_id);
        }else{
            return true;
        }
    }
    if(inheritance_position_flag==true){
        int_top=int_top_b;
        int_left=int_left_b;
    }
    html_v+='<div id="'+element_id+'" class="card" style="top:'+int_top+'px;left:'+int_left+'px;z-index:'+int_zindex+';">';
    html_v+='<p id="ic_b-'+id+'" class="card_head">';
    if(l=='_0'){
        html_v+='山札('+cardset_no+')';
    }else if(l=='_1'){
        html_v+='捨て札('+cardset_no+')';
    }else if(l=='_2'){
        html_v+='場('+cardset_no+')';
    }else{
        if(l=='<?=$principal_id;?>'){
            html_v+='手札('+cardset_no+')';
        }else{
            html_v+=displayNickName(l)+'('+cardset_no+')';
        }
    }
    html_v+='&nbsp;[';
    if(v=='_1'){
        html_v+='公開';
    }else{
        html_v+='非公開';
    }
    html_v+=']</p>';
    // 画像
    html_v+='<img id="ic_c-'+id+'" src="" style="display:none;" alt="'+l+','+d+','+v+','+z;
    if((v=='_1')||(v=='<?=$principal_id;?>')){
        html_v+=','+sx+','+sy;
        if(l=='_0'){
            html_v+=',山札('+cardset_no+')';
        }else if(l=='_1'){
            html_v+=',捨て札('+cardset_no+')';
        }else{
            html_v+=','+nm;
        }
    }else{
        html_v+=',-,-';
        if(l=='_0'){
            html_v+=',山札('+cardset_no+')';
        }else if(l=='_1'){
            html_v+=',捨て札('+cardset_no+')';
        }else if(l=='_2'){
            html_v+=',場('+cardset_no+')';
        }else{
            if(l=='<?=$principal_id;?>'){
                html_v+=',手札('+cardset_no+')';
            }else{
                html_v+=','+displayNickName(l)+'('+cardset_no+')';
            }
        }
    }
    html_v+='">';
    html_v+='<canvas id="'+ui_id+'" class="';
    if((d==0)||(d==180)){
        html_v+='card_body_v';
    }else{
        html_v+='card_body_h';
    }
    html_v+='" title="';
    if((v=='_1')||(v=='<?=$principal_id;?>')){
        if(l=='_0'){
            html_v+='山札';
        }else if(l=='_1'){
            html_v+='捨て札';
        }else{
            html_v+=nm;
        }
        if(dt){
            html_v+='&#10;&#10;'+dt;
        }
    }else{
        if(l=='_0'){
            html_v+='山札';
        }else if(l=='_1'){
            html_v+='捨て札';
        }else if(l=='_2'){
            html_v+='場のカード [非公開]';
        }else{
            if(l=='<?=$principal_id;?>'){
                html_v+='自分の手札 [非公開]';
            }else{
                html_v+=displayNickName(l)+'の手札 [非公開]';
            }
        }
    }
    html_v+='"></canvas></div>';
	document.getElementById('cardboard').insertAdjacentHTML('beforebegin',html_v);
    if((v=='_1')||(v=='<?=$principal_id;?>')){
        if(sx<0){
            canvasTextTrim(ui_id,nm,dt,sx,sy,sw,sh,dw,dh,d);
        }else{
            if(card_imagesheet[cardset_no]){
                canvasImageTrim(ui_id,card_imagesheet[cardset_no],sx,sy,sw,sh,dw,dh,d);
            }
        }
    }else{
        canvasImageTrim(ui_id,cardback_image,0,0,100,150,100,150,d);
    }
    $('#'+element_id).draggable({containment:"#cardboard"});
    document.getElementById(ui_id).addEventListener("contextmenu",openCardRightMenu);
}
// 回転計算
function tranformContext(ctx,sc,dw,dh,r){
    ctx.setTransform(sc,0,0,sc,0,0);
    if(r && r>0){
        ctx.rotate(r*Math.PI/180);
        if(r==90){
            ctx.translate(0,-dh*(1/sc));
        }else if(r==180){
            ctx.translate(-dw*(1/sc),-dh*(1/sc));
        }else if(r==270){
            ctx.translate(-dw*(1/sc),0);
        }
    }
}
// テキスト画像リサイズ／回転処理 トリミング版
function canvasTextTrim(id,str_nm,str_dt,sx,sy,sw,sh,dw,dh,r){
    var canvas=document.getElementById(id);
    if(r==90||r==270){
        // swap w <==> h
        canvas.width=dh;
        canvas.height=dw;
    }else{
        canvas.width=dw;
        canvas.height=dh;
    }
    var angle=parseInt(r)*Math.PI/180;
    var sin=Math.sin(angle);
    var cos=Math.cos(angle);
    // Draw (Resize)
    var ctx=canvas.getContext('2d');
    tranformContext(ctx,1,dw,dh,r);
    // 背景塗り
    ctx.fillStyle='#FFD';
    ctx.beginPath();
    ctx.rect(0,0,100,150);
    ctx.fill();
    // 配置初期設定
    ctx.font='10px "Osaka−等幅","ＭＳ ゴシック","Consolas","Courier New","Monaco","Courier","monospace"';
    ctx.fillStyle='#000';
    var base_px=3.5; // 配置初期値 x軸
    var base_ph=3.5; // 配置初期値 y軸
    var base_sh=ctx.measureText('Ｍ').width+1; // 初期サイズの文字高さ
    var str_width=0; // 出力する文字の横幅
    var str_scale=1; // スケール
    // カード名
    str_width=ctx.measureText(str_nm).width;
    str_scale=1;
    if(str_width>93){
        str_scale=93/str_width;
    }
    tranformContext(ctx,str_scale,dw,dh,r);
    ctx.fillText(str_nm,base_px*(1/str_scale),(base_ph+base_sh)*(1/str_scale));
    // 仕切り線
    tranformContext(ctx,1,dw,dh,r);
    ctx.lineWidth=1;
    ctx.strokeStyle='#DDD';
    ctx.beginPath();
    ctx.moveTo(0,base_ph+base_sh*2);
    ctx.lineTo(100,base_ph+base_sh*2);
    ctx.stroke();
    // カード説明
    var img_dt_text=[];
    img_dt_text=str_dt.split(/\r\n|\r|\n|&#10;/);
    for(var i=0;i<img_dt_text.length;i++){
        ctx.font='10px "Osaka−等幅","ＭＳ ゴシック","Consolas","Courier New","Monaco","Courier","monospace"';
        ctx.fillStyle='#666';
        str_scale=1;
        str_width=ctx.measureText(img_dt_text[i]).width;
        if(str_width>93){
            str_scale=93/str_width;
        }
        tranformContext(ctx,str_scale,dw,dh,r);
        ctx.fillText(img_dt_text[i],base_px*(1/str_scale),(base_ph+(base_sh*(3+i)))*(1/str_scale));
    }
}
// 画像リサイズ／回転処理 トリミング版
function canvasImageTrim(id,image_obj,sx,sy,sw,sh,dw,dh,r){
    //alert(id+' / '+image_obj+' / '+sx+' / '+sy+' / '+sw+' / '+sh+' / '+dw+' / '+dh+' / '+r);
    var canvas=document.getElementById(id);
    if(r==90||r==270){
        // swap w <==> h
        canvas.width=dh;
        canvas.height=dw;
    }else{
        canvas.width=dw;
        canvas.height=dh;
    }
    // Draw (Resize)
    var ctx=canvas.getContext('2d');
    if(r && r>0){
        ctx.rotate(r*Math.PI/180);
        if(r==90){
            ctx.translate(0,-dh);
        }else if(r==180){
            ctx.translate(-dw, -dh);
        }else if(r == 270){
            ctx.translate(-dw, 0);
        }
    }
    ctx.drawImage(image_obj,sx,sy,sw,sh,0,0,dw,dh);
}
// ルーム入場時の画像ロード処理
var cardback_image=new Image();
cardback_image.onload=function(){
    loadedimgsheet_flag[0][0]=1;
};
var card_imagesheet={};
for(let ci_no=1;ci_no<5;ci_no++){
	card_imagesheet[ci_no]=new Image();
	card_imagesheet[ci_no].onload=function(){
		loadedimgsheet_flag[ci_no][0]=1;
	};
}
if(loadedimgsheet_flag[0][1]!=''){
    cardback_image.src=loadedimgsheet_flag[0][1];
}
for(let ci_no=1;ci_no<5;ci_no++){
	if(loadedimgsheet_flag[1][1]!=''){
		card_imagesheet[ci_no].src=loadedimgsheet_flag[ci_no][1];
	}
}
// カードスペース表示処理 add.2016.12.19
function displayCardSpace(resHTTP){
	if(loadedimgsheet_flag[0][0]!=1){
		return false;
	}
	var card_element='';
	var card_position=[0,0,0];
	var card_stock=[0,'',99999]; // 山札の初期化
	var discard_stock=[0,'',99999]; // 捨て札の初期化
	var displayCardArray=[]; // [x,y,id,nm,sx,sy,sw,sh,dw,dh,dt,l,d,v,z]
	var mCardId='';
	var mCard_L=''; // 0=山札 _1=捨て札 _2=場 プレイヤーID=手札
	var mCard_N='';
	var mCard_Sx='';
	var mCard_Sy='';
	var mCard_Dt='';
	var mCard_D='';
	var mCard_V='';
	var mCard_Z='';
	var mCardSet;
	var mCardSetCardSheet;
	var mCardSetCards;
	var image_src='';
	listCardStock={0:{},1:{},2:{},3:{},4:{}};
	listDiscardStock={0:{},1:{},2:{},3:{},4:{}};
	for(var cardset_no=1;cardset_no<5;cardset_no++){
		card_stock=[0,'',99999]; // 山札の初期化
		discard_stock=[0,'',99999]; // 捨て札の初期化
		mCardSet=resHTTP.getElementsByTagName('cardset'+cardset_no);
		if(typeof mCardSet[0]!='undefined'){
			mCardSetCardSheet=mCardSet[0].getElementsByTagName('imagesheet');
			if(typeof mCardSetCardSheet[0]!='undefined'){
				if(mCardSetCardSheet[0].textContent.length>0){
					loadedimgsheet_flag[cardset_no][1]=mCardSetCardSheet[0].textContent;
					loadedimgsheet_flag[cardset_no][2]=1;
				}else{
					loadedimgsheet_flag[cardset_no][2]=2;
				}
			}else{
				loadedimgsheet_flag[cardset_no][2]=2;
			}
		}else{
			loadedimgsheet_flag[cardset_no][2]=0;
		}
		if(card_imagesheet[cardset_no]){
			image_src=card_imagesheet[cardset_no].src;
			if(card_imagesheet[cardset_no].src!=loadedimgsheet_flag[cardset_no][1]){
				card_imagesheet[cardset_no].src=loadedimgsheet_flag[cardset_no][1];
			}
		}
		if(((loadedimgsheet_flag[cardset_no][1]==image_src)&&
			(loadedimgsheet_flag[cardset_no][1]!='about:blank')&&
			(loadedimgsheet_flag[cardset_no][2]==1))||(loadedimgsheet_flag[cardset_no][2]==2)){
			if((loadedimgsheet_flag[cardset_no][0]==1)||(loadedimgsheet_flag[cardset_no][2]==2)){
				mCardSetCards=mCardSet[0].getElementsByTagName('card');
				for(var k=0;k<(mCardSetCards.length);k++){
					if((typeof mCardSetCards[k].getElementsByTagName('l')[0].textContent)!='undefined'){
						mCardId='sc'+cardset_no+'-'+mCardSetCards[k].getAttributeNode('id').value;
						mCard_L=mCardSetCards[k].getElementsByTagName('l')[0].textContent;
						mCard_N=mCardSetCards[k].getElementsByTagName('nm')[0].textContent;
						mCard_Sx=mCardSetCards[k].getElementsByTagName('sx')[0].textContent;
						mCard_Sy=mCardSetCards[k].getElementsByTagName('sy')[0].textContent;
						mCard_Dt=mCardSetCards[k].getElementsByTagName('dt')[0].textContent;
						mCard_D=mCardSetCards[k].getElementsByTagName('d')[0].textContent;
						mCard_V=mCardSetCards[k].getElementsByTagName('v')[0].textContent;
						mCard_Z=mCardSetCards[k].getElementsByTagName('z')[0].textContent;
						if(mCard_L=='_0'){ //山札
							card_stock[0]=card_stock[0]+1;
							if(card_stock[2]>parseInt(mCard_Z)){
								card_stock[1]=k;
								card_stock[2]=parseInt(mCard_Z);
							}
							listCardStock[cardset_no][mCardId]={0:mCard_N};
						}else if(mCard_L=='_1'){ //捨て札
							discard_stock[0]=discard_stock[0]+1;
							if(discard_stock[2]>parseInt(mCard_Z)){
								discard_stock[1]=k;
								discard_stock[2]=parseInt(mCard_Z);
							}
							listDiscardStock[cardset_no][mCardId]={0:mCard_N};
						}else if(mCard_L=='_2'){ //場
							card_position=initCardPosition(mCardId,mCard_L,mCard_Z);
							displayCardArray.push([card_position[0],
												   card_position[1],
												   card_position[2],
												   mCardId,
												   mCard_N,
												   mCard_Sx,
												   mCard_Sy,
												   100,150,100,150,
												   mCard_Dt,
												   mCard_L,
												   mCard_D,
												   mCard_V,
												   mCard_Z]);
						}else{ //手札
							card_position=initCardPosition(mCardId,mCard_L,mCard_Z);
							displayCardArray.push([card_position[0],
												   card_position[1],
												   card_position[2],
												   mCardId,
												   mCard_N,
												   mCard_Sx,
												   mCard_Sy,
												   100,150,100,150,
												   mCard_Dt,
												   mCard_L,
												   mCard_D,
												   mCard_V,
												   mCard_Z]);
						}
					}
				}
				if(card_stock[0]>0){
					mCard_L=mCardSetCards[card_stock[1]].getElementsByTagName('l')[0].textContent;
					mCard_N=mCardSetCards[card_stock[1]].getElementsByTagName('nm')[0].textContent;
					mCard_Sx=mCardSetCards[card_stock[1]].getElementsByTagName('sx')[0].textContent;
					mCard_Sy=mCardSetCards[card_stock[1]].getElementsByTagName('sy')[0].textContent;
					mCard_Dt=mCardSetCards[card_stock[1]].getElementsByTagName('dt')[0].textContent;
					mCard_D=mCardSetCards[card_stock[1]].getElementsByTagName('d')[0].textContent;
					mCard_V=mCardSetCards[card_stock[1]].getElementsByTagName('v')[0].textContent;
					mCard_Z=mCardSetCards[card_stock[1]].getElementsByTagName('z')[0].textContent;
					card_position=initCardPosition('sc'+cardset_no+'-card_stock',mCard_L,mCard_Z);
					displayCardArray.push([card_position[0],
										   card_position[1],
										   card_position[2],
										   'sc'+cardset_no+'-card_stock',
										   mCard_N,
										   mCard_Sx,
										   mCard_Sy,
										   100,150,100,150,
										   mCard_Dt,
										   mCard_L,
										   mCard_D,
										   mCard_V,
										   mCard_Z]);
				}
				if(discard_stock[0]>0){
					mCard_L=mCardSetCards[discard_stock[1]].getElementsByTagName('l')[0].textContent;
					mCard_N=mCardSetCards[discard_stock[1]].getElementsByTagName('nm')[0].textContent;
					mCard_Sx=mCardSetCards[discard_stock[1]].getElementsByTagName('sx')[0].textContent;
					mCard_Sy=mCardSetCards[discard_stock[1]].getElementsByTagName('sy')[0].textContent;
					mCard_Dt=mCardSetCards[discard_stock[1]].getElementsByTagName('dt')[0].textContent;
					mCard_D=mCardSetCards[discard_stock[1]].getElementsByTagName('d')[0].textContent;
					mCard_V=mCardSetCards[discard_stock[1]].getElementsByTagName('v')[0].textContent;
					mCard_Z=mCardSetCards[discard_stock[1]].getElementsByTagName('z')[0].textContent;
					card_position=initCardPosition('sc'+cardset_no+'-discard_stock',mCard_L,mCard_Z);
					displayCardArray.push([card_position[0],
										   card_position[1],
										   card_position[2],
										   'sc'+cardset_no+'-discard_stock',
										   mCard_N,
										   mCard_Sx,
										   mCard_Sy,
										   100,150,100,150,
										   mCard_Dt,
										   mCard_L,
										   mCard_D,
										   mCard_V,
										   mCard_Z]);
				}
			}
		}
	}
	displayCardArray.sort(function(a,b){
		if(a[1]<b[1]){return -1;}
		if(a[1]>b[1]){return 1;}
		if(a[0]<b[0]){return -1;}
		if(a[0]>b[0]){return 1;}
		return 0;
	});
	for(var i in displayCardArray){
		createCard(displayCardArray[i][0],
				   displayCardArray[i][1],
				   displayCardArray[i][2],
				   displayCardArray[i][3],
				   displayCardArray[i][4],
				   displayCardArray[i][5],
				   displayCardArray[i][6],
				   displayCardArray[i][7],
				   displayCardArray[i][8],
				   displayCardArray[i][9],
				   displayCardArray[i][10],
				   displayCardArray[i][11],
				   displayCardArray[i][12],
				   displayCardArray[i][13],
				   displayCardArray[i][14],
				   displayCardArray[i][15]);
	}
	var delete_card_array=[];
	displayedCardElements=document.getElementsByClassName('card');
	for(var i=0;i<(displayedCardElements.length);i++){
		$exist_flag=false;
		for(var k=0;k<(displayCardArray.length);k++){
			if('ic_a-'+displayCardArray[k][3]==displayedCardElements[i].id){
				$exist_flag=true;
				break;
			}
		}
		if($exist_flag==false){
			delete_card_array.push(displayedCardElements[i].id);
		}
	}
	deleteElements(delete_card_array);
	return true;
}
// カードイベント 送信処理
function sendCardEvent(ced){
    ced.xml='<?=$base_room_file;?>';
    ced.principal='<?=$principal_id;?>';
    ced.nick_name='<?=$nick_name;?>';
    $.post('<?=URL_ROOT;?>exe/putcardevent.php',ced,function(data){
        openWTL(data);
    },'html');
}
function setDefaultCardPosition(){
    var p_flag=document.getElementById('default_card_position_select').value;
    var cardset_px=parseInt(document.getElementById('cardset_position_x').value);
    var cardset_py=parseInt(document.getElementById('cardset_position_y').value);
    if(isFinite(cardset_px)===false){
        cardset_px=30;
    }else{
        cardset_px=Math.floor(cardset_px);
        if(cardset_px>9999){
            cardset_px=9999;
        }else if(cardset_px<0){
            cardset_px=0;
        }
    }
    if(isFinite(cardset_py)===false){
        cardset_py=30;
    }else{
        cardset_py=Math.floor(cardset_py);
        if(cardset_py>9999){
            cardset_py=9999;
        }else if(cardset_py<0){
            cardset_py=0;
        }
    }
    /*
    0=山札（初期位置）X    1=山札（初期位置）Y
    2=捨て札（初期位置）X   3=捨て札（初期位置）Y
    4=場（初期位置）X      5=場（初期位置）Y
    6=自分手札（初期位置）X 7=自分手札（初期位置）Y
    8=他手札（初期位置）X   9=他手札（初期位置）Y
    10=次カード間隔X（場）   11=次カード間隔Y（場）
    12=次カード間隔X（自分） 13=次カード間隔Y（自分）
    14=次カード間隔X（他）   15=次カード間隔Y（他）
    16=次メンバー間隔X（他）  17=次カード間隔Y（他）
    18=次カードセット間隔X   19=次カードセット間隔Y
    */
    if(p_flag==1){
        default_card_position=[0,0,
                               110,0,
                               275,0,
                               0,180,
                               275,180,
                               60,0,
                               60,0,
                               60,0,
                               0,100,
                               cardset_px,cardset_py];
    }else if(p_flag==2){
        default_card_position=[0,0,
                               110,0,
                               275,0,
                               0,180,
                               20,180,
                               105,0,
                               105,0,
                               105,0,
                               0,175,
                               cardset_px,cardset_py];
    }else{
        default_card_position=[0,0,
                               110,0,
                               275,0,
                               0,180,
                               275,180,
                               20,0,
                               20,0,
                               20,0,
                               0,50,
                               cardset_px,cardset_py];
    }
}
function relocationCardPosition(){
    setDefaultCardPosition();
    var displayedCardElements=document.getElementsByClassName('card');
    var card_position=[0,0,0];
    var strCardImgAlt='';
    var p_card_id='';
    var img_id='';
    var eCardImg;
    var arrayCardImgAlt=[];
    for(var i=0;i<(displayedCardElements.length);i++){
        if(displayedCardElements[i].id.indexOf('ic_a-')!=-1){
            p_card_id=displayedCardElements[i].id.substr(5);
            img_id='ic_c-'+p_card_id;
            eCardImg=document.getElementById(img_id);
            strCardImgAlt=eCardImg.alt;
            arrayCardImgAlt=strCardImgAlt.split(","); // 0=ロケーション(l) 1=方向(d) 2=公開(v) 3=順位(z) 4=(sx) 5=(sy)
            card_position=initCardPosition(p_card_id,arrayCardImgAlt[0],arrayCardImgAlt[3]);
			document.getElementById(displayedCardElements[i].id).style.left=card_position[0]+'px';
			document.getElementById(displayedCardElements[i].id).style.top=card_position[1]+'px';
			document.getElementById(displayedCardElements[i].id).style.zIndex=card_position[2]+'px';
        }
    }
}
(function(){
	// カードスペースの右クリックイベントセット
	document.addEventListener("mousedown",eventCardRightMenu);
})();
</script>