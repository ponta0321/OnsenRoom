# OnsenRoom ソース構造ガイド (Claude 向け)

本ファイルは `source/OnsenRoom/` 配下のソースコード構造を Claude がすばやく把握するためのガイドです。
プロジェクト全体の方針は [../../CLAUDE.md](../../CLAUDE.md) を参照。
本体 (TRPGオンセン) との連携仕様は [../../document/onsenroom_integration_handover.md](../../document/onsenroom_integration_handover.md) を参照。

---

## 1. 基本情報

- **言語**: PHP (サーバサイド) + JavaScript / jQuery (クライアント)
- **git**: `origin: https://github.com/ponta0321/OnsenRoom.git` (branch: `main` / `master`)
- **現行バージョン**: 1.05.00 (`s/common/const.php` の `APP_VERSION`)
- **DB**: なし。ファイルベース (room 配下に xml/php で保存)

## 2. エントリポイント

| ファイル | 役割 |
|---|---|
| `index.php` | トップ (ルーム一覧 / 新規作成 UI) |
| `roomin.php` | 入室処理 (本体から `?rn=...&tk=...` 等でリダイレクトされる) |
| `room.php` / `room-sp.php` | ルーム本体 (PC 版 / スマホ版) |
| `roomout.php` | 退室後の案内 |
| `administer.php` | 管理者ページ (`ADMINISTRATOR_PASSWORD` で保護) |
| `exit.php` | 共通の終了処理 |
| `favicon.ico` | ファビコン |

## 3. ディレクトリ構成

```
OnsenRoom/
├── index.php, roomin.php, room.php, room-sp.php, roomout.php,
│   administer.php, exit.php
│
├── exe/                            ← 本体 (TRPGオンセン) ↔ ルーム 用 API、クライアントからの Ajax
│   ├── createroom.php              ← [本体→] ルーム作成/更新 (flag=create_room/update_room)
│   ├── deleteroom.php              ← [本体→] ルーム削除
│   ├── getroomlist.php             ← [本体→] ルームリスト取得 (60秒ポーリング)
│   ├── getroominfo.php             ← ルーム情報取得
│   ├── diceroll.php                ← ダイス処理 (BCDice 連携含む)
│   ├── pushdicebottext.php         ← ダイスボット発言
│   ├── putcomment.php              ← チャット発言投稿
│   ├── putpositiondata.php         ← コマ位置更新
│   ├── putcardevent.php            ← カード操作
│   ├── putsettingdata.php          ← ルーム設定更新
│   ├── putresetchessman.php        ← コマ全消し
│   ├── putsysmsg.php               ← システムメッセージ
│   ├── getcharacterdata.php        ← キャラデータ取得 (本体へプロキシ)
│   ├── setcharacterlist.php        ← キャラ一覧設定
│   ├── reloadcharacterlist.php     ← キャラ一覧再取得
│   ├── loadcharacterdata.php       ← キャラデータ読込
│   ├── savecharacterdata.php       ← キャラデータ保存
│   ├── modcharacter.php            ← キャラ編集
│   ├── getchartemplate.php         ← キャラテンプレ取得
│   ├── getssdata.php               ← シナリオセット取得
│   ├── getreplay.php               ← リプレイ取得
│   ├── download-board.php          ← ボードデータDL
│   ├── download-log.php            ← ログDL
│   ├── uploadbgi.php               ← 背景画像UP
│   ├── uploadcharimage.php         ← キャラ画像UP
│
├── s/                              ← Server-side 共有コード
│   ├── common/                     ← コア処理
│   │   ├── core.php                ← 全エントリで最初に require (timezone/encoding/const 読込)
│   │   ├── const.php               ← ★定数定義 (サイト名、認証コード、各種上限値、ロビー連携設定)
│   │   ├── class.php               ← classRoomList 等
│   │   ├── function.php            ← 共通関数
│   │   ├── basicfunction.php       ← 文字列処理等の基本関数
│   │   ├── exefunction.php         ← exe/ 配下から使う処理
│   │   ├── exeboneandcars.php      ← Bone&Cars (旧式ダイスボット) 連携
│   │   ├── exeonsendb.php          ← オンセンDB 連携
│   │   ├── exewritelog.php         ← ログ書き込み
│   │   ├── bcdiceapiv2class.php    ← BCDice API v2 クライアント
│   │   └── initroom.php            ← ルーム初期化
│   │
│   ├── db/                         ← ダイスボット定義 (オンセンDB系)
│   │   ├── dicebot_basic.php
│   │   ├── dicebot_g1.php         (CoC)
│   │   ├── dicebot_g14.php
│   │   └── dicebot_g16.php
│   │
│   ├── list/                       ← 各種マスタリスト
│   │   ├── trpg_sys_list.php       ← TRPG システム一覧 (g1=CoC, g99=汎用 等)
│   │   ├── dicebot_textlist.php    ← ダイスボット説明文
│   │   ├── bac_gamelist.php        ← Bone&Cars 対応ゲーム一覧
│   │   ├── templatechar.php        ← キャラテンプレ
│   │   ├── backimage_list.txt      ← プリセット背景画像一覧
│   │   ├── mapchip_list.txt        ← プリセットマップチップ
│   │   ├── chessman_list.txt       ← プリセットコマ
│   │   ├── cardset_list.txt        ← プリセットカードセット
│   │   ├── music_list.txt          ← プリセットBGM
│   │   └── permission_url_list.php ← URL ホワイトリスト
│   │
│   └── views/                      ← View テンプレ (room*.php 内で include)
│       ├── head3.php / head3-sp.php          ← HTML head
│       ├── roomad.php / roomad-sp.php        ← 広告枠 (OSS 版は空、公式専用設定で上書き)
│       ├── roomcardset.php, roomcardspace.php ← カード機能
│       ├── roomcharsheet.php                  ← キャラシ表示
│       ├── roomchatpalette.php                ← チャットパレット
│       ├── roomchatspace.php                  ← チャット欄
│       ├── roomchessboard.php / roomchessboard-sp.php ← ボード
│       ├── roomchessman.php / roomchessman-sp.php     ← コマ
│       ├── roomdiceroll.php                   ← ダイスロール UI
│       ├── roomhelp.php                       ← ヘルプ
│       ├── roommappingtool.php                ← マッピング
│       ├── roommemo.php                       ← メモ
│       ├── roomscenarioset.php                ← シナリオセット
│       ├── roomsetting.php, roomsetting2.php  ← 設定
│       ├── roomtimer.php                      ← タイマー
│       ├── roomurl.php                        ← ルーム URL 表示
│       └── roomvoice.php                      ← Discord voice (※ 2026-05-27 本体側廃止、残骸)
│
├── js/                             ← クライアント JS
│   ├── const.js                    ← 定数
│   ├── jquery-3.1.0.min.js
│   └── jquery-ui.min.js
│
├── css/                            ← スタイル
│   ├── common.css, pc.css, sp.css
│   ├── jquery-ui.min.css
│   └── images/
│
├── images/                         ← 画像素材 (ダイス, 立ち絵, マップチップ 等)
│   ├── dice*.png, dice*.gif
│   ├── mapchip/
│   ├── chessman/
│   ├── animal/, demihuman/
│   └── cardback02.jpg
│
├── sounds/                         ← 効果音
│   └── se01.mp3, se02.mp3
│
├── cardset/                        ← 追加カードセット設置先
│   └── readme.txt
│
├── r/                              ← ルームデータ実体 (runtime 生成)
│   └── n/
│       └── (ルーム単位の xml/php データ)
│
├── log/                            ← アクセス/エラーログ (runtime 生成)
│
├── README.md                       ← OSS 公開用 README
├── CHANGELOG.md                    ← 変更履歴
└── LICENSE                         ← MIT
```

## 4. 主要な定数 (`s/common/const.php`)

| 定数 | 役割 | OSS 版初期値 |
|---|---|---|
| `APP_VERSION` | バージョン文字列 | `1.05.00` |
| `SITE_TITLE` | サイト名 | `オンセンルーム` |
| `AUTHENTICATION_CODE` | 本体 ↔ ルーム サーバ間認証コード (ハンドオーバー §3-1) | `12345678abcd` (**公式鯖では別値**) |
| `ADMINISTRATOR_PASSWORD` | 管理者ページ用 | `1234` (**公式鯖では別値**) |
| `BAC_ENDPOINT` | BCDice API v2 エンドポイント | `https://bcdice.trpgonsenroom.com` |
| `CPU_CORE` | サーバコア数 (負荷判定に使用) | `8` |
| `PLAYER_CAPACITY` | 収容人数上限 | `100` |
| `PLAYER_ALIVE_TIME` | 無更新タイムアウト秒 | `3600` |
| `UPLOAD_BI_LIMIT_SIZE` | 背景画像UP上限 | 1MB |
| `UPLOAD_MS_LIMIT_SIZE` | 楽曲UP上限 | 10MB |
| `UPLOAD_CS_LIMIT_SIZE` | カードセットUP上限 | 1MB |
| `DOWNLOAD_LOG_LIMIT_SIZE` | ログDL上限 | 10MB |
| `LOBBY_DOMAIN` | 本体ドメイン | `trpgsession.com` |

### 公式鯖で上書きされる値 (`../公式専用設定/s/common/const.php` 参照)

- `AUTHENTICATION_CODE = 'f-5Wq12aP_954wUn-A'`
- `ADMINISTRATOR_PASSWORD = '7z9x2s_5a46e83g'`
- `DIR_ROOT = '/home/Cn8wQsfDe48dhRo8/trpgonsenroom.com/'`
- `BAC_ROOT = '/home/Cn8wQsfDe48dhRo8/trpgonsenroom.com/BCDice20800/'`
- `THIS_DOMAIN = 'trpgonsen.com'` (※ 現行は `trpgonsenroom.com` のはず。要確認)
- `APP_VERSION = '1.04.03'` (※ OSS 版より古い。要確認)

## 5. 認証フロー (サーバ側)

`exe/` 配下の各 PHP の冒頭パターン:

```php
require('../s/common/core.php');
if(isset($_POST['auth_code'])){
    if(!checkAuth($_POST['auth_code'])){
        echo json_encode(array('error'=>'authentication_error', ...));
        exit;
    }
}else{
    echo json_encode(array('error'=>'not_access_from_lobby', ...));
    exit;
}
```

`checkAuth()` は `$_POST['auth_code'] === AUTHENTICATION_CODE` を確認している (ハンドオーバー §3-1 の `auth_code`)。
ハンドオーバーに記載されているすべての `exe/*.php` エンドポイントはこの認証を通る必要あり。

## 6. ルームデータの保存形式

- 1 ルーム = 1 ディレクトリ `r/n/<room_id>/`
- 中に XML (`room.xml`, `comment.xml` 等) と PHP (キャッシュ) が混在
- ルーム一覧は `r/roomlist.php` (eval で読む独自形式。ハンドオーバー §6-1 のスキーマ 16 フィールド)

## 7. BCDice 連携

- v2 (API 版): `s/common/bcdiceapiv2class.php` で `BAC_ENDPOINT` を叩く
- 旧版 (Bone&Cars = Ruby 直叩き): `s/common/exeboneandcars.php` が `BAC_ROOT` 配下の Ruby を exec
- 公式鯖には `BCDice20277` 〜 `BCDice20800` が同梱され、古いルームとの互換維持に使われている

## 8. フロントエンド

- jQuery 3.1.0 + jQuery UI
- `js/const.js` 以外に view 側で inline で書かれている JS が多数ある前提
- 開発計画 (`document/開発計画_オンセンルーム.txt`) に **「jQuery → 素のJS への脱却」** が記載あり

## 9. 作業上の注意

### 9-1. 公式鯖固有の設定を OSS 側に混入させない

- 公式用の `AUTHENTICATION_CODE` / `ADMINISTRATOR_PASSWORD` / 広告コード等は **絶対に `source/OnsenRoom/` 側へコミットしない**
- 変更が公式鯖固有なら `source/公式専用設定/` の該当パスに置くこと

### 9-2. 本体連携スキーマを壊さない

- `exe/createroom.php` / `exe/deleteroom.php` / `exe/getroomlist.php` のレスポンスフィールドは、
  `document/onsenroom_integration_handover.md` §4, §6 と**完全一致**を維持
- フィールド追加は OK (末尾に足す)、既存の削除/型変更は不可
- Discord 系フィールド (`voice_id`, `discord_room_id`) は廃止済みだが**残す**

### 9-3. git 操作

- `source/OnsenRoom/` は OSS 公開リポジトリ。コミット/プッシュは **ユーザから明示指示があった時のみ**
- origin HEAD は `master` を指しているが、現在のローカルは `main` ブランチ。要注意

### 9-4. PHP バージョン

- 本番環境は PHP 8 (本体側は Rocky Linux 9.3 + PHP 8)
- 本体の cron が `exec("php74 ...")` で呼ぶ箇所あり → CLI バイナリ名が環境依存

### 9-5. 既知の技術的負債

- `exe/updatechardata.php` 相当がない → ルームから本体のキャラ HP/MP 更新できない (ハンドオーバー §5-4)
- 大量の view ファイルに inline JS が散在 → リファクタリング未着手
- 旧式 Bone&Cars のコード残存 → BCDice API v2 への完全移行は未完了
