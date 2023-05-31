<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="main">
            <div class="box">
                <div>
                    <label for="month-select">年月選択：</label>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
//プルダウン変更で発火
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
        data: {"month-select":$("#month-select").val()}, //送信するデータ
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

// 最初の読み込み時に当月のデータを表示
ajax();
</script>
  
</body>
</html>