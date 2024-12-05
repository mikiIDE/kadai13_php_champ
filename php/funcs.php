<?php
// funcs.php
// ログイン済みかどうかの確認（シンプル）
// ヘッダーの部分に使う
function isLoggedIn()
{
    return isset($_SESSION['chk_ssid']) && isset($_SESSION['user_id']);
}

//SessionCheck(スケルトン)
function sschk()
{
    if (!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id()) {
        exit("Login Error");
    } else {
        session_regenerate_id(true); //セッションハイジャック対策。まったく新しいユニークKEYを発行してくれる。特定されづらくなる。
        $_SESSION["chk_ssid"] = session_id();
    }
}

//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

//SQLエラー
function sql_error($stmt)
{
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("SQLError:" . $error[2]);
}

//リダイレクト
function redirect($file_name)
{
    header("Location: " . $file_name);
    exit();
}

//DB接続関数：db_conn()
require __DIR__ . '/../vendor/autoload.php'; // Composerのオートローダーを読み込み
function db_conn()
{
    try {
        // .env.localを読み込む
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../.env', '.env.local'); //ローカル検証環境の場合は'.env.local'
        $dotenv->load();

        // .envファイルから取得した値を使う。$_ENVはスーパーグローバル変数というらしい
        $db_name = $_ENV['DB_NAME'];
        $db_id   = $_ENV['DB_USER'];
        $db_pw   = $_ENV['DB_PASSWORD'];
        $db_host = $_ENV['DB_HOST'];

        return new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}
//実際に↑を利用する際は
// <?php
// // 関数ファイルを読み込む（includeではなくrequire_once推奨。二重呼び込みやエラーの際の実行を避ける）
// require_once __DIR__ . '/../funcs.php';
// // DB接続
// $pdo = db_conn();
