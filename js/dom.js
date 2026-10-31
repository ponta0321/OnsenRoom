/*
 * OnsenRoom DOM ユーティリティ (vanilla JS)
 *
 * jQuery からの段階的脱却のための最小ヘルパー群。
 * jQuery 完全削除後も、既存の view に散在する短い呼び出しをコンパクトに
 * 書けるようにするため残置する。
 *
 * 設計方針:
 *   - jQuery とは互換性を取らない (チェイン等は提供しない)
 *   - 単純な置換性だけを重視
 *   - 返り値は常に Element | NodeList | undefined の 3 種類
 *
 * 2026-10-31 制作
 */

/* ========================================================================
 * DOM セレクタ
 * ======================================================================== */

/**
 * 単一要素取得 (document.querySelector の短縮形)
 * @param {string} selector CSS セレクタ (例: '#id', '.class', 'tag[attr]')
 * @param {Element} [root] 検索開始ノード (省略時は document)
 * @returns {Element|null}
 */
function $el(selector, root) {
    return (root || document).querySelector(selector);
}

/**
 * 複数要素取得 (document.querySelectorAll の短縮形、NodeList を Array に変換)
 * @param {string} selector CSS セレクタ
 * @param {Element} [root] 検索開始ノード
 * @returns {Element[]}
 */
function $all(selector, root) {
    return Array.from((root || document).querySelectorAll(selector));
}

/**
 * id で単一要素取得 (document.getElementById の短縮形、最速)
 * @param {string} id
 * @returns {Element|null}
 */
function $id(id) {
    return document.getElementById(id);
}

/* ========================================================================
 * CSS クラス操作
 * ======================================================================== */

/**
 * クラス追加 (単一・複数対応)
 * @param {Element|Element[]} el 要素または配列
 * @param {...string} classNames
 */
function addClass(el, ...classNames) {
    if (!el) return;
    const els = Array.isArray(el) ? el : [el];
    els.forEach(e => classNames.forEach(c => e.classList.add(c)));
}

/**
 * クラス削除
 */
function removeClass(el, ...classNames) {
    if (!el) return;
    const els = Array.isArray(el) ? el : [el];
    els.forEach(e => classNames.forEach(c => e.classList.remove(c)));
}

/**
 * クラス切替
 */
function toggleClass(el, className, force) {
    if (!el) return;
    const els = Array.isArray(el) ? el : [el];
    els.forEach(e => e.classList.toggle(className, force));
}

/**
 * クラスを持っているか判定 (最初の要素のみ)
 */
function hasClass(el, className) {
    if (!el) return false;
    const e = Array.isArray(el) ? el[0] : el;
    return e ? e.classList.contains(className) : false;
}

/* ========================================================================
 * スタイル操作
 * ======================================================================== */

/**
 * スタイル設定 (jQuery .css() の置換)
 * @param {Element|Element[]} el
 * @param {string|Object} prop プロパティ名 or {prop:value, ...}
 * @param {string} [value]
 */
function setStyle(el, prop, value) {
    if (!el) return;
    const els = Array.isArray(el) ? el : [el];
    if (typeof prop === 'object') {
        els.forEach(e => Object.assign(e.style, prop));
    } else {
        els.forEach(e => { e.style[prop] = value; });
    }
}

/**
 * 表示/非表示 (hidden クラスで制御)
 * CSS 側で .hidden { display: none; } を定義しておくこと
 */
function show(el) { removeClass(el, 'hidden'); }
function hide(el) { addClass(el, 'hidden'); }

/* ========================================================================
 * イベント
 * ======================================================================== */

/**
 * イベントリスナー登録 (単一・複数対応、複数イベント対応)
 * @param {Element|Element[]} el
 * @param {string} eventType 'click', 'click change' のようにスペース区切りも OK
 * @param {Function} handler
 * @param {Object} [options] addEventListener の options
 */
function on(el, eventType, handler, options) {
    if (!el) return;
    const els = Array.isArray(el) ? el : [el];
    const events = eventType.split(/\s+/);
    els.forEach(e => events.forEach(ev => e.addEventListener(ev, handler, options)));
}

/**
 * イベントリスナー解除
 */
function off(el, eventType, handler, options) {
    if (!el) return;
    const els = Array.isArray(el) ? el : [el];
    const events = eventType.split(/\s+/);
    els.forEach(e => events.forEach(ev => e.removeEventListener(ev, handler, options)));
}

/**
 * DOM Ready
 */
function ready(fn) {
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

/* ========================================================================
 * Ajax (fetch ラッパー)
 * ======================================================================== */

/**
 * POST リクエスト (jQuery .ajax 置換用)
 * @param {string} url
 * @param {Object|FormData} data POST データ
 * @param {Object} [opt] { onSuccess, onError, dataType ('json'|'text'|'xml') }
 */
function postData(url, data, opt) {
    opt = opt || {};
    const dataType = opt.dataType || 'json';
    let body;
    if (data instanceof FormData) {
        body = data;
    } else {
        body = new URLSearchParams();
        Object.keys(data || {}).forEach(k => body.append(k, data[k]));
    }
    fetch(url, { method: 'POST', body: body, credentials: 'same-origin' })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            if (dataType === 'json') return response.json();
            if (dataType === 'xml') return response.text().then(txt => new DOMParser().parseFromString(txt, 'text/xml'));
            return response.text();
        })
        .then(result => { if (opt.onSuccess) opt.onSuccess(result); })
        .catch(err => { if (opt.onError) opt.onError(err); });
}

/**
 * GET リクエスト
 */
function getData(url, opt) {
    opt = opt || {};
    const dataType = opt.dataType || 'json';
    fetch(url, { method: 'GET', credentials: 'same-origin' })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            if (dataType === 'json') return response.json();
            if (dataType === 'xml') return response.text().then(txt => new DOMParser().parseFromString(txt, 'text/xml'));
            return response.text();
        })
        .then(result => { if (opt.onSuccess) opt.onSuccess(result); })
        .catch(err => { if (opt.onError) opt.onError(err); });
}

/**
 * jQuery $.post 互換ヘルパ: postForm(url, data, callback)
 *
 * jQuery の postForm(url, data, callback) と同じシグネチャで動作。
 * データは application/x-www-form-urlencoded でエンコード、
 * レスポンスは文字列として callback に渡す (jQuery のデフォルト動作と同じ)。
 *
 * @param {string} url
 * @param {Object} data POST データ (キー: 値)
 * @param {Function} [callback] function(responseText) { ... }
 */
function postForm(url, data, callback) {
    const body = new URLSearchParams();
    if (data) {
        Object.keys(data).forEach(k => {
            const v = data[k];
            if (Array.isArray(v)) {
                v.forEach(x => body.append(k + '[]', x));
            } else {
                body.append(k, v === undefined || v === null ? '' : v);
            }
        });
    }
    fetch(url, {
        method: 'POST',
        body: body,
        credentials: 'same-origin'
    })
    .then(response => response.text())
    .then(text => { if (typeof callback === 'function') callback(text); })
    .catch(err => { /* エラー時は callback 呼ばない (jQuery $.post もエラー時はデフォルトでは success callback 呼ばない) */ });
}

/* ========================================================================
 * モーダルダイアログ (<dialog> ネイティブ要素を使用)
 * ======================================================================== */

/**
 * モーダルダイアログを開く
 * 対象要素は <dialog> 要素であること (jQuery UI dialog からの移行時には
 * <div> → <dialog> への HTML 変更が必要)
 */
function openModal(el) {
    const e = typeof el === 'string' ? $id(el) : el;
    if (e && typeof e.showModal === 'function') e.showModal();
}

/**
 * モーダルダイアログを閉じる
 */
function closeModal(el) {
    const e = typeof el === 'string' ? $id(el) : el;
    if (e && typeof e.close === 'function') e.close();
}

/* ========================================================================
 * ポップアップウィンドウの状態 (位置・サイズ・開閉) を localStorage に保存
 * ======================================================================== */
const PUW_STATE_KEY = 'onsenroom_puw_state';

/**
 * 全ポップアップの状態をロード
 * @returns {Object} {id: {top, left, width, height, visible}, ...}
 */
function loadPuwStateAll() {
    try {
        const raw = localStorage.getItem(PUW_STATE_KEY);
        return raw ? JSON.parse(raw) : {};
    } catch (e) {
        return {};
    }
}

/**
 * 全状態を保存
 */
function savePuwStateAll(obj) {
    try {
        localStorage.setItem(PUW_STATE_KEY, JSON.stringify(obj));
    } catch (e) { /* QuotaExceeded 等は無視 */ }
}

/**
 * 1 ポップアップの状態を保存 (部分更新)
 * @param {string} id 'puw_XX' の XX (例: '00', 'memo_1')
 * @param {Object} patch {top?, left?, width?, height?, visible?}
 */
function savePuwState(id, patch) {
    const all = loadPuwStateAll();
    all[id] = Object.assign({}, all[id] || {}, patch);
    savePuwStateAll(all);
}

/**
 * ポップアップ要素に保存された状態を適用
 * @param {Element} el ポップアップ要素
 * @param {string} id 'puw_XX' の XX
 * @returns {boolean} 状態があって適用されたら true
 */
function applyPuwState(el, id) {
    if (!el) return false;
    const all = loadPuwStateAll();
    const s = all[id];
    if (!s) return false;
    // ウィンドウ内側 (.puw_inbody_memo_X 等) の初期サイズとの差分を記憶してから
    // ウィンドウサイズを変更し、内側も同じ差分で追従させる (alsoResize と同等の挙動)
    const inbody = el.querySelector('[id^="puw_inbody_"]');
    const origInbodyW = inbody ? inbody.offsetWidth : 0;
    const origInbodyH = inbody ? inbody.offsetHeight : 0;
    const origElW = el.offsetWidth;
    const origElH = el.offsetHeight;
    if (typeof s.top === 'number')    el.style.top    = s.top + 'px';
    if (typeof s.left === 'number')   el.style.left   = s.left + 'px';
    if (typeof s.width === 'number')  el.style.width  = s.width + 'px';
    if (typeof s.height === 'number') el.style.height = s.height + 'px';
    if (inbody && (typeof s.width === 'number' || typeof s.height === 'number')) {
        const dw = (typeof s.width === 'number') ? (s.width - origElW) : 0;
        const dh = (typeof s.height === 'number') ? (s.height - origElH) : 0;
        inbody.style.width = (origInbodyW + dw) + 'px';
        inbody.style.height = (origInbodyH + dh) + 'px';
    }
    if (typeof s.visible === 'boolean') {
        el.style.display = s.visible ? 'block' : 'none';
    }
    return true;
}

/**
 * ポップアップから現在の位置・サイズを取得して保存
 */
function savePuwPositionSize(el, id) {
    if (!el) return;
    savePuwState(id, {
        top: parseFloat(el.style.top) || el.offsetTop,
        left: parseFloat(el.style.left) || el.offsetLeft,
        width: el.offsetWidth,
        height: el.offsetHeight
    });
}

/**
 * ポップアップの開閉状態を保存
 */
function savePuwVisibility(el, id) {
    if (!el) return;
    savePuwState(id, { visible: el.style.display !== 'none' });
}
