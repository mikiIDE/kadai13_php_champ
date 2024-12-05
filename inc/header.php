<?php
// header.php
session_start();
require_once __DIR__ . "/../php/funcs.php";
// ログイン状態の簡易チェック
//$is_logged_in = isLoggedIn();

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
        <div class="menu">
            <button class="search-friends">友達を探す</button>
        </div>
        <div class="title">G's LIFE QUEST</div>
        <button class="logout">ログアウト</button>
    </header>
    <!-- Header End -->