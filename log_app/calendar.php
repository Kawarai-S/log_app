<?php
session_start();
include("funcs.php");
sschk();

$target_id=$_GET["id"];

//************日記の有無の確認と日付を取り出す関数*************/
function getArticlesDates($target_id) {
    $pdo = db_conn();

    $ps = $pdo->prepare("SELECT date FROM diary_table WHERE target_id = :target_id GROUP BY date");
    $ps->execute([':target_id' => $target_id]);;

    $article_dates = array();

    foreach($ps as $out) {
        $date_out = strtotime((string) $out['date']);
        $article_dates[date('Y-m-d', $date_out)] = true;
    }

    ksort($article_dates);
    return $article_dates;
}

//************記事のある日にアイコンを表示させる関数*************/
$diary_array = getArticlesDates($target_id);
function diary_icon($date, $diary_array, $target_id) {
    $icon="";    
    if (array_key_exists($date, $diary_array)) {       
        $icon .= '</br><a href="diary_view.php?date='.$date.'&id='.$target_id.'">';
        $icon .= '<i class="fa-solid fa-book"></i>';
        $icon .= '</a>';
        }    
        return $icon;
}

//************通院記録の有無の確認と日付を取り出す関数*************/
function getHospitalDates($target_id) {
    $pdo = db_conn();

    $ps2 = $pdo->prepare("SELECT date FROM hospital_table WHERE target_id = :target_id GROUP BY date");
    $ps2->execute([':target_id' => $target_id]);;

    $hospital_dates = array();

    foreach($ps2 as $out) {
        $date_out = strtotime((string) $out['date']);
        $hospital_dates[date('Y-m-d', $date_out)] = true;
    }

    ksort($hospital_dates);
    return $hospital_dates;
}

//************通院記録のある日にアイコンを表示させる関数*************/
$hospital_array = getHospitalDates($target_id);
function hospital_icon($date, $hospital_array, $target_id) {
    $icon2="";    
    if (array_key_exists($date, $hospital_array)) {       
        $icon2 .= '</br><a href="hospital_view.php?date='.$date.'&id='.$target_id.'">';
        $icon2 .= '<i class="fa-solid fa-stethoscope"></i>';
        $icon2 .= '</a>';
        }    
        return $icon2;
}





//************カレンダーの表示*************/
// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

// 前月・次月リンクが押された場合は、GETパラメーターから年月を取得
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // 今月の年月を表示
    $ym = date('Y-m');
}

// タイムスタンプを作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// 今日の日付 'j'は一桁表示
$today = date('Y-m-j');

// カレンダーのタイトルを作成 'n'は一桁表示
$html_title = date('Y年n月', $timestamp);

// 前月・次月の年月を取得
// 方法１：mktimeを使う mktime(hour,minute,second,month,day,year)
$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));

// 方法２：strtotimeを使う
// $prev = date('Y-m', strtotime('-1 month', $timestamp));
// $next = date('Y-m', strtotime('+1 month', $timestamp));

// 該当月の日数を取得
$day_count = date('t', $timestamp);

// １日が何曜日か確認 0:日 1:月 2:火 ... 6:土
// 方法１：mktimeを使う
$youbi = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));
// 方法２
// $youbi = date('w', $timestamp);


// カレンダー作成の準備
$weeks = [];
$week = '';

// 第１週目：空のセルを追加
// 例）１日が火曜日だった場合、日・月曜日の２つ分の空セルを追加する
$week .= str_repeat('<td></td>', $youbi);

for ( $day = 1; $day <= $day_count; $day++, $youbi++) {

    // 
    $date = $ym . '-' . $day;

    //予約日設定
    $diary = diary_icon(date("Y-m-d",strtotime($date)),$diary_array,$target_id);
    $hospital = hospital_icon(date("Y-m-d",strtotime($date)),$hospital_array,$target_id);


    if ($today == $date) {
        // 今日の日付の場合は、class="today"をつける
        $week .= '<td class="today">' . $day;
    }else if(diary_icon(date("Y-m-d",strtotime($date)),$diary_array,$target_id)){
        $week .= '<td>' . $day . $diary;
    }else if(hospital_icon(date("Y-m-d",strtotime($date)),$hospital_array,$target_id)){
        $week .= '<td>' . $day . $hospital;
    }else if(diary_icon(date("Y-m-d", strtotime($date)), $diary_array, $target_id) && hospital_icon(date("Y-m-d", strtotime($date)), $hospital_array, $target_id)) {
        $week .= '<td>' . $day . $diary . $hospital;
    } else {
        $week .= '<td>' . $day;
    }
    $week .= '</td>';

    // 週終わり、または、月終わりの場合
    if ($youbi % 7 == 6 || $day == $day_count) {

        if ($day == $day_count) {
            // 月の最終日の場合、空セルを追加
            // 例）最終日が水曜日の場合、木・金・土曜日の空セルを追加
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
        }

        // weeks配列にtrと$weekを追加する
        $weeks[] = '<tr>' . $week . '</tr>';

        // weekをリセット
        $week = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="main">
            <div class="title">
                <h2>カレンダー</h2>
            </div>
            <div class="box">
                <h3>
                    <a href="?ym=<?=$prev?>&id=<?=$target_id?>"><i class="fa-solid fa-circle-chevron-left"></i></a> 
                    <?=$html_title?> 
                    <a href="?ym=<?=$next?>&id=<?=$target_id?>"><i class="fa-solid fa-circle-chevron-right"></i></a>
                </h3>
                <table class="calendar">
                    <tr>
                        <th>日</th>
                        <th>月</th>
                        <th>火</th>
                        <th>水</th>
                        <th>木</th>
                        <th>金</th>
                        <th>土</th>
                    </tr>
                    <?php
                        foreach ($weeks as $week) {
                            echo $week;
                        }
                    ?>
                </table>
            </div> 
            <div class="box add">
                <a href="diary.php?id=<?=$target_id?>"><p><i class="fa-solid fa-pen-to-square"></i> 日記をつける</p></a>
            </div>
            <div class="box add">
                <a href="hospital.php?id=<?=$target_id?>"><p><i class="fa-solid fa-pen-to-square"></i> 通院記録をつける</p></a>
            </div>
        </div>
        <?php include("menu.php"); ?>
    </div>
</body>
</html>