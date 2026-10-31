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
        const rect = target.getBoundingClientRect();
        origLeft = rect.left + window.scrollX;
        origTop = rect.top + window.scrollY;
        // 絶対位置化
        target.style.position = 'absolute';
        target.style.left = origLeft + 'px';
        target.style.top = origTop + 'px';
        if (e.pointerId !== undefined) {
            handleEl.setPointerCapture(e.pointerId);
        }
        e.preventDefault();
    }

    function onPointerMove(e) {
        if (!dragging) return;
        let newLeft = origLeft + (e.clientX - startX);
        let newTop = origTop + (e.clientY - startY);
        // containment 制限
        if (containment) {
            let container;
            if (containment === 'parent') {
                container = target.parentElement;
            } else {
                container = document.querySelector(containment);
            }
            if (container) {
                const crect = container.getBoundingClientRect();
                const containerLeft = crect.left + window.scrollX;
                const containerTop = crect.top + window.scrollY;
                const trect = target.getBoundingClientRect();
                const maxLeft = containerLeft + crect.width - trect.width;
                const maxTop = containerTop + crect.height - trect.height;
                if (newLeft < containerLeft) newLeft = containerLeft;
                if (newTop < containerTop) newTop = containerTop;
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
    handle.style.cssText = 'position:absolute;right:0;bottom:0;width:14px;height:14px;cursor:nwse-resize;background:transparent;z-index:9999;';
    // CSS で視覚ハンドルを提供する場合は or-resize-handle クラスにスタイルを当てる
    target.style.position = target.style.position || 'relative';
    target.appendChild(handle);

    let resizing = false;
    let startX = 0, startY = 0;
    let origWidth = 0, origHeight = 0;

    function onPointerDown(e) {
        if (e.button !== undefined && e.button !== 0) return;
        resizing = true;
        startX = e.clientX;
        startY = e.clientY;
        const rect = target.getBoundingClientRect();
        origWidth = rect.width;
        origHeight = rect.height;
        if (e.pointerId !== undefined) {
            handle.setPointerCapture(e.pointerId);
        }
        e.preventDefault();
    }

    function onPointerMove(e) {
        if (!resizing) return;
        let newWidth = origWidth + (e.clientX - startX);
        let newHeight = origHeight + (e.clientY - startY);
        if (newWidth < minWidth) newWidth = minWidth;
        if (newHeight < minHeight) newHeight = minHeight;
        target.style.width = newWidth + 'px';
        target.style.height = newHeight + 'px';
        if (alsoResizeSelector) {
            const also = target.querySelector(alsoResizeSelector) || document.querySelector(alsoResizeSelector);
            if (also) {
                // 対象要素のサイズも同期 (jQuery UI の alsoResize と同等の単純版)
                also.style.width = newWidth + 'px';
                also.style.height = newHeight + 'px';
            }
        }
    }

    function onPointerUp(e) {
        if (!resizing) return;
        resizing = false;
        if (e.pointerId !== undefined && handle.hasPointerCapture && handle.hasPointerCapture(e.pointerId)) {
            handle.releasePointerCapture(e.pointerId);
        }
    }

    handle.addEventListener('pointerdown', onPointerDown);
    handle.addEventListener('pointermove', onPointerMove);
    handle.addEventListener('pointerup', onPointerUp);
    handle.addEventListener('pointercancel', onPointerUp);
}
