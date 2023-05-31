<?php
//target_idを取得
$target_id=$_GET["id"];


//データベース接続
include("funcs.php");
$pdo = db_conn();

//データ取得
$stmt=$pdo->prepare("SELECT*FROM icon_table");
$status=$stmt->execute();

// 取得したアイコン情報をセレクトボックスのoptionタグに変換する
$icon="";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $icon .="<option value=\"" . $row["file_name"] . "\">" . $row["icon_name"] . "</option>";
}

// データベースとの接続を切断する
$dbh = null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>記録項目追加</title>
    <link rel="stylesheet" href="css/form.css">
</head>
<body>

    <div class="Form">
        <form method="POST" action="item_insert.php" >
            <div class="Form-Item">
                <p class="Form-Item-Label">記録項目</p>
                <input  class="Form-Item-Input" type="text" name="item">
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label">アイコン</p>
                    <select  class="Form-Item-Input" name="icon">
                        <option value="">選択してください</option>
                        <?=$icon?>
                    </select>
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label">記録形式</p>
                <select class="Form-Item-Input" name="type">
                    <option value="">選択してください</option>
                    <option value="number">数字</option>
                    <option value="checkbox">チェックのみ</option>
                </select>
                </p>
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label"> 単位</p>
                <input class="Form-Item-Input" type="text" name="unit">
            </div>
                <input type="hidden" name="target_id" value="<?=$target_id?>">
                <input class="Form-Btn" type="submit" value="OK">
        </form>
    </div>
</body>
</html>