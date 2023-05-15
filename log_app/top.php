<?php
//0.GETでid値を取得
$target_id = $_GET["id"];

//1.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
try{
    $pdo=new PDO('mysql:dbname=cat_db;charaset=utf8;host=localhost','root','');//host名,ID,パスワード
}catch(PDOException $e){
    exit('データベースに接続出来ませんでした。'.$e->getMessage());
}

//2.データ取得SQL（target）
$sql = "SELECT*FROM target_table WHERE id=:target_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':target_id',$target_id,PDO::PARAM_INT);
$status = $stmt->execute();

// $stmt2=$pdo->prepare("SELECT * FROM item_table");
// $status2=$stmt2->execute();


//3.データ表示(target)
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    $row=$stmt->fetch();
}

// データ取得SQL(item&log)
$sql = "SELECT item_table.*, latest_log.value, latest_log.checkbox, latest_log.date 
        FROM item_table 
        LEFT OUTER JOIN (
            SELECT item_id, value, checkbox, date 
            FROM log_table AS l1
            WHERE date = (
                SELECT MAX(date) 
                FROM log_table AS l2 
                WHERE l1.item_id = l2.item_id
            )
        ) AS latest_log ON item_table.id = latest_log.item_id 
        ORDER BY item_table.id ASC";

$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

// データ表示(item&log)
if ($status == false) {
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("QueryError:".$error[2]);
} else {
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 項目名・アイコンの表示・項目履歴一覧へリンク
        $view .= '<div class="box"><div>';
        $view .= '<a href="log_view.php?id='.$result["id"].'">';
        $view .= '<div><img src="'.$result["icon"].'">'.$result["item"].'</div>';
        $view .= '</a>';
        // 最新の記録を表示
        if (!is_null($result["value"])) {
            $view .= '<div>'.$result["value"].' '.$result["unit"].' ('.$result["date"].')</div>';
        } elseif (!is_null($result["checkbox"])) {
            $view .= '<div>'.$result["checkbox"].' ('.$result["date"].')</div>';
        }
        // 項目記録ページへリンク
        $view .= '<div>';
        $view .= '<a href="log_add.php?id='.$result["id"].'">+</a>';
        $view .= '</div>';
        $view .= '</div></div>';
    }
}




//4.月齢の計算
$now = new DateTime(); // 現在日時を取得
$birth = new DateTime($row["birth"]); // データベースから取得した誕生日をDateTimeオブジェクトに変換
$diff = $now->diff($birth); // 現在日時と誕生日の差分を計算
$ageInMonths = $diff->y * 12 + $diff->m;



//5.年月の計算
$ageInYears = floor($ageInMonths / 12); // 年を計算
$ageInMonths -= $ageInYears * 12; // 月を計算

//6.生まれてから何日？
$ageInDays = $diff->days;



?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="main">
            <div class="box target_box">   

                <!-- アイコン画像 -->
                <div class="target_icon"><img src="img/<?=$row["photo"]?>" alt="アイコン"></div>
                <!-- 名前・年齢・性別 -->
                <div>
                    <p><?=$row["name"]?></p>
                    <p><?=$ageInYears."歳".$ageInMonths."ヶ月"?><?=$row["gender"]?></p>
                </div>
                <!-- 誕生日・生まれて何日 -->
                <div>
                    <p><?=$row["birth"]?><?=$ageInDays."日"?></p> 
                </div>
            </div>
            <div class="box">
                <p>記録</p>
                <img src="" alt="項目追加">
            </div>
                <?=$view?>
            </div>
        </div>
    </div>
</body>
</html>