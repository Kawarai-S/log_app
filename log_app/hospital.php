<?php
session_start();
include("funcs.php");
sschk();

//target_idを取得
$target_id=$_GET["id"];

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>通院記録</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/forms.css">
</head>
<body>
    <div class="wrap">
        <div class="Form">
            <p class="title">通院記録</p>
            <form method="POST" action="hospital_insert.php"  enctype="multipart/form-data">
                <div class="Form-Item">
                    <p class="Form-Item-Label">タイトル</p>
                    <input  class="Form-Item-Input" type="text" name="title">
                </div>
                <div class="Form-Item">
                    <p class="Form-Item-Label">日時</p>
                    <input class="Form-Item-Input" type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
                <div class="Form-Item">
                    <p class="Form-Item-Label">カテゴリ</p>
                        <select  class="Form-Item-Input" name="category">
                            <option value="">選択してください</option>
                            <option value="通常受診">通常受診</option>
                            <option value="ワクチン・予防接種">ワクチン・予防接種</option>
                            <option value="定期健診">定期健診</option>
                            <option value="検査">検査</option>
                            <option value="その他">その他</option>
                        </select>
                </div>
                <div class="Form-Item">
                    <p class="Form-Item-Label">メモ</p>
                    <textarea type="text" name="memo" rows="5"></textarea>
                </div>
                <div class="Form-Item">
                    <p class="Form-Item-Label">写真</p>
                    <input type="file" name="photo" accept="image/*">
                    <div class="cms-thumb">
                        <img src="" width="200px">
                    </div>
                </div>
                    <input type="hidden" name="target_id" value="<?=$target_id?>">
                    <input class="Form-Btn" type="submit" value="OK">
            </form>
        </div>
    </div>
</body>
</html>