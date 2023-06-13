<?php
session_start();
include("funcs.php");
sschk();


//入力チェック（受信確認処理追加）
if(
    !isset($_POST["title"]) || $_POST["title"]=="" ||
    !isset($_POST["date"]) || $_POST["date"]=="" ||
    !isset($_POST["category"]) || $_POST["category"]=="" ||
    !isset($_POST["memo"]) || $_POST["memo"]=="" ||
    !isset($_POST["target_id"]) || $_POST["target_id"]==""

){
    exit('ParamError');
}

//1.POSTデータ取得
$title=$_POST["title"];
$date=$_POST["date"];
$category=$_POST["category"];
$memo=$_POST["memo"];
$target_id=$_POST["target_id"];

//2.DBに接続する（エラー処理追加）
$pdo = db_conn();

//1-2. FileUPLOAD
$status = fileUpload2("photo","h_img/");
if($status==1){
  exit("UploadError1");
}else if($status==2){
  exit("UploadError2");
}else{
  //Good
  $photo = $status;  //ファイル名
}

//3.データ登録SQL作成
$sql = "INSERT INTO hospital_table(id, title, date, category, memo, photo, target_id)
VALUES(NULL, :title, :date, :category, :memo, :photo, :target_id)";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':title',$title,PDO::PARAM_STR);
$stmt->bindValue(':date',$date,PDO::PARAM_STR);
$stmt->bindValue(':category',$category,PDO::PARAM_STR);
$stmt->bindValue(':memo',$memo,PDO::PARAM_STR);
$stmt->bindValue(':photo',$photo,PDO::PARAM_STR);
$stmt->bindValue(':target_id',$target_id,PDO::PARAM_STR);
$status=$stmt->execute();

//4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    //5.index.phpへリダイレクト
    header("Location: hospital_serch.php?id=" . $target_id);
    exit;
}

?>