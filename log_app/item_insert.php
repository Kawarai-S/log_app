<?php
session_start();
include("funcs.php");
sschk();

//入力チェック（受信確認処理追加）
if(
    !isset($_POST["item"]) || $_POST["item"]=="" ||
    !isset($_POST["icon"]) || $_POST["icon"]=="" ||
    !isset($_POST["type"]) || $_POST["type"]=="" ||
    !isset($_POST["target_id"]) || $_POST["target_id"]=="" ||
    !isset($_POST["unit"]) 
){
    exit('ParamError');
}

//1.POSTデータ取得
$item=$_POST["item"];
$icon=$_POST["icon"];
$type=$_POST["type"];
$target_id=$_POST["target_id"];
// $unit=$_POST["unit"];
$unit = isset($_POST["unit"]) ? $_POST["unit"] : null;


//2.DBに接続する（エラー処理追加）
$pdo = db_conn();

//3.データ登録SQL作成  
$sql = "INSERT INTO item_table(id, item, icon, type, unit, target_id )VALUES(NULL, :item, :icon, :type, :unit, :target_id)";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':item',$item,PDO::PARAM_STR);
$stmt->bindValue(':icon',$icon,PDO::PARAM_STR);
$stmt->bindValue(':type',$type,PDO::PARAM_STR);
$stmt->bindValue(':unit',$unit,PDO::PARAM_STR);
$stmt->bindValue(':target_id',$target_id,PDO::PARAM_STR);


$status=$stmt->execute();

//4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    sql_error($stmt);
}else{
    //5.index.phpへリダイレクト
    header("Location: top.php?id=" .$target_id);
    exit;
}

?>
