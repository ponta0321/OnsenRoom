<div class="mappingtool_box">
    <div id="mt_ln_del" class="chessma_box_ac" onClick='putAcMappingTool("mt_bx_del")'>消去&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）</div>
    <div id="mt_bx_del" class="chessman_box">
        <img id="mappingtool_ic_d" src="<?=URL_ROOT;?>images/m_icon133.png" width="32" height="32" onClick="clearAllMapping()" border="0" /><img id="mappingtool_ic_0" class="actived_mappingtool" src="<?=URL_ROOT;?>images/m_icon132.png" width="32" height="32" onClick="setMappingTool(0)" border="0" />
    </div>
    <div id="mt_ln_line" class="chessma_box_ac" onClick='putAcMappingTool("mt_bx_line")'>ライン描画&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）</div>
    <div id="mt_bx_line" class="chessman_box"></div>
    <div id="mt_ln_deco" class="chessma_box_ac" onClick='putAcMappingTool("mt_bx_deco")'>デコレーション貼り付け&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）</div>
    <div id="mt_bx_deco" class="chessman_box"></div>
</div>
<script>
    // PC版用 画面折りたたみ
    function putAcMappingTool(targetid){
        var tagid=['mt_ln_del','mt_ln_line','mt_ln_deco'];
        var tagtargetid=['mt_bx_del','mt_bx_line','mt_bx_deco'];
        var tagname=['消去','ライン描画','デコレーション貼り付け'];
        if(document.getElementById(targetid).style.display=="none"){
            document.getElementById(targetid).style.display="block";
            for(i=0;i<3;i++){
                if(tagtargetid[i]==targetid){
                    document.getElementById(tagid[i]).innerHTML=tagname[i]+'&nbsp;&nbsp;（▼&nbsp;クリックで閉じる&nbsp;▼）';
                }
            }
        }else{
            document.getElementById(targetid).style.display="none";
            for(i=0;i<3;i++){
                if(tagtargetid[i]==targetid){
                    document.getElementById(tagid[i]).innerHTML=tagname[i]+'&nbsp;&nbsp;（▼&nbsp;クリックで開く&nbsp;▼）';
                }
            }
        }
    }
    putAcMappingTool("mt_bx_line");
    putAcMappingTool("mt_bx_deco");
</script>