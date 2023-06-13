<?php
session_start();
include("funcs.php");
sschk();


//入力チェック（受信確認処理追加）
if(
    !isset($_POST["name"]) || $_POST["name"]=="" ||
    !isset($_POST["gender"]) || $_POST["gender"]=="" ||
    !isset($_POST["birth"]) || $_POST["birth"]=="" ||
    !isset($_POST["type"]) 
){
    exit('ParamError');
}

//1.POSTデータ取得
$name=$_POST["name"];
$gender=$_POST["gender"];
$birth=$_POST["birth"];
$type=$_POST["type"];
$user_id=$_SESSION["id"];

//2.DBに接続する（エラー処理追加）
$pdo = db_conn();

//1-2. FileUPLOAD
$status = fileUpload("photo","img/");
if($status==1){
  exit("UploadError1");
}else if($status==2){
  exit("UploadError2");
}else{
  //Good
  $photo = $status;  //ファイル名
}

//3.データ登録SQL作成
$sql = "INSERT INTO target_table(id, name, gender, birth, type, photo, user_id, indate )
VALUES(NULL, :name, :gender, :birth, :type, :photo, :user_id, sysdate())";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':name',$name,PDO::PARAM_STR);
$stmt->bindValue(':gender',$gender,PDO::PARAM_STR);
$stmt->bindValue(':birth',$birth,PDO::PARAM_STR);
$stmt->bindValue(':type',$type,PDO::PARAM_STR);
$stmt->bindValue(':photo',$photo,PDO::PARAM_STR);
$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
$status=$stmt->execute();

//4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    //5.index.phpへリダイレクト
    $last_insert_id = $pdo->lastInsertId();
    $redirect_url = "top.php?id=" . urlencode($last_insert_id);

    header("Location: " . $redirect_url);
    exit;
}

?>
