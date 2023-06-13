<?php
session_start();
include("funcs.php");
sschk();

//1. POSTデータ取得
$log_id = $_POST["log_id"];

//2. DB接続します
$pdo = db_conn();

//３．削除処理
$sql="DELETE FROM log_table WHERE id=:log_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':log_id', $log_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute(); //実行

//４．削除処理後
if($status==true){
  $response = array("status"=>"success");
}else{
  $response =array("status"=>"error");
}
echo json_encode($response);
?>
