/*
 * OnsenRoom ミニ draggable/resizable 実装 (vanilla JS)
 *
 * jQuery UI の draggable/resizable を置換する軽量実装。
 * 使っているオプションのサブセットのみサポート:
 *
 *   draggable:
 *     handle   : CSS セレクタ (この要素をつかんでドラッグ)
 *     cancel   : CSS セレクタ (この要素上ではドラッグ開始しない)
 *     containment: '#id' or 'parent' (親要素内に閉じ込める)
 *
 *   resizable:
 *     minHeight / minWidth : 最小サイズ (px)
 *     alsoResize : CSS セレクタ (同時にリサイズする要素)
 *
 * 2026-10-31 制作
 */

/* ========================================================================
 * makeDraggable
 * ======================================================================== */

/**
 * 要素をドラッグ移動可能にする
 * @param {Element|string} el 対象要素 or '#id'
 * @param {Object} opt
 *   - handle: CSSセレクタ, この要素部分でのみドラッグ可
 *   - cancel: CSSセレクタ, この要素部分ではドラッグ不可
 *   - containment: '#id' or 'parent', 移動範囲を制限
 */
function makeDraggable(el, opt) {
    const target = typeof el === 'string' ? document.querySelector(el) : el;
    if (!target) return;
    opt = opt || {};
    const handleSelector = opt.handle || null;
    const cancelSelector = opt.cancel || null;
    const containment = opt.containment || null;

    let dragging = false;
    let startX = 0, startY = 0;
    let origLeft = 0, origTop = 0;

    const handleEl = handleSelector ? target.querySelector(handleSelector) : target;
    if (!handleEl) return;

    handleEl.style.cursor = 'move';
    handleEl.style.userSelect = 'none';

    function onPointerDown(e) {
        // cancel セレクタに該当する要素クリックなら無視
        if (cancelSelector) {
            const cancelEls = target.querySelectorAll(cancelSelector);
            for (const c of cancelEls) {
                if (c === e.target || c.contains(e.target)) return;
            }
        }
        if (e.button !== undefined && e.button !== 0) return; // 左クリックのみ
        dragging = true;
        startX = e.clientX;
        startY = e.clientY;
        // 親要素基準の相対座標 (offsetLeft/Top は position != static な親の内側座標)
        // 既存の CSS で .popup_window は position:absolute, .card も position:absolute
        // になっているため、style を書き換えず現在の offsetLeft/Top を基準とする
        origLeft = target.offsetLeft;
        origTop = target.offsetTop;
        if (e.pointerId !== undefined) {
            handleEl.setPointerCapture(e.pointerId);
        }
        e.preventDefault();
    }

    function onPointerMove(e) {
        if (!dragging) return;
        let newLeft = origLeft + (e.clientX - startX);
        let newTop = origTop + (e.clientY - startY);
        // containment 制限 (親要素基準の相対座標で判定)
        if (containment) {
            let container;
            if (containment === 'parent') {
                container = target.offsetParent || target.parentElement;
            } else {
                container = document.querySelector(containment);
            }
            if (container) {
                // target.offsetParent が container と同じ場合は 0 基準でよい
                // 違う場合は container の offsetLeft/Top との差分を考慮
                const maxLeft = container.clientWidth - target.offsetWidth;
                const maxTop = container.clientHeight - target.offsetHeight;
                if (newLeft < 0) newLeft = 0;
                if (newTop < 0) newTop = 0;
                if (newLeft > maxLeft) newLeft = maxLeft;
                if (newTop > maxTop) newTop = maxTop;
            }
        }
        target.style.left = newLeft + 'px';
        target.style.top = newTop + 'px';
    }

    function onPointerUp(e) {
        if (!dragging) return;
        dragging = false;
        if (e.pointerId !== undefined && handleEl.hasPointerCapture && handleEl.hasPointerCapture(e.pointerId)) {
            handleEl.releasePointerCapture(e.pointerId);
        }
        // ドラッグ終了時のコールバック (位置保存等に使う)
        if (typeof opt.onEnd === 'function') opt.onEnd(target);
    }

    handleEl.addEventListener('pointerdown', onPointerDown);
    handleEl.addEventListener('pointermove', onPointerMove);
    handleEl.addEventListener('pointerup', onPointerUp);
    handleEl.addEventListener('pointercancel', onPointerUp);
}

/* ========================================================================
 * makeResizable
 * ======================================================================== */

/**
 * 要素をリサイズ可能にする (右下角ハンドル)
 * @param {Element|string} el
 * @param {Object} opt
 *   - minHeight / minWidth: 最小サイズ (px)
 *   - alsoResize: 同時にリサイズするセレクタ
 */
function makeResizable(el, opt) {
    const target = typeof el === 'string' ? document.querySelector(el) : el;
    if (!target) return;
    opt = opt || {};
    const minHeight = opt.minHeight || 50;
    const minWidth = opt.minWidth || 50;
    const alsoResizeSelector = opt.alsoResize || null;

    // 既に resize ハンドルを持っていたらスキップ
    if (target.querySelector('.or-resize-handle')) return;

    // 右下にリサイズハンドルを追加
    const handle = document.createElement('div');
    handle.className = 'or-resize-handle';
    // CSS で視覚ハンドルを提供するためスタイル は or-resize-handle クラスに集約
    target.appendChild(handle);
    // box-sizing を border-box に揃えることで、style.width に offsetWidth を
    // そのまま入れても見た目サイズが変化しないようにする (resizable 時のマージン変化対策)
    target.style.boxSizing = 'border-box';
    // alsoResize 対象の要素も box-sizing を揃える (padding/border のある内側要素が親からはみ出さないよう)
    if (alsoResizeSelector) {
        const alsoEl = target.querySelector(alsoResizeSelector) || document.querySelector(alsoResizeSelector);
        if (alsoEl) alsoEl.style.boxSizing = 'border-box';
    }

    let resizing = false;
    let startX = 0, startY = 0;
    let origWidth = 0, origHeight = 0;
    // alsoResize 対象の初期サイズを記憶 (差分方式でサイズ変更するため)
    let alsoOrigWidth = 0, alsoOrigHeight = 0;
    let alsoEl = null;

    function onPointerDown(e) {
        if (e.button !== undefined && e.button !== 0) return;
        resizing = true;
        startX = e.clientX;
        startY = e.clientY;
        // offsetWidth/Height は border + padding + content のサイズ (border-box 時は = style.width)
        origWidth = target.offsetWidth;
        origHeight = target.offsetHeight;
        if (alsoResizeSelector) {
            alsoEl = target.querySelector(alsoResizeSelector) || document.querySelector(alsoResizeSelector);
            if (alsoEl) {
                alsoOrigWidth = alsoEl.offsetWidth;
                alsoOrigHeight = alsoEl.offsetHeight;
            }
        }
        if (e.pointerId !== undefined) {
            handle.setPointerCapture(e.pointerId);
        }
        e.preventDefault();
    }

    function onPointerMove(e) {
        if (!resizing) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        let newWidth = origWidth + dx;
        let newHeight = origHeight + dy;
        if (newWidth < minWidth) newWidth = minWidth;
        if (newHeight < minHeight) newHeight = minHeight;
        // 実際の適用量 (最小サイズでクリップされた場合は差分も縮む)
        const actualDx = newWidth - origWidth;
        const actualDy = newHeight - origHeight;
        target.style.width = newWidth + 'px';
        target.style.height = newHeight + 'px';
        if (alsoEl) {
            // 差分方式: alsoResize 対象には、元のサイズに実際の変化分だけ加算
            // (jQuery UI の alsoResize と同じ挙動)
            alsoEl.style.width = (alsoOrigWidth + actualDx) + 'px';
            alsoEl.style.height = (alsoOrigHeight + actualDy) + 'px';
        }
    }

    function onPointerUp(e) {
        if (!resizing) return;
        resizing = false;
        if (e.pointerId !== undefined && handle.hasPointerCapture && handle.hasPointerCapture(e.pointerId)) {
            handle.releasePointerCapture(e.pointerId);
        }
        // リサイズ終了時のコールバック (サイズ保存等に使う)
        if (typeof opt.onEnd === 'function') opt.onEnd(target);
    }

    handle.addEventListener('pointerdown', onPointerDown);
    handle.addEventListener('pointermove', onPointerMove);
    handle.addEventListener('pointerup', onPointerUp);
    handle.addEventListener('pointercancel', onPointerUp);
}
