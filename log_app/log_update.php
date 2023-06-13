<?php
session_start();
include("funcs.php");
sschk();

//入力確認
if (
    !isset($_POST["date"]) || $_POST["date"] == "" ||
    !isset($_POST["item_id"]) || $_POST["item_id"] == "" ||
    (!isset($_POST["value"]) && !isset($_POST["checkbox"])) ||
    (isset($_POST["value"]) && isset($_POST["checkbox"]))
) {
    exit('ParamError');
}

// var_dump($_POST);
// 1.POSTで取得
$id=$_POST["id"];
$date=$_POST["date"];
// $value=$_POST["value"];
// $checkbox=$_POST["checkbox"];
$checkbox = isset($_POST['checkbox']) ? 1 : NULL;
$value = isset($_POST['value']) ? $_POST['value'] : NULL;
$memo=$_POST["memo"];
$item_id=$_POST["item_id"];

// //2.DB接続
$pdo = db_conn();

// //3.UPDATEで更新
// $sql = "UPDATE log_table SET item_id=:item_id, value=:value ,memo=:memo, date=:date WHERE id=:id";
$sql = "UPDATE log_table SET item_id=:item_id, value=:value, checkbox=:checkbox, memo=:memo, date=:date WHERE id=:id";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':id',$id,PDO::PARAM_INT);
$stmt->bindValue(':item_id',$item_id,PDO::PARAM_INT);
$stmt->bindValue(':value',$value,PDO::PARAM_STR);
$stmt->bindValue(':checkbox',$checkbox,PDO::PARAM_INT);
$stmt->bindValue(':memo',$memo,PDO::PARAM_STR);
$stmt->bindValue(':date',$date,PDO::PARAM_STR);

// var_dump($_POST);
$status=$stmt->execute();
// var_dump($status);
// print_r($status-> errorInfo());

// 4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    sql_error($stmt);
}else{
    //5.index.phpへリダイレクト
    header("Location: log_view.php?id=".$item_id);
    exit;
}
?>