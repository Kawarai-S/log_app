<?php
//入力チェック（受信確認処理追加）
if(
    !isset($_POST["name"]) || $_POST["name"]=="" ||
    !isset($_POST["lid"]) || $_POST["lid"]=="" ||
    !isset($_POST["lpw"]) || $_POST["lpw"]=="" 
){
    exit('ParamError');
}

//1.POSTデータ取得
$name=$_POST["name"];
$lid=$_POST["lid"];
$lpw=$_POST["lpw"];

//2.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
include("funcs.php");
$pdo = db_conn();

//3.データ登録SQL作成
$sql = "INSERT INTO user_table(id, name, lid, lpw, indate )
VALUES(NULL, :name, :lid, :lpw, sysdate())";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':name',$name,PDO::PARAM_STR);
$stmt->bindValue(':lid',$lid,PDO::PARAM_STR);
$stmt->bindValue(':lpw',$lpw,PDO::PARAM_STR);
$status=$stmt->execute();

//4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    sql_error($stmt);
}else{
    //5.index.phpへリダイレクト
    redirect('register.php');
}

?>
