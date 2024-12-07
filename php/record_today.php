<?php
// prof_setting.php
require_once __DIR__ . '/funcs.php'; //関数ファイル読み込み
require_once __DIR__ . '/../inc/header.php'; //session_start();は含まれているので注意

// セッションチェックとユーザーID取得
if (!isset($_SESSION['chk_ssid']) || !isset($_SESSION['user_id'])) {
    redirect('index.php');
}
?>

<main>
    <form method="POST" action="record_today_act.php">
        <div class="form-section study-section">
            <h3>学習記録</h3>
            <div class="form-group">
                <label>学習時間：</label>
                <input type="number" name="study_hours" step="0.5" min="0" max="24" required> 時間
            </div>
        </div>

        <div class="form-section sleep-section">
            <h3>睡眠記録</h3>
            <div class="form-group">
                <label>就寝時刻：</label>
                <input type="time" name="sleep_start" required>
            </div>
            <div class="form-group">
                <label>起床時刻：</label>
                <input type="time" name="sleep_end" required>
            </div>
            <div class="form-group">
                <label>睡眠の質：</label>
                <select name="sleep_quality" class="quality-select">
                    <option value="">選択してください</option>
                    <option value="5">とても良い</option>
                    <option value="4">良い</option>
                    <option value="3">普通</option>
                    <option value="2">悪い</option>
                    <option value="1">とても悪い</option>
                </select>
            </div>
        </div>

        <div class="form-section meals-section">
            <h3>食事記録</h3>
            <div class="form-group">
                <label>お肉か魚を食べましたか？</label>
                <div class="radio-group">
                    <label><input type="radio" name="has_protein" value="1" required> はい</label>
                    <label><input type="radio" name="has_protein" value="0"> いいえ</label>
                </div>
            </div>
            <div class="form-group">
                <label>炭水化物を食べましたか？</label>
                <div class="radio-group">
                    <label><input type="radio" name="has_carbo" value="1" required> はい</label>
                    <label><input type="radio" name="has_carbo" value="0"> いいえ</label>
                </div>
            </div>
            <div class="form-group">
                <label>野菜を食べましたか？</label>
                <div class="radio-group">
                    <label><input type="radio" name="has_vegetable" value="1" required> はい</label>
                    <label><input type="radio" name="has_vegetable" value="0"> いいえ</label>
                </div>
            </div>
            <div class="form-group">
                <label>食事の質：</label>
                <select name="meal_quality" class="quality-select">
                    <option value="">選択してください</option>
                    <option value="5">とても良い</option>
                    <option value="4">良い</option>
                    <option value="3">普通</option>
                    <option value="2">悪い</option>
                    <option value="1">とても悪い</option>
                </select>
            </div>
        </div>

        <div class="form-section fitness-section">
            <h3>運動記録</h3>
            <div class="form-group">
                <label>30分以上の運動をしましたか？</label>
                <div class="radio-group">
                    <label><input type="radio" name="exercise_over_30min" value="1" required onclick="toggleStepCount(false)"> はい</label>
                    <label><input type="radio" name="exercise_over_30min" value="0" onclick="toggleStepCount(true)"> いいえ</label>
                </div>
            </div>
            <div id="step-count-group" class="form-group step-input">
                <label>歩数：</label>
                <input type="number" name="step_count" min="0" max="100000"> 歩
            </div>
        </div>

        <button type="submit" class="save_record">記録を保存</button>
    </form>

</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>
<script src="../js/record.js"></script>
</body>
</html>