// calendar.js
document.addEventListener('DOMContentLoaded', function() {
    // カレンダーの日付セルにクリックイベントを追加
    const dateCells = document.querySelectorAll('td[data-date]');
    dateCells.forEach(cell => {
        cell.addEventListener('click', function() {
            const date = this.getAttribute('data-date');
            window.location.href = `record_today.php?date=${date}`;
        });

        // ホバー時のスタイル
        cell.style.cursor = 'pointer';
    });
});