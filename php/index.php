<?php
// index.php
require_once __DIR__ . '/../inc/header.php';
?>
<main class="main">
    <!-- ポップアップ -->
    <div id="popup-wrapper">
        <div id="popup-inside">
            <div id="close">x</div>
            <div id="message-content">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="success" data-show-popup="true"> <!-- data属性を追加 -->
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error" data-show-popup="true">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div id="form-content" style="display: none;"></div>
        </div>
    </div>
    <div class="menu-container">
        <p class="intro">モットーは「自分も大事に」</p>
        <p class="intro">日々の睡眠、食事、運動、学習の記録をつけて<br>
            自分をよしよし出来てるか？確認をしましょう</p>
        <p class="intro">まずは<button class="signin-btn"><a href="signin.php">新規登録</a></button></p>
        <p class="intro">登録済みの方は以下からログインして進んでください</p>
        <form id="login-form" action="login_act.php" method="post">
            <div class="form-group">
                <label for="lid">ユーザーID：</label>
                <input type="text" id="lid" name="lid" required>
            </div>
            <div class="form-group">
                <label for="lpw">パスワード：</label>
                <input type="password" id="lpw" name="lpw" required>
            </div>
            <button type="submit" class="login-btn">ログイン</button>
        </form>
    </div>
</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>
<script src="../js/popup.js"></script>
</body>

</html>