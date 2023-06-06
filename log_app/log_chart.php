<?php
session_start();
if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
    echo "LOGIN Error!";
    exit();
}

//0.GETでid値を取得
$item_id = $_GET["id"];
$target_id = $_GET["target_id"];

//1.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
include("funcs.php");
$pdo = db_conn();

//2.データ取得SQL
$sql = "SELECT log_table.id as log_id, item_table.id as item_id, 
        DATE_FORMAT(log_table.date, '%Y-%m-%d %H:%i') as f_date, value, checkbox, memo, unit 
        FROM log_table 
        LEFT JOIN item_table 
        ON log_table.item_id = item_table.id 
        WHERE item_table.id=:item_id ORDER BY log_table.date DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':item_id',$item_id,PDO::PARAM_INT);
$status = $stmt->execute();

//3.データ表示
$view="";
if($status==false){
    //SQL実行時にエラーがある場合
    $error = $stmt->errorInfo();
    exit("QueryError:".$error[2]);
}else{
    while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
        $view .= '<div class="box">';
        $view .= '<div class="log">';
        $view .= '<a href="log_update_view.php?id='.$result["log_id"].'">';
        $view .= '<div>'.$result["f_date"].'</div>';
        if(!is_null($result["checkbox"])){
            $view .= '<div><img src="icon/check.png" style="width:24px; height:24px;"></div>';
        }elseif(!is_null($result["value"])){
            $view .= '<div>'.$result["value"].$result["unit"].'</div>';
        }
        $view .= '</a>';
        $view .= '<div class="trash" data-log_id="'.$result["log_id"].'">'.'<i class="fa-solid fa-trash-can"></i>'.'</div>';
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
    <title>LOG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.0.0/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.0.0/dist/sweetalert2.min.css">
</head>
<body>
    <div class="wrap">
        <div class="main">
            <div class="box icon">
                <div class="add_icon">
                    <a href="top.php?id=<?= h($target_id) ?>">
                        <img src="icon/back.png" style="width:24px; height:24px">
                    </a>
                </div>    
            </div>
            <!-- chart -->
            <div class="box">
                <div>
                    <!-- <label for="month-select">年月選択：</label> -->
                    <select id="month-select">
                        <?php
                        // 過去12ヶ月分の年月を生成してセレクトボックスに追加
                        for ($i = 0; $i < 12; $i++) {
                            $month = date('Y-m', strtotime(date("Y-m-01") . " -$i months"));
                            $selected = ($i === 0) ? 'selected' : ''; // 最初は現在の月を選択状態にする
                            echo "<option value=\"$month\" $selected>$month</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <canvas id="chart" width="400" height="200"></canvas>
                </div>
            </div>
            <!-- LOG -->
            <div class="log_view">
                <?=$view?>
            </div>        
        </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/alert.js"></script>
<script>
//  chart作成
// プルダウン変更で発火
$(document).ready(function() {
$("#month-select").on("change",function(){
    destroyChart(); // グラフを破棄する
    ajax();
})});

// グラフを破棄する関数
function destroyChart() {
    const ctx = document.getElementById('chart');
    const chart = Chart.getChart(ctx);
    if (chart) {
        chart.destroy();
    }
}

// ajaxでphpへプルダウンのvalueを送信
function ajax(){
    $.ajax({
        type: "post", //HTTPメソッド
        url:"chart_back.php", //データの送信先
        data: { //送信するデータ
            "month-select":$("#month-select").val(),
            "item_id": <?=$item_id?>
        }, 
        dataType:"json" //レスポンスの型、種類
    })
    .done(function(data){
        createChart(data); //チャートを作成
    })
};

// グラフを作成する関数
function createChart(data) {
    const ctx = document.getElementById('chart');
    // 年月日と時間から月日のみを抽出して新しい配列を作成
    const labels = data.map(item => item.date.substr(5, 5));

    new Chart(ctx, {
        type: 'line',
        data: {
        labels: labels,
        datasets: [{
            data: data.map(item => item.value),
            borderWidth: 1
        }]
        },
        options: {
        scales: {
            y: {
            beginAtZero: true
            }
        },
        plugins: {
            legend: {
            display: false
            }
        }
    }})
}

//最初の読み込み時に当月のデータを表示
ajax();
</script>
</body>
</html>