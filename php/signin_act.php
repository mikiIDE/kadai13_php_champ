<?php
// signin_act.php
session_start();
require_once __DIR__ . '/funcs.php'; // 関数ファイルを読み込む（includeではなくrequire_once推奨。二重呼び込みやエラーの際の実行を避ける）
if ($_SERVER['REQUEST_METHOD'] != 'POST') { //直接このページを見に来た場合はリダイレクトする
    redirect("index.php");
}

//1. POSTデータの取得と入力チェック
$name = isset($_POST["name"]) ? $_POST["name"] : '';
$lid = isset($_POST["lid"]) ? $_POST["lid"] : '';
$lpw = isset($_POST["lpw"]) ? $_POST["lpw"] : '';
// パスワードのハッシュ化
$lpw = password_hash($lpw, PASSWORD_DEFAULT);
// 空欄チェック
if(empty($name) || empty($lid) || empty($lpw)){
    exit('ParamError:必須項目が入力されていません');
}

//2. DB接続
$pdo = db_conn();

//3. 既存ユーザーチェック（※同じlidが存在しないか）
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_info WHERE lid = :lid");
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR); //lidだけ渡すのがポイント
$stmt->execute();
if($stmt->fetchColumn() > 0){
    exit('そのログインIDは既に使用されています');
}

//4. データ登録SQL作成
$sql = "INSERT INTO user_info(name, lid, lpw) VALUES(:name, :lid, :lpw)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR); //Integer（文字の場合 PDO::PARAM_STR)（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':lpw', $lpw, PDO::PARAM_STR);
$status = $stmt->execute(); //クエリ（要求）実行役。trueかfalseが返ってくる
$_SESSION['user_id'] = $row['id']; //user_infoに自動で付与されるidをセッションに保存

//5. 登録後の処理
if ($status == false) {
    sql_error($stmt);
} else {
    $_SESSION['success'] = "登録が完了しました！<br>ログインしてください";
    redirect("index.php");  // ログイン画面へ遷移
}