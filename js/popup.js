// popup.js
// ポップアップ用
// HTMLの読み込み完了後に動くよう指定する
document.addEventListener('DOMContentLoaded', () => {
    const popupWrapper = document.getElementById('popup-wrapper');
    const close = document.getElementById('close');
    const targetBtn = document.querySelector('.target-btn'); //main.phpの睡眠学習目標時間用
    const formContent = document.getElementById('form-content');
    const messageContent = document.getElementById('message-content');
    const targetForm = document.getElementById('target-form');

    // セッションメッセージがある場合の処理
    const successMessage = document.querySelector('[data-show-popup="true"]');
    if (successMessage) {
        popupWrapper.style.display = "block";
        formContent.style.display = "none";
        messageContent.style.display = "block";

        // 3秒後にポップアップを自動で閉じる
        setTimeout(() => {
            popupWrapper.style.display = 'none';
            // メッセージを消去してフォームを表示状態に戻す
            messageContent.style.display = "none";
            formContent.style.display = "block";
        }, 3000);
    }
    // 目標設定ボタンのクリックイベント
    targetBtn.addEventListener('click', () => {
        popupWrapper.style.display = "block";
        formContent.style.display = "block";
        messageContent.style.display = "none";
    });

    // フォーム送信時の処理
    targetForm.addEventListener('submit', () => {
        // フォームを非表示にしてメッセージ表示準備
        formContent.style.display = "none";
        messageContent.style.display = "block";
    });

    // ポップアップを閉じる処理
    popupWrapper.addEventListener('click', e => {
        if (e.target.id === popupWrapper.id || e.target.id === close.id) {
            popupWrapper.style.display = 'none';
            messageContent.style.display = "none";
            formContent.style.display = "block";
        }
    });
});