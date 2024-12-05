<?php
// signin.php
require_once __DIR__ . '/../inc/header.php';
?>
<main>
    <div class="menu-container">
        <p class="intro">あなたの情報を登録してください</p>
        <form action="signin_act.php" method="post">
            <div class="form-group">
                <label for="name">お　名　前：</label>
                <input type="text" id="name" name="name" placeholder="64文字以内" required>
            </div>
            <div class="form-group">
                <label for="lid">ユーザーID：</label>
                <input type="text" id="lid" name="lid" placeholder="ログイン時に必要です" required>
            </div>
            <div class="form-group">
                <label for="lpw">パスワード：</label>
                <input type="password" id="lpw" name="lpw" placeholder="ログイン時に必要です" required>
            </div>
            <button type="submit" class="signin">登録</button>
        </form>
    </div>
    </div>
</main>
<?php
require_once __DIR__ . '/../inc/footer.php';
?>