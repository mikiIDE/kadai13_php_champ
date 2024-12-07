
function toggleStepCount(show) {
    const stepCountGroup = document.getElementById('step-count-group');
    const stepCountInput = stepCountGroup.querySelector('input');
    
    stepCountGroup.style.display = show ? 'block' : 'none';
    stepCountInput.required = show;
    if (!show) {
        stepCountInput.value = '';
    }
}

// ページ読み込み時の初期設定
document.addEventListener('DOMContentLoaded', function() {
    // 運動のラジオボタンの状態を確認して歩数入力欄の表示を設定
    const noExerciseRadio = document.querySelector('input[name="exercise_over_30min"][value="0"]');
    if (noExerciseRadio && noExerciseRadio.checked) {
        toggleStepCount(true);
    }
});