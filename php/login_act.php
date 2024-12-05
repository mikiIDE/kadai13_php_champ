<?php
// loggin_act.php
session_start();
require_once __DIR__ . '/funcs.php'; //関数ファイル読み込み
if ($_SERVER['REQUEST_METHOD'] != 'POST') {//直接このページを見に来た場合はリダイレクトする
    redirect("index.php");
}

//POST値
$lid = $_POST["lid"];
$lpw = $_POST["lpw"];

// DB接続
$pdo = db_conn();

// データ確認
//* PasswordがHash化→条件はlidのみ！！
$stmt = $pdo->prepare("SELECT * FROM user_info WHERE lid = :lid");
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR); //lidだけ渡すのがポイント
$status = $stmt->execute();

// SQL実行時にエラーがある場合STOP
if($status==false){
    sql_error($stmt);
}

// データの参照
$user = $stmt->fetch(PDO::FETCH_ASSOC);
//ユーザーが見つからない場合の処理
if(!$user){
    $_SESSION["error"] = "ログインIDまたは<br>パスワードが間違っています";
    redirect("index.php");
}

//該当レコードがあればSESSIONに値を代入
//入力したPasswordと暗号化されたPasswordを比較！[戻り値：true,false]
$pw = password_verify($lpw, $user["lpw"]); 
if($pw){ 
  //Login成功時
  $_SESSION["chk_ssid"] = session_id(); //ここで一旦SESSIONに預ける
  $_SESSION["name"] = $user["name"];
  $_SESSION["user_id"] = $user["id"];
  //Login成功時
  redirect("main.php");

}else{
  //Login失敗時の処理
  $_SESSION["error"] = "ログインIDまたはパスワードが間違っています";
  redirect("index.php");
}
exit();
