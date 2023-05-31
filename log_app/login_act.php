<?php
session_start();
$lid=$_POST["lid"];
$lpw=$_POST["lpw"];

//DB接続
include("funcs.php");
$pdo = db_conn();

//データを取得するsql
$sql="SELECT * FROM user_table WHERE lid=:lid AND lpw=:lpw";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':lid',$lid,PDO::PARAM_STR);
$stmt->bindValue(':lpw',$lpw,PDO::PARAM_STR);
$status = $stmt->execute();

//sql実行時にエラーがある場合
if($status==false){
    sql_error($stmt);
}

//抽出データ数を取得
$val = $stmt->fetch();

//該当レコードがあればSESSIONに値を代入
if($val["id"]!="" && $val["life_flg"] == 1){
    $_SESSION["chk_ssid"] = session_id();
    // $_SESSION["kanri_flg"]=$val['kanri_flg'];
    // $_SESSION["life_flg"]=$val['life_flg'];
    $_SESSION["name"]=$val['name'];
    //login処理OKの場合select.phpへ遷移
    header("Location: select.php?id=<??>");
}else{
    //login処理NGの場合login.phoへ遷移
    redirect('login.php');

}
?>