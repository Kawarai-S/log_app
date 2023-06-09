<?php
session_start();
if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
    echo "LOGIN Error!";
    exit();
}

//0.GETでid値を取得
$target_id = $_GET["id"];
$user_id=$_SESSION["id"];

//1.DBに接続する（エラー処理追加）
include("funcs.php");
$pdo = db_conn();

//2.データ取得SQL（target）
$sql = "SELECT*FROM target_table WHERE id=:target_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':target_id',$target_id,PDO::PARAM_INT);
$status = $stmt->execute();

//3.データ表示(target)
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    $row=$stmt->fetch();
}


// ユーザーが所有する全てのペットを取得するSQL
$sql = "SELECT * FROM target_table WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();

$list = "";
if ($status == false) {
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("QueryError: " . $error[2]);
} else {
    // ペットのリストを表示する
    while ($pet_row = $stmt->fetch()) {
        $tabId = $pet_row["id"]; // targetのidをタブの識別子とする

        // ページを読み込んだ時に取得した$target_idと$tabIDが一致したら背景の色を変える
        if ($target_id == $tabId) {
            $list .= '<li class="tab"  data-tab="' . $tabId . '" style="background-color:#b9e9f2;">';
            $list .= '<a href="top.php?id=' . $tabId . '">' . $pet_row["name"] . '</a>';
            $list .= '</li>';
        } else {
            $list .= '<li class="tab"  data-tab="' . $tabId . '">';
            $list .= '<a href="top.php?id=' . $tabId . '">' . $pet_row["name"] . '</a>';
            $list .= '</li>';
        }
    }
}



// データ取得SQL(item&log)
$sql = "SELECT item_table.*, latest_log.value, latest_log.checkbox, latest_log.date,
                DATE_FORMAT(latest_log.date, '%Y-%m-%d %H:%i') AS f_date 
        FROM item_table 
        LEFT JOIN (
            SELECT item_id, value, checkbox, date 
            FROM log_table AS l1
            WHERE date = (
                SELECT MAX(date) 
                FROM log_table AS l2 
                WHERE l1.item_id = l2.item_id
            )
        ) AS latest_log ON item_table.id = latest_log.item_id 
        WHERE target_id=:target_id
        ORDER BY item_table.id ASC";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':target_id',$target_id,PDO::PARAM_INT);
$status = $stmt->execute();

// データ表示(item&log)
$view="";
if ($status == false) {
    // SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("QueryError:".$error[2]);
} else {
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 項目名・アイコンの表示・項目履歴一覧へリンク
        $view .= '<div class="box">';
        $view .= '<div class="items_box">';
        // $view .= '<a href="log.php?id='.$result["id"].'">';
        $view .= '<div class="icon_box"><img src="'.$result["icon"].'"></div>';
        // $view .= '</a>';
        // 最新の記録を表示
        if (!is_null($result["value"])) {
            $view .= '<div class="log_box">';
            $view .= '<a href="log_chart.php?id='.$result["id"].'&target_id='.$target_id.'">';
            $view .= '<div>'.$result["item"].'</div><div>'.$result["value"].' '.$result["unit"].'</div><div>'.$result["f_date"].'</div>';
            $view .= '</a>';
            $view .= '</div>';
        } elseif (!is_null($result["checkbox"])) {
            $view .= '<div class="log_box">';
            $view .= '<a href="log_view.php?id='.$result["id"].'&target_id='.$target_id.'">';
            $view .= '<div>'.$result["item"].'</div><div><img src="icon/check.png" style="width:24px; height:24px;"></div><div>'.$result["f_date"].'</div>';
            $view .= '</a>';
            $view .= '</div>';
        }
        // 項目記録ページへリンク
        $view .= '<div class="add_box">';
        $view .= '<a href="log_add.php?id='.$result["id"].'&target_id='.$target_id.'">';
        $view .= '<div>';
        $view .= '<img src="icon/pen.png" style="width: 32px; height: 32px">';
        $view .= '</div>';
        $view .= '</a>';
        $view .= '</div>';
        $view .= '</div>';
        $view .= '</div>';
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
    <title>記録一覧</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="main">
            <!-- <div class="box icon"> -->
                <!-- <div class="add_icon"> -->
                    <!-- <a href="register.php"> -->
                        <!-- <i class="fa-regular fa-square-plus size"></i> -->
                        <!-- <img src="icon/plus.png"  style="width: 24px; height: 24px"> -->
                    <!-- </a> -->
                <!-- </div>     -->
            <!-- </div> -->
            <div class="box">   
                <div id="pet_tabs">
                    <ul>
                        <?=$list?>
                        <li class="tab">
                            <a href="register.php">
                            <i class="fa-regular fa-square-plus size"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="target_box">
                    <!-- アイコン画像 -->
                    <div class="target">
                        <div class="target_icon"><img src="img/<?=$row["photo"]?>" alt="アイコン"></div>
                    </div>
                    <!-- 名前・年齢・性別 -->
                    <div class="name">
                        <p><?=$row["name"]?></p>
                        <p><?=$ageInYears."歳".$ageInMonths."ヶ月"?>　<?=$row["gender"]?></p>
                    </div>
                    <!-- 誕生日・生まれて何日 -->
                    <div class="birth">
                        <table class="birth_table">
                            <tr><td>誕生日</td><td class="left"><?=$row["birth"]?></td></tr>
                            <tr><td>生まれて</td><td class="left"><?=$ageInDays."日目"?></td></tr> 
                        </table>
                    </div>
                </div>
            </div>
            <div class="box icon">
                <div style="font-size:1.1rem; font-weight:bold;">LOG</div>
                <div class="add_icon">
                    <a href="item_add.php?id=<?=$target_id?>">
                        <i class="fa-regular fa-square-plus size"></i>
                        <!-- <img style="width: 24px; height: 24px" src="icon/plus.png" alt="項目追加"> -->
                    </a>                
                </div>              
            </div>
                <?=$view?>
            </div>
                <?php include("menu.php"); ?>
            <!-- <div class="menu">
                <ul>
                    <li><a href="calendar.php"><i class="fa-solid fa-calendar-days"></i><span>Calendar</span></a></li>
                    <li><a href="chart_view.php"><i class="fa-solid fa-book"></i><span>Diary</span></a></li>
                    <li><a href="hospital_serch.php?id=<?=h($target_id)?>"><i class="fa-solid fa-stethoscope"></i><span>Hospital</span></a></li>
                    <li><a href="#"><i class="fas fa-user"></i><span>Profile</span></a></li>
                    <li>
                        <form method="post" action="logout.php">
                            <button type="submit" name="logout" class="logout_btn">
                                <i class="fa-solid fa-right-from-bracket"></i><span>Log out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div> -->
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script>
        $(document).ready(function(){
            $(".tab").on("click",function(){
                let tabId = $(this).data('data-tab');
         
                $('.tab').removeClass('active');

                $(this).addClass('active');
                
            });
            // 最初のタブに active クラスを追加
            $('.tab:first').addClass('active');
        })
    </script> -->
</body>
</html>