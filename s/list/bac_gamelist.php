<?php
/*-----------------------------------------------------------------
BCDiceのダイスボットリスト一覧
    Ver3.00.00 対応
配列
　　　　Keyの命名ルール
　　　　BCDice ID の前に bac_ をつける
　　　 [0]=ダイスボット名 [1]=ダイスボット説明(基本未使用) [2]=オンセン用キー
順列
	BCDiceAPIのゲームシステム sork_key昇順
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

《BCDice》 コマンドでサイコロを振ります。
【例】
2d8 ： 8面ダイスを2個振ります。
1d100>=50 ： 100面ダイスを振り、50以上は成功、未満は失敗。
1d100>=25 聞き耳 : 合否判定ロールに名称「聞き耳」をつけて振ります。
10b6 ： 6面ダイスを10個振り、全てダイスの和は出しません。
3b6>3 ： 6面ダイスを3個振り、条件3以上にあうダイスがいくつあるのか数えます。
3r6>=3 ： 6面ダイスを3個振り、その成功数の個数分さらにロールして成功度を加算します。
3u6[6]>=3 ： 結果が上方無限境界値以上になると再度振り、その値をダイス目に加えます。
choice[1,3,5] ： 列挙した要素1,3,5から一つを選択表示します。
s2d6 ： 6面ダイスを2個振り、結果は振った本人にのみ分かります。
3 2d4 ： 4面ダイスを2個振ります。これを3回繰り返します。
';
$bac_gamelist=array(
	'bac_DiceBot'=>array('指定なし','','g99'),
	'bac_EarthDawn'=>array('アースドーン','','g71'),
	'bac_EarthDawn3'=>array('アースドーン3版','','g341'),
	'bac_EarthDawn4'=>array('アースドーン4版','','g342'),
	'bac_Airgetlamh'=>array('朱の孤塔のエアゲトラム','','g332'),
	'bac_AFF2e'=>array('AFF2e','','g299'),
	'bac_AnimaAnimus'=>array('アニマアニムス','','g308'),
	'bac_Amadeus'=>array('アマデウス','','g35'),
	'bac_Arianrhod'=>array('アリアンロッド','','g3'),
	'bac_OrgaRain'=>array('在りて遍くオルガレイン','','g366'),
	'bac_AlchemiaStruggle'=>array('アルケミア・ストラグル','','g385'),
	'bac_Alshard'=>array('アルシャード','','g5'),
	'bac_ArsMagica'=>array('アルスマギカ','','g334'),
	'bac_AlterRaise'=>array('心衝想機TRPGアルトレイズ','','g333'),
	'bac_UnsungDuet'=>array('アンサング・デュエット','','g322'),
	'bac_IthaWenUa'=>array('イサー・ウェン=アー','','g207'),
	'bac_YearZeroEngine'=>array('イヤーゼロエンジン','','g376'),
	'bac_Insane'=>array('インセイン','','g36'),
	'bac_VampireTheMasquerade5th'=>array('ヴァンパイア：ザ・マスカレード5版','','g122'),
	'bac_WitchQuest'=>array('ウィッチクエスト','','g204'),
	'bac_Warhammer'=>array('ウォーハンマー','','g63'),
	'bac_Warhammer4'=>array('ウォーハンマー4版','','g321'),
	'bac_Utakaze'=>array('ウタカゼ','','g38'),
	'bac_Alsetto'=>array('詩片のアルセット','','g256'),
	'bac_AceKillerGene'=>array('エースキラージーン','','g117'),
	'bac_EclipsePhase'=>array('エクリプス・フェイズ','','g53'),
	'bac_NSSQ'=>array('SRSじゃない世界樹の迷宮TRPG','','g412'),
	'bac_EmbryoMachine'=>array('エムブリオマシン','','g106'),
	'bac_Emoklore'=>array('エモクロア','','g99'),
	'bac_Elysion'=>array('エリュシオン','','g72'),
	'bac_Elric'=>array('エルリック！','','g233'),
	'bac_EndBreaker'=>array('エンドブレイカー','','g86'),
	'bac_Oukahoushin3rd'=>array('央華封神RPG第3版','','g358'),
	'bac_OracleEngine'=>array('オラクルエンジン','','g357'),
	'bac_GardenOrder'=>array('ガーデンオーダー','','g64'),
	'bac_CardRanker'=>array('カードランカー','','g62'),
	'bac_GURPS'=>array('ガープス','','g4'),
	'bac_GurpsFW'=>array('ガープスフィルトウィズ','','g348'),
	'bac_ChaosFlare'=>array('カオスフレア','','g51'),
	'bac_OneWayHeroics'=>array('片道勇者','','g41'),
	'bac_Kamigakari'=>array('神我狩','','g47'),
	'bac_Comes'=>array('カムズ','','g408'),
	'bac_Garako'=>array('ガラコと破界の塔','','g344'),
	'bac_KanColle'=>array('艦これRPG','','g12'),
	'bac_Gundog'=>array('ガンドッグ','','g346'),
	'bac_GundogZero'=>array('ガンドッグゼロ','','g19'),
	'bac_GundogRevised'=>array('ガンドッグ・リヴァイズド','','g347'),
	'bac_KillDeathBusiness'=>array('キルデスビジネス','','g9'),
	'bac_StellarKnights'=>array('銀剣のステラナイツ','','g275'),
	'bac_Cthulhu'=>array('クトゥルフ神話TRPG','','g2'),
	'bac_Cthulhu7th'=>array('クトゥルフ神話7版','','g270'),
	'bac_CthulhuTech'=>array('クトゥルフテック','','g340'),
	'bac_KurayamiCrying'=>array('クラヤミクライン','','g271'),
	'bac_GranCrest'=>array('グランクレスト','','g33'),
	'bac_GeishaGirlwithKatana'=>array('ゲイシャ・ガール・ウィズ・カタナ','','g345'),
	'bac_GehennaAn'=>array('アナスタシス','','g113'),
	'bac_KemonoNoMori'=>array('獸ノ森','','g279'),
	'bac_StrangerOfSwordCity'=>array('剣の街の異邦人','','g114'),
	'bac_Yggdrasill'=>array('鋼鉄のユグドラシル','','g410'),
	'bac_Illusio'=>array('晃天のイルージオ','','g350'),
	'bac_CodeLayerd'=>array('コード:レイヤード','','g119'),
	'bac_Avandner'=>array('黒絢のアヴァンドナー','','g130'),
	'bac_GoblinSlayer'=>array('ゴブリンスレイヤーTRPG','','g278'),
	'bac_Gorilla'=>array('ゴリラTRPG','','g280'),
	'bac_ColossalHunter'=>array('コロッサルハンター','','g243'),
	'bac_Postman'=>array('壊れた世界のポストマン','','g361'),
	'bac_Satasupe'=>array('サタスペ','','g24'),
	'bac_SamsaraBallad'=>array('サンサーラ・バラッド','','g297'),
	'bac_SharedFantasia'=>array('Shared†Fantasia','','g79'),
	'bac_JamesBond'=>array('ジェームズボンド007','','g227'),
	'bac_JekyllAndHyde'=>array('ジキルとハイドとグリトグラ','','g411'),
	'bac_LiveraDoll'=>array('紫縞のリヴラドール','','g265'),
	'bac_ShinobiGami'=>array('シノビガミ','','g10'),
	'bac_ShadowRun'=>array('シャドウラン','','g26'),
	'bac_ShadowRun4'=>array('シャドウラン第4版','','g369'),
	'bac_ShadowRun5'=>array('シャドウラン第5版','','g370'),
	'bac_JuinKansen'=>array('呪印感染','','g292'),
	'bac_ShoujoTenrankai'=>array('少女展爛会','','g260'),
	'bac_InfiniteBabeL'=>array('Infinite BabeL','','g260'),
	'bac_ShinkuuGakuen'=>array('真空学園','','g371'),
	'bac_ShinMegamiTenseiKakuseihen'=>array('真・女神転生TRPG覚醒篇','','g28'),
	'bac_Skynauts'=>array('歯車の塔の探空士','','g121'),
	'bac_ScreamHighSchool'=>array('スクリームハイスクール','','g291'),
	'bac_SRS'=>array('スタンダードRPGシステム','','g99'),
	'bac_SteamPunkers'=>array('スチームパンカーズ','','g309'),
	'bac_SterileLife'=>array('ステラーライフ','','g356'),
	'bac_StratoShout'=>array('ストラトシャウト','','g286'),
	'bac_EtrianOdysseySRS'=>array('世界樹の迷宮SRS','','g87'),
	/*'bac_ZettaiReido'=>array('絶対隷奴','','g99'),*/
	'bac_SevenFortressMobius'=>array('セブン=フォートレスメビウス','','g98'),
	'bac_TherapieSein'=>array('青春疾患セラフィザイン','','g373'),
	'bac_Villaciel'=>array('蒼天のヴィラシエル','','g290'),
	'bac_SwordWorld'=>array('ソード・ワールド無印','','g16'),
	'bac_SwordWorld2.0'=>array('ソード・ワールド2.0','','g1'),
	'bac_SwordWorld2.5'=>array('ソード・ワールド2.5','','g272'),
	'bac_DarkSouls'=>array('DARK SOULS','','g248'),
	'bac_DarkDaysDrive'=>array('ダークデイズドライブ','','g252'),
	'bac_DarkBlaze'=>array('ダークブレイズ','','g92'),
	'bac_DiceOfTheDead'=>array('ダイス・オブ・ザ・デッド','','g55'),
	'bac_DoubleCross'=>array('ダブルクロス2nd,3rd','','g14'),
	'bac_DungeonsAndDragons'=>array('ダンジョンズ＆ドラゴンズ','','g20'),
	'bac_Paradiso'=>array('チェレステ色のパラディーゾ','','g319'),
	'bac_Chill'=>array('Chill','','g337'),
	'bac_Chill3'=>array('Chill 3','','g338'),
	'bac_CrashWorld'=>array('墜落世界','','g339'),
	'bac_DesperateRun'=>array('Desperate Run TRPG','','g409'),
	'bac_DetatokoSaga'=>array('でたとこサーガ','','g27'),
	'bac_DeadlineHeroes'=>array('デッドラインヒーローズ','','g247'),
	'bac_DemonParasite'=>array('デモンパラサイト','','g120'),
	'bac_TokyoGhostResearch'=>array('東京ゴーストリサーチ','','g374'),
	'bac_TokyoNova'=>array('トーキョーN◎VA','','g37'),
	'bac_Torg'=>array('トーグ','','g134'),
	'bac_Torg1.5'=>array('トーグ リヴァイスド','','g267'),
	'bac_TorgEternity'=>array('トーグ エタニティ','','g375'),
	'bac_TokumeiTenkousei'=>array('特命転攻生','','g179'),
	'bac_Dracurouge'=>array('ドラクルージュ','','g44'),
	'bac_TrinitySeven'=>array('トリニティセブンRPG','','g289'),
	'bac_TwilightGunsmoke'=>array('トワイライトガンスモーク','','g78'),
	'bac_TunnelsAndTrolls'=>array('Ｔ＆Ｔ','','g57'),
	'bac_NightWizard'=>array('ナイトウィザード','','g25'),
	'bac_NightWizard3rd'=>array('ナイトウィザード3rd','','g355'),
	'bac_NightmareHunterDeep'=>array('ナイトメアハンター=ディープ','','g277'),
	'bac_NinjaSlayer'=>array('ニンジャスレイヤーTRPG','','g284'),
	'bac_NjslyrBattle'=>array('NJSLYRBATTLE','','g99'),
	'bac_Nuekagami'=>array('鵺鏡','','g45'),
	'bac_Nechronica'=>array('ネクロニカ','','g6'),
	'bac_NeverCloud'=>array('ネバークラウドTRPG','','g354'),
	'bac_HarnMaster'=>array('ハーンマスター','','g230'),
	'bac_Pathfinder'=>array('Pathfinder','','g70'),
	'bac_BadLife'=>array('犯罪活劇RPGバッドライフ','','g335'),
	'bac_HatsuneMiku'=>array('初音ミクTRPG','','g255'),
	'bac_BattleTech'=>array('バトルテック','','g69'),
	'bac_ParasiteBlood'=>array('パラサイトブラッド','','g54'),
	'bac_Paranoia'=>array('パラノイア','','g11'),
	'bac_ParanoiaRebooted'=>array('パラノイア リブーテッド','','g359'),
	'bac_BarnaKronika'=>array('バルナ・クロニカ','','g49'),
	'bac_PulpCthulhu'=>array('パルプ・クトゥルフ','','g362'),
	'bac_Raisondetre'=>array('叛逆レゾンデートル','','g363'),
	'bac_HuntersMoon'=>array('ハンターズ・ムーン','','g61'),
	'bac_Peekaboo'=>array('ピーカーブー','','g40'),
	'bac_BeastBindTrinity'=>array('ビーストバインド トリニティ','','g59'),
	'bac_BBN'=>array('BBNTRPG','','g336'),
	'bac_Hieizan'=>array('比叡山炎上','','g349'),
	'bac_BeginningIdol'=>array('ビギニングアイドル','','g56'),
	'bac_PhantasmAdventure'=>array('ファンタズムアドベンチャー','','g151'),
	'bac_Fiasco'=>array('Fiasco','','g251'),
	'bac_FilledWith'=>array('フィルトウィズ','','g343'),
	'bac_FutariSousa'=>array('フタリソウサ','','g285'),
	'bac_BlindMythos'=>array('ブラインド・ミトス','','g266'),
	'bac_BloodCrusade'=>array('ブラッド・クルセイド','','g60'),
	'bac_BloodMoon'=>array('ブラッドムーン','','g39'),
	'bac_FullMetalPanic'=>array('フルメタル・パニック!RPG','','g58'),
	'bac_BladeOfArcana'=>array('ブレイド・オブ・アルカナ','','g23'),
	'bac_Strave'=>array('碧空のストレイヴ','','g372'),
	'bac_Pendragon'=>array('ペンドラゴン','','g360'),
	'bac_HouraiGakuen'=>array('蓬莱学園の冒険','','g185'),
	'bac_MagicaLogia'=>array('マギカロギア','','g18'),
	'bac_InfiniteFantasia'=>array('無限のファンタジア','','g95'),
	'bac_MeikyuKingdom'=>array('CPDT迷宮キングダム','','g351'),
	'bac_MeikyuKingdomBasic'=>array('迷宮キングダム','','g50'),
	'bac_MeikyuDays'=>array('迷宮デイズ','','g245'),
	'bac_MetallicGuadian'=>array('メタリックガーディアン','','g34'),
	'bac_MetalHead'=>array('メタルヘッド','','g31'),
	'bac_MetalHeadExtream'=>array('メタルヘッドエクストリーム','','g352'),
	'bac_MonotoneMusium'=>array('モノトーンミュージアムRPG','','g7'),
	'bac_YankeeYogSothoth'=>array('ヤンキー＆ヨグ＝ソトース','','g253'),
	'bac_GoldenSkyStories'=>array('ゆうやけこやけ','','g17'),
	'bac_Ryutama'=>array('りゅうたま','','g46'),
	'bac_RyuTuber'=>array('リューチューバーとちいさな奇跡','','g367'),
	'bac_RuinBreakers'=>array('ルーインブレイカーズ','','g328'),
	'bac_RuneQuest'=>array('ルーンクエスト','','g163'),
	'bac_RecordOfSteam'=>array('Record of Steam','','g364'),
	'bac_RecordOfLodossWar'=>array('ロードス島戦記RPG','','g66'),
	'bac_RoleMaster'=>array('ロールマスター','','g236'),
	'bac_LogHorizon'=>array('ログ・ホライズン','','g13'),
	'bac_RokumonSekai2'=>array('六門世界2nd','','g109'),
	'bac_LostRecord'=>array('ロストレコード','','g296'),
	'bac_LostRoyal'=>array('ロストロイヤル','','g118'),
	'bac_WaresBlade'=>array('ワースブレイド','','g165'),
	'bac_WARPS'=>array('ワープス','','g205'),
	'bac_WorldOfDarkness'=>array('ワールド・オブ・ダークネス','','g125'),
);