// ポップアップ用
// HTMLの読み込み完了後に動くよう指定する
document.addEventListener('DOMContentLoaded', () => {
    const popupWrapper = document.getElementById('popup-wrapper');
    const close = document.getElementById('close');
    const targetBtn = document.querySelector('.target-btn'); //main.phpの睡眠学習目標時間用
    const successMessage = document.querySelector('[data-show-popup="true"]');

    // ページ読み込み時のポップアップ表示処理
    if (successMessage) {
        popupWrapper.style.display = "block";
    }

    // 目標設定ボタンのクリックイベント
    targetBtn.addEventListener('click', () => {
        popupWrapper.style.display = "block";
    });

    // ポップアップを閉じる処理
    popupWrapper.addEventListener('click', e => {
        if (e.target.id === popupWrapper.id || e.target.id === close.id) {
            popupWrapper.style.display = 'none';
        }
    });
});