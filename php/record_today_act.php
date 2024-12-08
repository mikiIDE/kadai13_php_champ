<?php
// record_today_act.php
session_start();
require_once __DIR__ . '/funcs.php';
sschk();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirect('index.php');
}

// POSTデータ取得
$study_hours = $_POST['study_hours'];
$sleep_start = $_POST['sleep_start'];
$sleep_end = $_POST['sleep_end'];
$sleep_quality = $_POST['sleep_quality'];
$has_protein = $_POST['has_protein'];
$has_carbo = $_POST['has_carbo'];
$has_vegetable = $_POST['has_vegetable'];
$meal_quality = $_POST['meal_quality'];
$exercise_over_30min = $_POST['exercise_over_30min'];
$step_count = isset($_POST['step_count']) ? $_POST['step_count'] : 0;

// DB接続
$pdo = db_conn();

// スコア計算関数
function calculateScores($data) {
    // 睡眠スコアの計算
    $sleep_score = 0;
    // 睡眠時間の計算
    $sleep_start_obj = new DateTime($data['sleep_start']);
    $sleep_end_obj = new DateTime($data['sleep_end']);
    $sleep_duration = $sleep_end_obj->diff($sleep_start_obj)->h;
    
    // 理想の睡眠時間（6-8時間）との比較
    if ($sleep_duration >= 6 && $sleep_duration <= 8) {
        $sleep_score += 3;
    }
    // 睡眠の質を加算
    $sleep_score += intval($data['sleep_quality']);

    $study_score = 0;
    if ($data['study_hours'] >= 6) {
        $study_score = 5;
    } else {
        // 6時間未満の場合は比例配分（1時間につき0.83点）
        $study_score = round(($data['study_hours'] / 6) * 5);
    }
    
    // 食事スコアの計算（最大10点）
    $meal_score = 0;
    if ($data['has_protein']) $meal_score += 2;
    if ($data['has_carbo']) $meal_score += 2;
    if ($data['has_vegetable']) $meal_score += 2;
    $meal_score += intval($data['meal_quality']);

    // 運動スコアの計算（最大5点）
    $exercise_score = 0;
    if ($data['exercise_over_30min']) {
        $exercise_score = 5;
    } else {
        // 歩数に応じてスコア付け
        if ($data['step_count'] >= 8000) $exercise_score = 4;
        else if ($data['step_count'] >= 5000) $exercise_score = 3;
        else if ($data['step_count'] >= 3000) $exercise_score = 2;
        else $exercise_score = 1;
    }

    return [
        'sleep_score' => $sleep_score,
        'meal_score' => $meal_score,
        'exercise_score' => $exercise_score,
        'study_score' => $study_score
    ];
}
try{
    // スコアの計算
    $scores = calculateScores($_POST);

    // トランザクション開始
    $pdo->beginTransaction();

    // 日付
    $record_date = $_POST['record_date'];  // フォームから送信された日付を使用

    // 既存のレコードを確認
    $check_sql = "SELECT id FROM daily_records WHERE user_id = :user_id AND record_date = :record_date LIMIT 1";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $check_stmt->bindValue(':record_date', $record_date);
    $check_stmt->execute();
    $existing_record = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_record) {
        // UPDATEの場合
        $sql = "UPDATE daily_records SET 
            sleep_start = :sleep_start,
            sleep_end = :sleep_end,
            sleep_quality = :sleep_quality,
            has_meat = :has_protein,
            has_fish = :has_protein,
            has_vegetable = :has_vegetable,
            has_carbo = :has_carbo,
            exercise_over_30min = :exercise_over_30min,
            step_count = :step_count,
            study_hours = :study_hours,
            sleep_score = :sleep_score,
            meal_score = :meal_score,
            exercise_score = :exercise_score,
            study_score = :study_score,
            updated_at = CURRENT_TIMESTAMP
            WHERE user_id = :user_id AND record_date = :record_date";
    } else {
        // INSERTの場合
    $sql = "INSERT INTO daily_records (
        user_id, record_date, sleep_start, sleep_end, sleep_quality,
        has_meat, has_fish, has_vegetable, has_carbo,
        exercise_over_30min, step_count, study_hours,
        sleep_score, meal_score, exercise_score,study_score
    ) VALUES (
        :user_id, :record_date, :sleep_start, :sleep_end, :sleep_quality,
        :has_protein, :has_protein, :has_vegetable, :has_carbo,
        :exercise_over_30min, :step_count, :study_hours,
        :sleep_score, :meal_score, :exercise_score, :study_score
    )";
    }
    
    $stmt = $pdo->prepare($sql);
    
    // パラメータのバインド
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':record_date', $record_date);
    $stmt->bindValue(':sleep_start', $sleep_start);
    $stmt->bindValue(':sleep_end', $sleep_end);
    $stmt->bindValue(':sleep_quality', $sleep_quality, PDO::PARAM_INT);
    $stmt->bindValue(':has_protein', $has_protein, PDO::PARAM_BOOL);
    $stmt->bindValue(':has_carbo', $has_carbo, PDO::PARAM_BOOL);
    $stmt->bindValue(':has_vegetable', $has_vegetable, PDO::PARAM_BOOL);
    $stmt->bindValue(':exercise_over_30min', $exercise_over_30min, PDO::PARAM_BOOL);
    $stmt->bindValue(':step_count', $step_count, PDO::PARAM_INT);
    $stmt->bindValue(':study_hours', $study_hours);
    $stmt->bindValue(':study_score', $scores['study_score'], PDO::PARAM_INT);
    $stmt->bindValue(':sleep_score', $scores['sleep_score'], PDO::PARAM_INT);
    $stmt->bindValue(':meal_score', $scores['meal_score'], PDO::PARAM_INT);
    $stmt->bindValue(':exercise_score', $scores['exercise_score'], PDO::PARAM_INT);

    // 実行
    $status = $stmt->execute();

    if ($status) {   
    // 火曜日始まりの週の開始日を取得
    $current_date = new DateTime($record_date);
    $days_to_subtract = (($current_date->format('w') - 2 + 7) % 7);
    $week_start = clone $current_date;
    $week_start->modify("-{$days_to_subtract} days");

    // デバッグ用
    error_log("Week Start Date: " . $week_start->format('Y-m-d'));
    
    // 称号の計算と保存
    require_once 'achievements.php';
    $result = calculateWeeklyAchievements($pdo, $_SESSION['user_id'], $week_start->format('Y-m-d'));
    
    // デバッグ用
    error_log("Achievement Calculation Result: " . ($result ? 'Success' : 'Failed'));
    
    $pdo->commit();
    $_SESSION['success_message'] = "記録を保存しました！";
    redirect('main.php');
} else {
    throw new Exception('データの保存に失敗しました。');
}
}catch (Exception $e) {
$pdo->rollBack();
$_SESSION['error_message'] = $e->getMessage();
redirect('record_today.php');
}