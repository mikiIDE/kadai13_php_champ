<?php
// main.php
require_once __DIR__ . '/funcs.php'; //関数ファイル読み込み
require_once __DIR__ . '/../inc/header.php'; //session_start();は含まれているので注意

// セッションチェックとユーザーID取得
if (!isset($_SESSION['chk_ssid']) || !isset($_SESSION['user_id'])) {
    redirect('index.php');
}
?>

<main>
<site-intro>
<h3>G's Life QUESTについて</h3>



</site-intro>
</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>

</body>
</html>