<?php
session_start();
include("funcs.php");
sschk();

//1.GETでid値を取得
$id = $_GET["id"];

//2.DB接続など
$pdo = db_conn();

//3.SELECT*FROM
$sql = "SELECT * FROM log_table
        LEFT JOIN item_table
        ON log_table.item_id = item_table.id
        WHERE log_table.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id',$id,PDO::PARAM_INT);
$status = $stmt->execute();



//4.データ表示
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    $row=$stmt->fetch();
}

$view = "";
if ($row['type'] === 'number') {
    // 数値の場合はテキストボックスを表示する
    $view .= '<div class="Form-Item">';
    $view .= '<p class="Form-Item-Label">'.$row["item"].'('.$row["unit"].')'.'</p>';
    $view .= '<input class="Form-Item-Input" type="text" name="value" value="'.$row["value"].'">';
    $view .= '</div>';
} else if ($row['type'] === 'checkbox') {
    // チェックボックスの場合は、複数の選択肢を表示する
    $view .= '<div class="Form-Item">';
    $view .= '<p class="Form-Item-Label">チェック</p>';
    $view .= '<input class="Form-Item-Input" type="checkbox" name="checkbox" value="'.$row["checkbox"].'">';
    $view .= '</div>';
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>LOG編集</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/forms.css">
</head>
<body>
    <div class="wrap">
        <div class="Form">
            <p class="title">記録の編集</p>
            <form method="POST" action="log_update.php" >
                <div class="Form-Item">
                    <p class="Form-Item-Label">日時</p>
                    <input class="Form-Item-Input" type="datetime-local" name="date" value="<?=$row["date"]?>">
                </div>
                <?=$view?>
                <div class="Form-Item">
                    <p class="Form-Item-Label">メモ</p>
                    <input  class="Form-Item-Input" type="textarea" name="memo" value="<?=$row["memo"]?>">
                </div>
                <input type="hidden" name="item_id" value="<?=$row["item_id"]?>">
                <input type="hidden" name="id" value="<?=$id?>">
                <input class="Form-Btn" type="submit" value="OK">
            </form>
        </div>
    </div>
</body>
</html>