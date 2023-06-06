<?php
session_start();
$lid=$_POST["lid"];
$lpw=$_POST["lpw"];

//DB接続
include("funcs.php");
$pdo = db_conn();

//データを取得するsql
$sql="SELECT * FROM user_table WHERE lid=:lid AND life_flg=1";
$stmt = $pdo->prepare("$sql");
$stmt->bindValue(':lid',$lid,PDO::PARAM_STR);
$status = $stmt->execute();

//sql実行時にエラーがある場合
if($status==false){
    sql_error($stmt);
}

//抽出データ数を取得
$val = $stmt->fetch();

// 該当レコードがあればSESSIONに値を代入
$pw = password_verify($lpw, $val["lpw"]);
// echo $pw;
if($pw){ //$pw==trueと同じ意味だよ
    //Login成功時
    $_SESSION["chk_ssid"]  = session_id(); 
    $_SESSION["kanri_flg"] = $val['kanri_flg'];
    $_SESSION["name"]      = $val['name'];
    $_SESSION["id"] = $val['id'];
    //Login成功時（リダイレクト）
    redirect("select.php");
  }else{
    //Login失敗時(Logoutを経由：リダイレクト)
    redirect("entrance.php");
  }
  
exit();  
?>