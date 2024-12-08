<?php
// header.php
session_start();
require_once __DIR__ . "/../php/funcs.php";
// ログイン状態の簡易チェック
$is_logged_in = isLoggedIn();

//var_dump($_SESSION);  // デバッグ用＞セッションの中身を確認
//var_dump($is_logged_in);  // デバッグ用＞ログイン状態を確認
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DotGothic16&family=Kaisei+Decol&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <title>G's LIFE QUEST</title>
</head>

<body>
    <!-- Header Start -->
    <header class="site-header">
    <?php if ($is_logged_in) : ?>
        <!-- ログイン済みの場合 -->
        <div class="menu">
            <button class="search-friends">友達を探す</button>
        </div>
        <?php endif; ?>
        <div class="title"><a href="main.php">G's LIFE QUEST</a></div>
        <?php if ($is_logged_in) : ?>
                    <!-- ログイン済みの場合 -->
                    <form action="../php/logout.php" method="POST" style="display: inline;">
                        <button type="submit" id="logout-btn" class="logout-btn">
                            ログアウト
                        </button>
                    </form>
                <?php endif; ?>
    </header>
    <!-- Header End -->