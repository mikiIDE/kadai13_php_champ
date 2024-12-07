<?php
// prof_setting_act.php
session_start();
require_once __DIR__ . '/funcs.php';
sschk();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirect('index.php');
}

// POSTデータ取得
$name = h($_POST['name'] ?? '');
$lid = h($_POST['lid'] ?? '');
$lpw = $_POST['lpw'] ?? ''; // パスワードは取得するだけ（画面表示しないから）
$user_id = $_SESSION['user_id'];

// 画像アップロード処理
$image_path = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $upload_dir = '../img/';
    $upload_file = $upload_dir . basename($_FILES['profile_image']['name']);
    $image_type = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));
    
    // 画像の形式チェック
    if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif'])) {
        // ファイル名をユニークに
        $new_image_name = uniqid() . '.' . $image_type;
        $upload_file = $upload_dir . $new_image_name;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_file)) {
            $image_path = $new_image_name;
        }
    }
}

// DB接続
$pdo = db_conn();

// パスワード確認
$sql = "SELECT lpw FROM user_info WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

if (!password_verify($lpw, $user['lpw'])) {
    $_SESSION['error'] = 'パスワードが一致しません';
    redirect('prof_setting.php');
}

// 更新SQL作成
$sql = "UPDATE user_info SET name = :name, lid = :lid";
if ($image_path) {
    $sql .= ", profile_image = :image_path";
}
$sql .= " WHERE id = :user_id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
if ($image_path) {
    $stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
}
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $status = $stmt->execute();
    if ($status) {
        $_SESSION['name'] = $name;
        $_SESSION['success'] = 'プロフィールを更新しました';
    } else {
        $_SESSION['error'] = '更新に失敗しました';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = '更新に失敗しました';
    error_log($e->getMessage());
}

redirect('main.php');