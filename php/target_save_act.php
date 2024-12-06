<?php
// target_save_act.php
session_start();
require_once __DIR__ . '/funcs.php'; // 関数ファイルを読み込む（includeではなくrequire_once推奨。二重呼び込みやエラーの際の実行を避ける）
sschk(); // セッションチェック
if ($_SERVER['REQUEST_METHOD'] != 'POST') { //直接このページを見に来た場合はリダイレクトする
    redirect("index.php");
}

//1. POSTデータの取得と入力チェック
$study_hours = filter_input(INPUT_POST, 'study_hour', FILTER_VALIDATE_FLOAT);
$sleep_hours = filter_input(INPUT_POST, 'sleep_hour', FILTER_VALIDATE_FLOAT);
// ※下の方法だと値が入っているかのチェックのみで、abcや悪意のあるコードなどでも通る可能性がある
// $study_hours = isset($_POST["study_hours"]) ? $_POST["study_hours"] : '';
// $sleep_hours = isset($_POST["sleep_hours"]) ? $_POST["sleep_hours"] : '';

// 入力チェック
if ($study_hours === false || $sleep_hours === false) {
    exit('ParamError: 半角で数値を入力してください');
}
if ($study_hours < 0 || $study_hours > 24 || $sleep_hours < 0 || $sleep_hours > 24) {
    exit('ParamError: 0から24の間で入力してください');
}

//2. DB接続
$pdo = db_conn();

//3. データ登録SQL作成（目標時間の保存）
$sql = "INSERT INTO user_goals (user_id, daily_study_hours, daily_sleep_hours) 
        VALUES (:user_id, :study_hours, :sleep_hours)
        ON DUPLICATE KEY UPDATE 
        daily_study_hours = :study_hours,
        daily_sleep_hours = :sleep_hours";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':study_hours', $study_hours, PDO::PARAM_STR); //数字だけど小数点が入るのでSTR
$stmt->bindValue(':sleep_hours', $sleep_hours, PDO::PARAM_STR);

// SQL実行時にエラーがある場合STOP
try {
    $status = $stmt->execute();
    if ($status == false) {
        sql_error($stmt);
    }
    $_SESSION['success'] = '目標設定しました！';
} catch (PDOException $e) {
    $_SESSION['error'] = '目標の設定に失敗しました';
    error_log($e->getMessage());
}

// 元のページにリダイレクト
redirect($_SERVER['HTTP_REFERER']);