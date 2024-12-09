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
        <p>ジーズアカデミーでの日々の努力を記録して</p>
        <p>一緒に「セカイ」を変えていきましょう！</p>
        <p>ログイン後のページについて説明します</p>
        <div class="help-info">
            <img id="help3" class="help-img" src="../img/help3.png" alt="ページの説明">
            <div class="help3-info">睡眠と学習の目標時間が設定できます。まずはここで目標を設定してみましょう</div>
        </div>
        <div class="help-info">
            <img id="help2" class="help-img" src="../img/help2.png" alt="ページの説明">
            <div class="help2-info">左端のカレンダーから過去の記録も保存できます</div>
        </div>
        <div class="help-info">
            <img id="help4" class="help-img" src="../img/help4.png" alt="ページの説明">
            <div class="help-info4">プロフィールの変更はここから</div>
        </div>
        <div class="help-info">
            <img id="help8" class="help-img" src="../img/help8.png" alt="ページの説明">
            <div class="help-info8">プロフィール画像、名前、ユーザーIDが変更できます</div>
        </div>
        <div class="help-info">
            <img id="help9" class="help-img" src="../img/help9.png" alt="ページの説明">
            <div class="help-info9">退会を希望の場合はこちらから</div>
        </div>
        <div class="help-info">
            <h4>称号について</h4>
            <img id="help10" class="help-img" src="../img/help10.png" alt="ページの説明">
            <p>以下の指標を達成した場合、その週の称号が画面に表示されます</p>
            <div class="achievements-info">
            <div class="getup">早起きプロ 🌅：<br>6時前の起床が累計4日以上</div>
            <div class="nutri">栄養大臣 🍳；<br>食事スコア80%以上</div>
            <div class="active">アクティブの申し子 🏃：<br>運動スコア80%以上</div>
            <div class="self">自走力ロケット 📚：<br>学習スコア80%以上</div>
            </div>
        </div>
        <button class="back"><a href="main.php">戻る</a></button>
    </site-intro>
</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>
</body>

</html>