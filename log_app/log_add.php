<?php
session_start();
if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
    echo "LOGIN Error!";
    exit();
}
//0.GETでid値を取得
$item_id = $_GET["id"];
$target_id=$_GET["target_id"];

//1.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
include("funcs.php");
$pdo = db_conn();

//2.データ取得SQL
$sql = "SELECT*FROM item_table WHERE id=:item_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':item_id',$item_id,PDO::PARAM_INT);
$status = $stmt->execute();

//3.データ表示
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    $row=$stmt->fetch();
}

//4.フォーム分岐
$view = "";
if ($row['type'] === 'number') {
    // 数値の場合はテキストボックスを表示する
    $view .= '<div class="Form-Item">';
    $view .= '<p class="Form-Item-Label">'.$row["item"].'('.$row["unit"].')'.'</p>';
    $view .= '<input class="Form-Item-Input" type="text" name="value">';
    $view .= '</div>';
} else if ($row['type'] === 'checkbox') {
    // チェックボックスの場合は、複数の選択肢を表示する
    $view .= '<div class="Form-Item">';
    $view .= '<p class="Form-Item-Label">チェック</p>';
    $view .= '<input class="Form-Item-Input" type="checkbox" name="checkbox" value="1">';
    $view .= '</div>';
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>記録</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/forms.css">
</head>
<body>
    <div class="wrap">
        <div class="Form">
            <form method="POST" action="log_insert.php" >
                <div class="Form-Item">
                    <p class="Form-Item-Label">日時</p>
                    <input class="Form-Item-Input" type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
                <?=$view?>
                <div class="Form-Item">
                    <p class="Form-Item-Label">メモ</p>
                    <input  class="Form-Item-Input" type="textarea" name="memo">
                </div>
                <input type="hidden" name="item_id" value="<?=$item_id?>">
                <input type="hidden" name="target_id" value="<?=$target_id?>">
                <input class="Form-Btn" type="submit" value="OK">
            </form>
        </div>
    </div>
    </body>
</html>