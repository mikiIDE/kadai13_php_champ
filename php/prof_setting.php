<?php
// prof_setting.php
require_once __DIR__ . '/funcs.php'; //関数ファイル読み込み
require_once __DIR__ . '/../inc/header.php'; //session_start();は含まれているので注意

// セッションチェックとユーザーID取得
if (!isset($_SESSION['chk_ssid']) || !isset($_SESSION['user_id'])) {
    redirect('index.php');
}
$user_id = $_SESSION['user_id'];

// DB接続
$pdo = db_conn();

// データの確認
$check_sql = "SELECT * FROM user_info WHERE id = :id";
$check_stmt = $pdo->prepare($check_sql);
$check_stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
$check_stmt->execute();
$user_info = $check_stmt->fetch(PDO::FETCH_ASSOC); //ユーザー情報の取得
?>
<main>
    <h4 class="prof-setting-title">プロフィール編集</h4>
    <form action="prof_setting_act.php" method="post" enctype="multipart/form-data">
    <div class="profile-image-section">
            <img src="../img/<?= h($user_info['profile_image'] ?? 'default-icon.png') ?>" 
                 alt="プロフィール画像" 
                 class="current-profile-image">
            <div class="image-upload">
                <label for="profile_image">プロフィール画像を変更</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>
        </div>    
    <div class="form-group">
            <label for="name">お　名　前：</label>
            <input type="text" id="name" name="name" value="<?= h($user_info['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="lid">ユーザーID：</label>
            <input type="text" id="lid" name="lid" value="<?= h($user_info['lid']) ?>" required>
        </div>
        <div class="form-group">
            <label for="lpw">パスワード確認：</label>
            <input type="password" id="lpw" name="lpw" placeholder="設定済みのパスワードを入力" required>
        </div>
        <button type="submit" class="edit_prof">編集完了</button>
    </form>
    </div>

</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>
<script src="../js/popup.js"></script>
</body>

</html>