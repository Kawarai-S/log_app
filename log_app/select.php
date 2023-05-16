<?php
//1.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
try{
    $pdo=new PDO('mysql:dbname=cat_db;charaset=utf8;host=localhost','root','');//host名,ID,パスワード
}catch(PDOException $e){
    exit('DbConnectError:'.$e->getMessage());
}

//2.データ取得SQL
$stmt=$pdo->prepare("SELECT * FROM target_table");
$status=$stmt->execute();

//3.データ表示
$view="";
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
        $view .= '<div class="box">';
        $view .= '<div class="select">';
        $view .= '<a href="top.php?id='.$result["id"].'">';
        $view .= '<div class="target_icon"><img src="img/'.$result["photo"].'"></div>';
        $view .= '<div>'.$result["name"].'</div>';
        $view .= '</a>';
        $view .= '</div>';
        $view .= '</div>';
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="main">
            <?=$view?>
        </div>
        <div class="menu">
            <ul>
                <li><a href="select.php"><i class="fa-solid fa-paw"></i><span>Pets</span></a></li>
                <li><a href="#"><i class="fa-solid fa-chart-line"></i><span>Chart</span></a></li>
                <li><a href="#"><i class="fa-solid fa-stethoscope"></i><span>Hospital</span></a></li>
                <li><a href="#"><i class="fas fa-user"></i><span>Profile</span></a></li>
            </ul>
        </div>
    </div>
</body>
</html>
