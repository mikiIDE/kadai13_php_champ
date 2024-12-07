<?php
// main.php
require_once __DIR__ . '/funcs.php'; //関数ファイル読み込み
require_once __DIR__ . '/../inc/header.php'; //session_start();は含まれているので注意

// セッションチェックとユーザーID取得
if (!isset($_SESSION['chk_ssid']) || !isset($_SESSION['user_id'])) {
    redirect('index.php');
}
$user_id = $_SESSION['user_id'];

// デバッグ用
//var_dump($_SESSION);  // セッションの中身を確認

// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

// 前月・次月リンクが押された場合は、GETパラメーターから年月を取得
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // 今月の年月を表示
    $ym = date('Y-m');
}

// タイムスタンプを作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// 今日の日付 フォーマット　例）2024-12-01
$today = date('Y-m-d');

// カレンダーのタイトルを作成　例）2024年12月
$html_title = date('Y年n月', $timestamp);

// 前月・次月の年月を取得
$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) - 1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) + 1, 1, date('Y', $timestamp)));

// 該当月の日数を取得
$day_count = date('t', $timestamp);

// １日が何曜日か　0:日 1:月 2:火 ... 6:土
$youbi = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));

// カレンダー作成の準備
$weeks = [];
$week = '';

// 第１週目：空のセルを追加
// 例）１日が火曜日だった場合、日・月曜日の２つ分の空セルを追加する
$week .= str_repeat('<td></td>', $youbi);

// DB接続準備
$pdo = db_conn();

// 月初めと月末の日付を取得
$start_date = $ym . '-01';
$end_date = $ym . '-' . $day_count;

for ($day = 1; $day <= $day_count; $day++, $youbi++) {
    // 例2024-12-1
    $date = $ym . '-' . sprintf("%02d", $day);
    $todo_count = isset($todo_counts[$date]) ? $todo_counts[$date][0]["todo_count"] : 0;

    // tdタグの開始（data-date属性を含める）
    if ($today == $date) {
        $week .= '<td class="today" data-date="' . $date . '">';
    } else {
        $week .= '<td data-date="' . $date . '">';
    }

    // date-cellの中身
    $week .= '<div class="date-cell">';
    $week .= '<div class="date-number">' . $day . '</div>';

    // if ($todo_count > 0) {
    //     $week .= '<div class="todo-badge">';
    //     $week .= '<span class="todo-icon">📝</span>' . $todo_count;
    //     $week .= '</div>';
    // }
    // $week .= '</div>';
    // $week .= '</td>';

    // 週終わり、または、月終わりの場合
    if ($youbi % 7 == 6 || $day == $day_count) {
        if ($day == $day_count) {
            // 月の最終日の場合、空セルを追加
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
        }

        // weeks配列にtrと$weekを追加する
        $weeks[] = '<tr>' . $week . '</tr>';

        // weekをリセット
        $week = '';
    }
}
// ユーザーの目標時間を取得するSQL
$sql = "SELECT daily_study_hours, daily_sleep_hours FROM user_goals WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $stmt->execute();
    $goals = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
}

?>
<main>
    <?php if ($is_logged_in) : ?>
        <div class="greeting"><?= h($_SESSION["name"]) ?>さん、お疲れ様です！</div>
    <?php endif; ?>
    <div class="user-prof">
    <img class="user_icon" src="../img/default-icon.png" alt="ユーザーアイコン">
    <button class="prof-setting"><a href="prof_setting.php">プロフィールを編集する</a></button>
    </div>
    <!-- カレンダーの表示 -->
    <div class="calender-container">
        <h4 class="mb-5"><a href="?ym=<?= $prev ?>">&lt;</a><span class="mx-3"><?= $html_title ?></span><a href="?ym=<?= $next ?>">&gt;</a></h4>
        <table class="table table-bordered">
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
            <?php
            foreach ($weeks as $week) {
                echo $week;
            }
            ?>
        </table>
    </div>
    <div class="target">
        <div class="target-sleep">睡眠：<?php echo isset($goals['daily_sleep_hours']) ? h($goals['daily_sleep_hours']) . "時間" : '未設定'; ?></div>
        <div class="target-learn">学習：<?php echo isset($goals['daily_study_hours']) ? h($goals['daily_study_hours']) . "時間" : '未設定'; ?></div>
        <button class="target-btn">目標を登録/編集する</button>
        <!-- ポップアップ -->
        <div id="popup-wrapper">
            <div id="popup-inside">
                <div id="close">x</div>
                <div id="form-content" class="popup-content">
                    <div id="set_message">
                        日々の学習と睡眠の<br>目標時間を設定しよう！
                        <form action="target_save_act.php" method="post">
                            <div class="form-group">
                                <label for="sleep_hour">睡眠時間：</label>
                                <input type="number" id="sleep_hour" name="sleep_hour" step="0.1" min="0" max="24" required>
                            </div>
                            <div class="form-group">
                                <label for="study_hour">学習時間：</label>
                                <input type="number" id="study_hour" name="study_hour" step="0.1" min="0" max="24" required>
                            </div>
                            <button type="submit" class="set_target">設定</button>
                        </form>
                    </div>
                </div>
                <!-- 処理の可否についてのメッセージ -->
                <div id="message-content" class="popup-content" style="display:none;">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="success" data-show-popup="true"> <!-- data属性を追加 -->
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        <?php endif; ?>
                        </div>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="error" data-show-popup="true">
                                <?php
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <button class="help"><a href="help.php">？ <span class="help_s">ヘルプ</span></a></button></div>
</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>
<script src="../js/popup.js"></script>
</body>

</html>