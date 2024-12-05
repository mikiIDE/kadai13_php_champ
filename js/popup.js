// ポップアップ用
// HTMLの読み込み完了後に動くよう指定する
document.addEventListener('DOMContentLoaded', () => {
    const popupWrapper = document.getElementById('popup-wrapper');
    const close = document.getElementById('close');
    const successMessage = document.querySelector('[data-show-popup="true"]');

    // ページ読み込み時のポップアップ表示処理
    if (successMessage) {
        popupWrapper.style.display = "block";
    }
    // クリックイベントの設定
    popupWrapper.addEventListener('click', e => {
        if (e.target.id === popupWrapper.id || e.target.id === close.id) {
            popupWrapper.style.display = 'none';
        }
    });
});