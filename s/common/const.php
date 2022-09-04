<?php 
/* common */
  define('SITE_TITLE','オンセンルーム'); // サイト名 ※サイト名はデフォルト「オンセンルーム」から必ず変更する
  define('SITE_DESCRIPTION','ブラウザで遊べるTRPGオンラインセッションルームです。'); // サイト説明
  define('CPU_CORE',8); // サーバのCPUコア数を入力する
  define('APP_VERSION','1.04.06'); // バージョン
/* password */
  define('ADMINISTRATOR_PASSWORD','1234'); // 管理者ページ用パスワード
  define('AUTHENTICATION_CODE','12345678abcd'); // ロビーサーバから要求があった際の認証用パスワード（ロビーサーバから渡されるコードとこれが一致したら情報を提供する）
/* ダイスボット（共有） */
  define('REPEAT_ROLL_LIMIT',10); // ロールを繰り返す上限
/* ダイスボット（BCDice専用） */
  define('BAC_ENDPOINT','https://bcdice.trpgonsen.click'); // BCDiceAPIのエンドポイント
/* ダイスボット（オンセンdb専用） */
  define('DICE_ROLL_LIMIT',100); // 振るダイスの数の上限
  define('DICE_SURFACE_LIMIT',100); // ダイス面の上限
  define('ADDITION_VALUE_LIMIT',10000); // 加算できる値の上限
/* 各種上限値 */
  define('ALLOWABLE_LOAD_LIMIT',100); // サーバ負荷によって入場を制限する 設定値範囲(%) : 0～100 , 0=負荷がない場合のみ入場できる 100=最大負荷でも入場できる
  define('PLAYER_CAPACITY',100); // 参加者(見学者含む)を収容できる上限値
  define('PLAYER_ALIVE_TIME',3600); // 参加者が更新をしなくなってから一覧に表示される限界時間(秒)
  define('UPLOAD_BI_LIMIT_SIZE',1048576); // 画像ファイルアップロード上限サイズ
  define('UPLOAD_MS_LIMIT_SIZE',10485760); // 楽曲ファイルアップロード上限サイズ
  define('UPLOAD_CS_LIMIT_SIZE',1048576); // カードセットファイルアップロード上限サイズ
  define('DOWNLOAD_LOG_LIMIT_SIZE',10485760); // ダウンロードできるログの上限サイズ
/* path */
  define('DIR_ROOT',$_SERVER['DOCUMENT_ROOT'].'/'); // ルートとなるファイルロケーションのフルパスを入力する
/* TRPGオンラインセッションSNSサーバー(ロビーサーバ) */
// 基本的には変更しない、ロビーサーバを自設する場合のみ変更
  define('LOBBY_TRANSFER_PROTOCOL','https:'); // デフォルト https:
  define('LOBBY_DOMAIN','trpgsession.click'); // ロビーサイトのドメイン デフォルト trpgsession.click
/* 以下、変更禁止 */
  define('CHAR_SET','UTF-8'); // 内部文字エンコーディング
  define('TRANSFER_PROTOCOL',(isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']==='on'))?'https:':'http:'); // 自サバがSSLならhttps:、そうでない場合はhttp:と設定する
  define('THIS_DOMAIN',$_SERVER["HTTP_HOST"]); // サイトのドメインを入力する
  define('URL_ROOT',TRANSFER_PROTOCOL.'//'.THIS_DOMAIN.'/'); // URLルート
  define('LOBBY_URL_ROOT',LOBBY_TRANSFER_PROTOCOL.'//'.LOBBY_DOMAIN.'/'); // ルームに入るための入り口となるサイトのURL（https:なども含む）を入力する 変更しない場合は https://trpgsession.click/
  define('LOBBY_URL_SP_ROOT',LOBBY_URL_ROOT.'sp/'); // スマホ版TRPGオンラインセッションSNSサーバー
  define('URL_DEFAULT_MAPCHIP',URL_ROOT.'images/mapchip/mc_1.png'); // デフォルト・マップチップ