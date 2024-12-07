<?php
// record_today.php
require_once __DIR__ . '/funcs.php'; //関数ファイル読み込み
require_once __DIR__ . '/../inc/header.php'; //session_start();は含まれているので注意

// セッションチェックとユーザーID取得
if (!isset($_SESSION['chk_ssid']) || !isset($_SESSION['user_id'])) {
    redirect('index.php');
}
// 日付の取得（GETパラメータまたは今日の日付）
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// DB接続
$pdo = db_conn();

// 既存のデータを取得
$existing_data = null;
try {
    $sql = "SELECT * FROM daily_records 
            WHERE user_id = :user_id 
            AND record_date = :record_date 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':record_date', $date);
    $stmt->execute();
    $existing_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // エラーハンドリング
}

?>

<main>
    <form method="POST" action="record_today_act.php">
        <div class="date-header">
            <?php echo date('Y年n月j日', strtotime($date)); ?>の記録
        </div>
        <input type="hidden" name="record_date" value="<?php echo $date; ?>">

        <div class="study-section">
            <h3>学習記録</h3>
            <input type="number" name="study_hours" step="0.5" min="0" max="24" required
                value="<?php echo $existing_data ? h($existing_data['study_hours']) : ''; ?>"> 時間
        </div>

        <div class="sleep-section">
            <h3>睡眠記録</h3>
            <input type="time" name="sleep_start" required
                value="<?php echo $existing_data ? h($existing_data['sleep_start']) : ''; ?>">
            <input type="time" name="sleep_end" required
                value="<?php echo $existing_data ? h($existing_data['sleep_end']) : ''; ?>">
            <div class="select-group">
                <label class="select-label">睡眠の自己評価：</label>
                <select name="sleep_quality">
                    <option value="">選択してください</option>
                    <?php
                    $qualities = [
                        5 => 'とても良い',
                        4 => '良い',
                        3 => '普通',
                        2 => '悪い',
                        1 => 'とても悪い'
                    ];
                    foreach ($qualities as $value => $label) {
                        $selected = ($existing_data && $existing_data['sleep_quality'] == $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$label</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="meals-section">
            <h3>食事記録</h3>
            <div class="form-group">
                <label>お肉か魚を食べましたか？</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="has_protein" value="1" required
                            <?php echo ($existing_data && $existing_data['has_meat']) ? 'checked' : ''; ?>> はい
                    </label>
                    <label>
                        <input type="radio" name="has_protein" value="0"
                            <?php echo ($existing_data && !$existing_data['has_meat']) ? 'checked' : ''; ?>> いいえ
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label>炭水化物は食べましたか？</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="has_carbo" value="1" required
                            <?php echo ($existing_data && $existing_data['has_meat']) ? 'checked' : ''; ?>> はい
                    </label>
                    <label>
                        <input type="radio" name="has_carbo" value="0"
                            <?php echo ($existing_data && !$existing_data['has_meat']) ? 'checked' : ''; ?>> いいえ
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label>野菜は食べましたか？</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="has_vegetable" value="1" required
                            <?php echo ($existing_data && $existing_data['has_meat']) ? 'checked' : ''; ?>> はい
                    </label>
                    <label>
                        <input type="radio" name="has_vegetable" value="0"
                            <?php echo ($existing_data && !$existing_data['has_meat']) ? 'checked' : ''; ?>> いいえ
                    </label>
                </div>
            </div>
            <div class="select-group">
                <label class="select-label">食事の自己評価：</label>
                <select name="meal_quality">
                    <option value="">選択してください</option>
                    <?php
                    $qualities = [
                        5 => 'とても良い',
                        4 => '良い',
                        3 => '普通',
                        2 => '悪い',
                        1 => 'とても悪い'
                    ];
                    foreach ($qualities as $value => $label) {
                        $selected = ($existing_data && $existing_data['sleep_quality'] == $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$label</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="fitness-section">
                <h3>運動記録</h3>
                <div class="form-group">
                    <label>30分以上の運動をしましたか？</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="exercise_over_30min" value="1" required
                                <?php echo ($existing_data && $existing_data['exercise_over_30min']) ? 'checked' : ''; ?>
                                onclick="toggleStepCount(false)"> はい
                        </label>
                        <label>
                            <input type="radio" name="exercise_over_30min" value="0"
                                <?php echo ($existing_data && !$existing_data['exercise_over_30min']) ? 'checked' : ''; ?>
                                onclick="toggleStepCount(true)"> いいえ
                        </label>
                    </div>
                </div>
                <div id="step-count-group" class="form-group step-input"
                    style="display: <?php echo ($existing_data && !$existing_data['exercise_over_30min']) ? 'block' : 'none'; ?>">
                    <label>歩数：</label>
                    <input type="number" name="step_count" min="0" max="100000"
                        value="<?php echo $existing_data ? h($existing_data['step_count']) : ''; ?>"> 歩
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