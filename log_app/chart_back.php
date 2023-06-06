<?php
session_start();
if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
    echo "LOGIN Error!";
    exit();
}

$item_id=$_POST["item_id"];

//最初の読み込み時に当月を割り当て
$s_month = date('m');

if(isset($_POST["month-select"]) && $_POST["month-select"]!=""){
    $s_month = $_POST["month-select"];
}

//データベース接続
include("funcs.php");
$pdo = db_conn();

//データ取得
$sql="SELECT value,date FROM log_table WHERE item_id=:item_id  AND DATE_FORMAT(date, '%Y-%m') = :s_month  ORDER BY date";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':s_month',$s_month,PDO::PARAM_STR);
$stmt->bindValue(':item_id',$item_id,PDO::PARAM_INT);
$status = $stmt->execute();

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
//JSONに変換
$json = json_encode($values,JSON_UNESCAPED_UNICODE);
echo $json;
?>