<?php
session_start();
if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
    echo "LOGIN Error!";
    exit();
}

$user_id=$_SESSION["id"];

// 1.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
include("funcs.php");
$pdo = db_conn();

//2.データ取得SQL
$sql="SELECT * FROM target_table WHERE user_id=:user_id";
$stmt=$pdo->prepare($sql);
$stmt->bindValue(':user_id',$user_id,PDO::PARAM_STR);
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

        // $view .='<div class="box">';   
        // $view .='<div class="target_box">';
        // $view .= '<a href="top.php?id='.$result["id"].'">';
        // // アイコン画像
        // $view .='<div class="target">';
        // $view .='<div class="target_icon"><img src=".img/'.$result["photo"].'" alt="アイコン"></div>';
        // $view .='</div>';
        // // 名前・年齢・性別
        // $view .= '<div class="name">';
        // $view .= '<p>'.$result["name"].'</p>';
        // $view .= '<p>'.$ageInYears.'歳'.$ageInMonths.'ヶ月　'.$result["gender"].'</p>';
        // $view .= '</div>';
        // // 誕生日・生まれて何日 -->
        // $view .=  '<div class="birth">';
        // $view .=  '<table class="birth_table">';
        // $view .=  '<tr><td>誕生日</td><td class="left">'.$result["birth"].'</td></tr>';
        // $view .=  '<tr><td>生まれて</td><td class="left">'.$ageInDays.'日目'.'</td></tr>'; 
        // $view .=  '</table>';
        // $view .=  '</div>';
        // $view .=  '</a>';
        // $view .=  '</div></div>';

    }
}

// //4.月齢の計算
// $now = new DateTime(); // 現在日時を取得
// $birth = new DateTime($row["birth"]); // データベースから取得した誕生日をDateTimeオブジェクトに変換
// $diff = $now->diff($birth); // 現在日時と誕生日の差分を計算
// $ageInMonths = $diff->y * 12 + $diff->m;



// //5.年月の計算
// $ageInYears = floor($ageInMonths / 12); // 年を計算
// $ageInMonths -= $ageInYears * 12; // 月を計算

// //6.生まれてから何日？
// $ageInDays = $diff->days;



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
                <li><a href="chart.php"><i class="fa-solid fa-chart-line"></i><span>Chart</span></a></li>
                <li><a href="#"><i class="fa-solid fa-stethoscope"></i><span>Hospital</span></a></li>
                <li><a href="#"><i class="fas fa-user"></i><span>Profile</span></a></li>
            </ul>
        </div>
    </div>
</body>
</html>
