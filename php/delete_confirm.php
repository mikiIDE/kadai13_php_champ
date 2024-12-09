<?php
// delete_confirm.php
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
    <h4 class="prof-setting-title">退会手続き</h4>
    <p><?= h($_SESSION["name"]) ?>さん</p>
    <p>本当に退会しますか？</p>
    <p class="alert">※「退会」を押すと取り消しできません※</p>
    <form action="delete_act.php" method="post">
        <div class="form-group">
            <label for="lpw">パスワード確認：</label>
            <input type="password" id="lpw" name="lpw" placeholder="設定済みのパスワード" required>
        </div>
        <div class="buttons">
            <button type="submit" class="delete-btn">退会</button>
            <button class="back"><a href="main.php">戻る</a></button>
        </div>
        <!-- ポップアップ （退会処理時にエラーが起きた場合表示）-->
        <div id="popup-wrapper">
            <div id="popup-inside">
                <div id="close">x</div>
                <div id="message">
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
</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>
</body>

</html>