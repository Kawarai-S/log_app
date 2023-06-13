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

    //最初の一匹のidを取得
    $target_id = ""; // target_tableから取得するidを格納する変数
    $sql = "SELECT id FROM target_table WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $target_id = $row['id'];
        }
    }

    //Login成功時（リダイレクト）
    redirect("top.php?id=$target_id");
  }else{
    //Login失敗時(Logoutを経由：リダイレクト)
    redirect("entrance.php");
  }
  
exit();  
?>