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
// 方法１：mktimeを使う mktime(hour,minute,second,month,day,year)
$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) - 1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) + 1, 1, date('Y', $timestamp)));

// 方法２：strtotimeを使う
// $prev = date('Y-m', strtotime('-1 month', $timestamp));
// $next = date('Y-m', strtotime('+1 month', $timestamp));

// 該当月の日数を取得
$day_count = date('t', $timestamp);

// １日が何曜日か　0:日 1:月 2:火 ... 6:土
// 方法１：mktimeを使う
$youbi = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));
// 方法２
// $youbi = date('w', $timestamp);

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

    if ($todo_count > 0) {
        $week .= '<div class="todo-badge">';
        $week .= '<span class="todo-icon">📝</span>' . $todo_count;
        $week .= '</div>';
    }
    $week .= '</div>';
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
?>
<main>
    <!-- カレンダーの表示 -->
    <div class="calender-container">
        <h4 class="mb-4"><a href="?ym=<?= $prev ?>">&lt;</a><span class="mx-3"><?= $html_title ?></span><a href="?ym=<?= $next ?>">&gt;</a></h4>
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
</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>
</body>
</html>