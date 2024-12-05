<?php
// logout.php
session_start();
require_once __DIR__ . '/funcs.php';

// このページに直接これないように設定
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// セッションを破棄
$_SESSION = array();
session_destroy();

//Cookieに保存してある"SessionIDの保存期間を過去にして破棄
if (isset($_COOKIE[session_name()])) { //session_name()は、セッションID名を返す関数
    setcookie(session_name(), '', time()-42000, '/');
}

// ログインページへリダイレクト
redirect('index.php');
?>