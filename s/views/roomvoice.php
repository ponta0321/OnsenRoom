<?php if($voice_invite_code!=-1){ ?>
    <p><label class="font_bold">DISCORDのボイスチャットを使用する</label></p>
    <p class="font_xsmall" style="margin-top:10px;">＜使い方＞</p>
    <p class="font_xsmall" style="margin:10px 0;">～はじめて接続する場合～</p>
    <ol>
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">下の「ボイスチャット」ボタンを押し、DISCORDのページが開きます。</li>
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">ニックネームを入力し、「続ける」ボタンを押します。</li>
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">僕はロボットじゃないよチェックを画面に従って完了させます。</li>
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">自動的に接続が完了します。</li>
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">ブラウザ操作でこのルームに戻してください。</li>
    </ol>
    <p class="font_xsmall" style="margin:20px 0 5px 0;">～２回目から～</p>
    <ol style="margin-bottom:30px;">
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">下の「ボイスチャット」ボタンを押し、DISCORDのページが開きます。</li>
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">自動的に接続が完了します。</li>
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">ブラウザ操作でこのルームに戻します。</li>
    </ol>
    <p class="font_xsmall" style="margin:20px 0 5px 0;">～終了するとき～</p>
    <ul style="margin-bottom:30px;">
        <li class="font_xsmall" style="margin:3px 10px 3px 20px;">DISCORDのページを閉じます。</li>
    </ul>
    <div class="discord_button"><a href="#" onClick="pushStartVoiceChat();"><img src="<?=URL_ROOT;?>images/m_icon125.png" width="200" height="100" border="0" /></a></div>
    <p class="font_red font_xsmall" style="margin-top:30px;">※留意点<br>IEでは動作しません。ブラウザはChromeを推奨します。</p>
<?php }?>