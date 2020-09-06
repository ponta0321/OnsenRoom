<?php
/*-----------------------------------------------------------------
Bone&Carsのダイスボットリスト一覧
    Ver2.08.00 対応
配列
　　　　Keyの命名ルール
　　　　　　Bone&Carsのフォルダ scr/diceBot のファイル名 の前に bac_ をつける
　　　 [0]=ダイスボット名 [1]=ダイスボット説明(基本未使用) [2]=オンセン用キー
未使用にするダイスボットのルール
    韓国語版、18禁(絶対隷奴など)
-----------------------------------------------------------------*/
$default_preset_comment=
'《タグ》 キャラクターシートに登録されたタグの値を出力します。※
【例】 タグ名:敏捷度、値:7　　【入力】 #敏捷度

《マクロ》 キャラクターシートに登録されたマクロの内容を出力します。※
【例】 マクロ名:目星、内容:1d100>=#目星  【入力】 $目星

※タグ・マクロを使うには発言者をそのキャラにしておく必要があります。

《ウィスパー送信》 特定の相手にだけ見えるメッセージを送信します。
【例】 送信先: ts12  【入力】 @ts12 お腹減った

《プロット》 本人以外には伏せた状態でコメントを送信します。
【入力】 /p お腹減った  【出力】 （プロット）

《コメント公開》 シークレットダイスやプロットを全員に公開します。
【例】 公開したいコメントID: #wp515　　【入力】 /o #wp515
【例】 最後に発言したシークレットダイスorプロット　　【入力】 /ol

《ダイスボット「ボーンズ＆カーズ」》 コマンドでサイコロを振ります。
【例】
2d8 ： 8面ダイスを2個振ります。
1d6+1d4-5 ： 6面ダイスと4面ダイスを振り、結果から5を引きます。
1d100>=50 ： 100面ダイスを振り、50以上は成功、未満は失敗。
1d100>=25 聞き耳 : 合否判定ロールに名称「聞き耳」をつけて振ります。
10b6 ： 6面ダイスを10個振り、全てダイスの和は出しません。
3b6>3 ： 6面ダイスを3個振り、条件3以上にあうダイスがいくつあるのか数えます。
3r6>=3 ： 6面ダイスを3個振り、その成功数の個数分さらにロールして成功度を加算します。
3u6[6]>=3 ： 結果が上方無限境界値以上になると再度振り、その値をダイス目に加えます。
choice[1,3,5] ： 列挙した要素1,3,5から一つを選択表示します。
s2d6 ： 6面ダイスを2個振り、結果は振った本人にのみ分かります。
3 2d4 ： 4面ダイスを2個振ります。これを3回繰り返します。 
[1...5]D6 ： 6面ダイスを1～5個振ります。 
';
$bac_gamelist=array();
$bac_gamelist['bac_DiceBot']=array('指定なし','','g99');

$bac_gamelist['bac_AceKillerGene']=array('エースキラージーン','','g117');
$bac_gamelist['bac_AFF2e']=array('AFF2e','','g299');
$bac_gamelist['bac_Airgetlamh']=array('朱の孤塔のエアゲトラム','','g99');
$bac_gamelist['bac_Alsetto']=array('詩片のアルセット','','g256');
$bac_gamelist['bac_Alshard']=array('アルシャード','','g5');
$bac_gamelist['bac_Alter_raise']=array('心衝想機TRPGアルトレイズ','','g99');
$bac_gamelist['bac_Amadeus']=array('アマデウス','','g35');
//$bac_gamelist['bac_Amadeus_Korean']=array('アマデウス','','g35');
$bac_gamelist['bac_AnimaAnimus']=array('アニマアニムス','','g308');
$bac_gamelist['bac_Arianrhod']=array('アリアンロッド','','g3');
$bac_gamelist['bac_ArsMagica']=array('アルスマギカ','','g99');
$bac_gamelist['bac_Avandner']=array('黒絢のアヴァンドナー','','g130');
$bac_gamelist['bac_BadLife']=array('犯罪活劇RPGバッドライフ','','g99');
$bac_gamelist['bac_BarnaKronika']=array('バルナ・クロニカ','','g49');
$bac_gamelist['bac_BattleTech']=array('バトルテック','','g69');
$bac_gamelist['bac_BBN']=array('BBNTRPG','','g99');
$bac_gamelist['bac_BeastBindTrinity']=array('ビーストバインド トリニティ','','g59');
$bac_gamelist['bac_BeginningIdol']=array('ビギニングアイドル','','g56');
//$bac_gamelist['bac_BeginningIdol_Korean']=array('ビギニングアイドル','','g56');
$bac_gamelist['bac_BladeOfArcana']=array('ブレイド・オブ・アルカナ','','g23');
$bac_gamelist['bac_BlindMythos']=array('ブラインド・ミトス','','g266');
$bac_gamelist['bac_BloodCrusade']=array('ブラッド・クルセイド','','g60');
$bac_gamelist['bac_BloodMoon']=array('ブラッドムーン','','g39');
$bac_gamelist['bac_CardRanker']=array('カードランカー','','g62');
$bac_gamelist['bac_ChaosFlare']=array('カオスフレア','','g51');
$bac_gamelist['bac_Chill']=array('Chill','','g99');
$bac_gamelist['bac_Chill3']=array('Chill 3','','g99');
$bac_gamelist['bac_CodeLayerd']=array('コード:レイヤード','','g119');
$bac_gamelist['bac_ColossalHunter']=array('コロッサルハンター','','g243');
$bac_gamelist['bac_CrashWorld']=array('墜落世界','','g99');
$bac_gamelist['bac_Cthulhu']=array('クトゥルフ','','g2');
//$bac_gamelist['bac_Cthulhu_ChineseTraditional']=array('クトゥルフ','','g2');
//$bac_gamelist['bac_Cthulhu_Korean']=array('クトゥルフ','','g2');
$bac_gamelist['bac_Cthulhu7th']=array('クトゥルフ第7版','','g270');
//$bac_gamelist['bac_Cthulhu7th_ChineseTraditional']=array('クトゥルフ第7版','','g2');
//$bac_gamelist['bac_Cthulhu7th_Korean']=array('クトゥルフ第7版','','g2');
$bac_gamelist['bac_CthulhuTech']=array('クトゥルフテック','','g2');
$bac_gamelist['bac_DarkBlaze']=array('ダークブレイズ','','g92');
$bac_gamelist['bac_DarkDaysDrive']=array('ダークデイズドライブ','','g252');
$bac_gamelist['bac_DarkSouls']=array('DARK SOULS','','g248');
$bac_gamelist['bac_DeadlineHeroes']=array('デッドラインヒーローズ','','g247');
$bac_gamelist['bac_DemonParasite']=array('デモンパラサイト','','g120');
$bac_gamelist['bac_DetatokoSaga']=array('でたとこサーガ','','g27');
//$bac_gamelist['bac_DetatokoSaga_Korean']=array('でたとこサーガ','','g27');
$bac_gamelist['bac_DiceOfTheDead']=array('ダイス・オブ・ザ・デッド','','g55');
$bac_gamelist['bac_DoubleCross']=array('ダブルクロス','','g14');
$bac_gamelist['bac_Dracurouge']=array('ドラクルージュ','','g44');
//$bac_gamelist['bac_Dracurouge_Korean']=array('ドラクルージュ','','g44');
$bac_gamelist['bac_DungeonsAndDoragons']=array('ダンジョンズ＆ドラゴンズ','','g20');
$bac_gamelist['bac_EarthDawn']=array('アースドーン','','g71');
$bac_gamelist['bac_EarthDawn3']=array('アースドーン3版','','g71');
$bac_gamelist['bac_EarthDawn4']=array('アースドーン4版','','g71');
$bac_gamelist['bac_EclipsePhase']=array('エクリプス・フェイズ','','g53');
$bac_gamelist['bac_Elric']=array('エルリック！','','g233');
$bac_gamelist['bac_Elysion']=array('エリュシオン','','g72');
$bac_gamelist['bac_EmbryoMachine']=array('エムブリオマシン','','g106');
$bac_gamelist['bac_EndBreaker']=array('エンドブレイカー','','g86');
$bac_gamelist['bac_EtrianOdysseySRS']=array('世界樹の迷宮SRS','','g87');
$bac_gamelist['bac_FilledWith']=array('フィルトウィズ','','g99');
$bac_gamelist['bac_FullMetalPanic']=array('フルメタル・パニック!RPG','','g58');
$bac_gamelist['bac_FutariSousa']=array('フタリソウサ','','g285');
$bac_gamelist['bac_Garako']=array('ガラコと破界の塔','','g99');
$bac_gamelist['bac_GardenOrder']=array('ガーデンオーダー','','g64');
$bac_gamelist['bac_GehennaAn']=array('ゲヘナ―アナスタシス','','g113');
$bac_gamelist['bac_GeishaGirlwithKatana']=array('ゲイシャ・ガール・ウィズ・カタナ','','g99');
$bac_gamelist['bac_GoblinSlayer']=array('ゴブリンスレイヤーTRPG','','g278');
$bac_gamelist['bac_GoldenSkyStories']=array('ゆうやけこやけ','','g17');
$bac_gamelist['bac_Gorilla']=array('ゴリラTRPG','','g280');
$bac_gamelist['bac_GranCrest']=array('グランクレスト','','g33');
$bac_gamelist['bac_Gundog']=array('ガンドッグ','','g99');
$bac_gamelist['bac_GundogRevised']=array('ガンドッグ・リヴァイズド','','g99');
$bac_gamelist['bac_GundogZero']=array('ガンドッグ・ゼロ','','g19');
$bac_gamelist['bac_Gurps']=array('ガープス','','g4');
$bac_gamelist['bac_GurpsFW']=array('ガープスフィルトウィズ','','g4');
$bac_gamelist['bac_HarnMaster']=array('ハーンマスター','','g230');
$bac_gamelist['bac_HatsuneMiku']=array('初音ミクTRPG','','g255');
$bac_gamelist['bac_Hieizan']=array('比叡山炎上','','g2');
$bac_gamelist['bac_HouraiGakuen']=array('蓬莱学園の冒険','','g185');
$bac_gamelist['bac_HuntersMoon']=array('ハンターズ・ムーン','','g61');
$bac_gamelist['bac_Illusio']=array('晃天のイルージオ','','g99');
$bac_gamelist['bac_InfiniteFantasia']=array('無限のファンタジア','','g95');
$bac_gamelist['bac_Insane']=array('インセイン','','g36');
//$bac_gamelist['bac_Insane_Korean']=array('インセイン','','g36');
$bac_gamelist['bac_IthaWenUa']=array('イサー・ウェン=アー','','g207');
$bac_gamelist['bac_JamesBond']=array('ジェームズボンド007','','g227');
$bac_gamelist['bac_Kamigakari']=array('神我狩','','g47');
//$bac_gamelist['bac_Kamigakari_Korean']=array('神我狩','','g47');
$bac_gamelist['bac_KanColle']=array('艦これRPG','','g12');
$bac_gamelist['bac_KemonoNoMori']=array('獸ノ森','','g279');
$bac_gamelist['bac_KillDeathBusiness']=array('キルデスビジネス','','g9');
//$bac_gamelist['bac_KillDeathBusiness_Korean']=array('キルデスビジネス','','g9');
$bac_gamelist['bac_KurayamiCrying']=array('クラヤミクライン','','g271');
$bac_gamelist['bac_LiveraDoll']=array('紫縞のリヴラドール','','g265');
$bac_gamelist['bac_LogHorizon']=array('ログ・ホライズン','','g13');
//$bac_gamelist['bac_LogHorizon_Korean']=array('ログ・ホライズン','','g13');
$bac_gamelist['bac_LostRecord']=array('ロストレコード','','g296');
$bac_gamelist['bac_LostRoyal']=array('ロストロイヤル','','g118');
$bac_gamelist['bac_MagicaLogia']=array('マギカロギア','','g18');
$bac_gamelist['bac_MeikyuDays']=array('迷宮デイズ','','g245');
$bac_gamelist['bac_MeikyuKingdom']=array('迷宮キングダム','','g50');
$bac_gamelist['bac_MeikyuKingdomBasic']=array('迷宮キングダム 基本ルールブック','','g50');
$bac_gamelist['bac_MetalHead']=array('メタルヘッド','','g31');
$bac_gamelist['bac_MetalHeadExtream']=array('メタルヘッドエクストリーム','','g31');
$bac_gamelist['bac_MetallicGuadian']=array('メタリックガーディアン','','g34');
$bac_gamelist['bac_MonotoneMusium']=array('モノトーンミュージアムRPG','','g7');
//$bac_gamelist['bac_MonotoneMusium_Korean']=array('モノトーンミュージアムRPG','','g7');
$bac_gamelist['bac_Nechronica']=array('ネクロニカ','','g6');
//$bac_gamelist['bac_Nechronica_Korean']=array('ネクロニカ','','g6');
$bac_gamelist['bac_NeverCloud']=array('ネバークラウドTRPG','','g99');
$bac_gamelist['bac_NightmareHunterDeep']=array('ナイトメアハンター=ディープ','','g108');
$bac_gamelist['bac_NightWizard']=array('ナイトウィザード','','g25');
$bac_gamelist['bac_NightWizard3rd']=array('ナイトウィザード3ｒｄ','','g25');
$bac_gamelist['bac_NinjaSlayer']=array('ニンジャスレイヤーTRPG','','g284');
$bac_gamelist['bac_NjslyrBattle']=array('NJSLYRBATTLE','','g99');
$bac_gamelist['bac_Nuekagami']=array('鵺鏡','','g45');
$bac_gamelist['bac_OneWayHeroics']=array('片道勇者','','g41');
$bac_gamelist['bac_OracleEngine']=array('オラクルエンジン','','g99');
$bac_gamelist['bac_Oukahoushin3rd']=array('央華封神RPG第3版','','g166');
$bac_gamelist['bac_Paradiso']=array('チェレステ色のパラディーゾ','','g99');
$bac_gamelist['bac_Paranoia']=array('パラノイア','','g11');
$bac_gamelist['bac_ParanoiaRebooted']=array('パラノイア リブーテッド','','g11');
$bac_gamelist['bac_ParasiteBlood']=array('パラサイトブラッド','','g54');
$bac_gamelist['bac_Pathfinder']=array('Pathfinder','','g70');
$bac_gamelist['bac_Peekaboo']=array('ピーカーブー','','g40');
$bac_gamelist['bac_Pendragon']=array('ペンドラゴン','','g99');
$bac_gamelist['bac_PhantasmAdventure']=array('ファンタズムアドベンチャー','','g151');
$bac_gamelist['bac_Postman']=array('壊れた世界のポストマン','','g99');
$bac_gamelist['bac_PulpCthulhu']=array('パルプ・クトゥルフ','','g99');
$bac_gamelist['bac_Raisondetre']=array('叛逆レゾンデートル','','g99');
$bac_gamelist['bac_RecordOfLodossWar']=array('ロードス島戦記RPG','','g66');
$bac_gamelist['bac_RecordOfSteam']=array('Record of Steam','','g99');
$bac_gamelist['bac_RokumonSekai2']=array('六門世界2nd','','g109');
$bac_gamelist['bac_RoleMaster']=array('ロールマスター','','g236');
$bac_gamelist['bac_ROrgaRain']=array('在りて遍くオルガレイン','','g99');
$bac_gamelist['bac_RuneQuest']=array('ルーンクエスト','','g163');
$bac_gamelist['bac_Ryutama']=array('りゅうたま','','g46');
$bac_gamelist['bac_RyuTuber']=array('リューチューバーとちいさな奇跡','','g99');
$bac_gamelist['bac_SamsaraBallad']=array('サンサーラ・バラッド','','g297');
$bac_gamelist['bac_Satasupe']=array('サタスペ','','g24');
$bac_gamelist['bac_ScreamHighSchool']=array('スクリームハイスクール','','g291');
$bac_gamelist['bac_SevenFortressMobius']=array('セブン=フォートレスメビウス','','g98');
$bac_gamelist['bac_ShadowRun']=array('シャドウラン','','g26');
$bac_gamelist['bac_ShadowRun4']=array('シャドウラン第4版','','g26');
$bac_gamelist['bac_ShadowRun5']=array('シャドウラン第5版','','g26');
$bac_gamelist['bac_SharedFantasia']=array('Shared†Fantasia','','g79');
$bac_gamelist['bac_ShinkuuGakuen']=array('真空学園','','g99');
$bac_gamelist['bac_ShinMegamiTenseiKakuseihen']=array('真・女神転生TRPG覚醒篇','','g28');
$bac_gamelist['bac_ShinobiGami']=array('シノビガミ','','g10');
$bac_gamelist['bac_ShoujoTenrankai']=array('少女展爛会','','g260');
$bac_gamelist['bac_Skynauts']=array('歯車の塔の探空士','','g121');
$bac_gamelist['bac_SRS']=array('スタンダードRPGシステム','','g99');
$bac_gamelist['bac_SteamPunkers']=array('スチームパンカーズ','','g309');
$bac_gamelist['bac_StellarKnights']=array('銀剣のステラナイツ','','g275');
$bac_gamelist['bac_SterileLife']=array('ステラーライフ','','g99');
$bac_gamelist['bac_StrangerOfSwordCity']=array('剣の街の異邦人','','g114');
$bac_gamelist['bac_StratoShout']=array('ストラトシャウト','','g286');
$bac_gamelist['bac_Strave']=array('碧空のストレイヴ','','g99');
$bac_gamelist['bac_SwordWorld']=array('ソード・ワールド無印','','g16');
$bac_gamelist['bac_SwordWorld2_0']=array('ソード・ワールド2.0','','g1');
$bac_gamelist['bac_SwordWorld2_5']=array('ソード・ワールド2.5','','g272');
$bac_gamelist['bac_TherapieSein']=array('青春疾患セラフィザイン','','g99');
$bac_gamelist['bac_TokumeiTenkousei']=array('特命転攻生','','g179');
$bac_gamelist['bac_TokyoGhostResearch']=array('東京ゴーストリサーチ','','g99');
$bac_gamelist['bac_TokyoNova']=array('トーキョーN◎VA','','g37');
$bac_gamelist['bac_Torg']=array('トーグ','','g134');
$bac_gamelist['bac_Torg1_5']=array('トーグ1.5版','','g134');
$bac_gamelist['bac_TorgEternity']=array('TORG Eternity','','g134');
$bac_gamelist['bac_TrinitySeven']=array('トリニティセブンRPG','','g289');
$bac_gamelist['bac_TunnelsAndTrolls']=array('Ｔ＆Ｔ','','g57');
$bac_gamelist['bac_TwilightGunsmoke']=array('トワイライトガンスモーク','','g78');
$bac_gamelist['bac_Utakaze']=array('ウタカゼ','','g38');
$bac_gamelist['bac_VampireTheMasquerade5th']=array('ヴァンパイア：ザ マスカレード 第５版','','g122');
$bac_gamelist['bac_Villaciel']=array('蒼天のヴィラシエル','','g290');
$bac_gamelist['bac_WaresBlade']=array('ワースブレイド','','g165');
$bac_gamelist['bac_Warhammer']=array('ウォーハンマー','','g63');
$bac_gamelist['bac_WARPS']=array('ワープス','','g205');
$bac_gamelist['bac_WitchQuest']=array('ウィッチクエスト','','g204');
$bac_gamelist['bac_WorldOfDarkness']=array('ワールド・オブ・ダークネス','','g125');
$bac_gamelist['bac_YankeeYogSothoth']=array('ヤンキー＆ヨグ＝ソトース','','g253');
$bac_gamelist['bac_YearZeroEngine']=array('イヤーゼロエンジン','','g253');
//$bac_gamelist['bac_ZettaiReido']=array('絶対隷奴','','g99');