<?php
//0.GETでid値を取得
$item_id = $_GET["id"];

//1.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
try{
    $pdo=new PDO('mysql:dbname=cat_db;charaset=utf8;host=localhost','root','');//host名,ID,パスワード
}catch(PDOException $e){
    exit('データベースに接続出来ませんでした。'.$e->getMessage());
}

//2.データ取得SQL
$sql = "SELECT id, item_id, DATE_FORMAT(date, '%Y-%m-%d %H:%i') as f_date, value, checkbox 
        FROM log_table WHERE item_id=:item_id ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':item_id',$item_id,PDO::PARAM_INT);
$status = $stmt->execute();

//3.データ表示
$view="";
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
        $view .= '<div class="box">';
        $view .= '<div class="log">';
        $view .= '<a href="log_update.php?id='.$result["id"].'">';
        $view .= '<div>'.$result["f_date"].'</div>';
        if(!is_null($result["checkbox"])){
            $view .= '<div>'.$result["checkbox"].'</div>';
        }elseif(!is_null($result["value"])){
            $view .= '<div>'.$result["value"].'</div>';
        }
        $view .= '</a>';
        $view .= '</div>';
        $view .= '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
            <?=$view?>
        </div>
    </div>
</body>
</html>