<?php
// delete_act.php
session_start();
require_once __DIR__ . '/funcs.php';
sschk();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirect('index.php');
}

// POSTデータ取得
$lpw = $_POST['lpw'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';

// DB接続
$pdo = db_conn();

try{
    $verify_sql = "SELECT lpw FROM user_info WHERE id = :user_id";
    $verify_stmt = $pdo->prepare($verify_sql);
    $verify_stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $verify_stmt->execute();
    $verify_password = $verify_stmt->fetchColumn();
    // パスワードが一致しない場合
    if (!password_verify($lpw, $verify_password)) {
        $_SESSION['error'] = "パスワードが一致しません";
        redirect('delete_confirm.php');
}

//トランザクション開始
$pdo->beginTransaction();

//※実装出来たら！
//フレンド関係の削除
// $delete_friends_sql = "DELETE FROM friendships WHERE user_id = :user_id OR friend_id = :user_id";
// $friends_stmt = $pdo->prepare($delete_friends_sql);
// $friends_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
// $friends_stmt->execute();

 // 週間称号の削除
 $delete_achievements_sql = "DELETE FROM weekly_achievements WHERE user_id = :user_id";
 $achievements_stmt = $pdo->prepare($delete_achievements_sql);
 $achievements_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
 $achievements_stmt->execute();

 // 目標設定の削除
 $delete_goals_sql = "DELETE FROM user_goals WHERE user_id = :user_id";
 $goals_stmt = $pdo->prepare($delete_goals_sql);
 $goals_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
 $goals_stmt->execute();

 //日々の記録の削除
 $delete_records_sql = "DELETE FROM daily_records WHERE user_id = :user_id";
 $records_stmt = $pdo->prepare($delete_records_sql);
 $records_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
 $records_stmt->execute();

 // 最後にユーザー情報の削除
 $delete_user_sql = "DELETE FROM user_info WHERE id = :user_id";
 $user_stmt = $pdo->prepare($delete_user_sql);
 $user_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
 $user_stmt->execute();

 // トランザクションのコミット
 $pdo->commit();

 $message = "退会手続き完了！<br>また使ってね";

// セッションを破棄
$_SESSION = array();
if(isset($_COOKIE[session_name()])){
    setcookie(session_name(), '', time()-42000, "/");
}
session_destroy();

// 退会処理後の表示
session_start();
$_SESSION['success'] = $message;
    redirect("index.php");  // ログイン画面へ遷移
} catch(Exception $e){
    $pdo->rollBack();
    $_SESSION["error"] = "退会処理中にエラーが<br>発生しました";
    redirect('delete_confirm.php');
}