<?php
//データを取得
// $item_id=$_GET["item_id"];
// $s_month = $_POST['selected_month'];

//データベース接続
include("funcs.php");
$pdo = db_conn();

//データ取得
// $sql="SELECT value,date FROM log_table WHERE item_id=$item_id  AND MONTH(date) =$s_month  ORDER BY date";
$sql="SELECT value,date FROM log_table WHERE item_id=6  AND MONTH(date) =5  ORDER BY date";
$stmt = $pdo->prepare($sql);
// $stmt->bindValue(':s_month',$s_month,PDO::PARAM_INT);
// $stmt->bindValue(':item_id',$item_id,PDO::PARAM_INT);
$status = $stmt->execute();

//配列を入れる変数をリセット
$valu_ar = array();
$date_ar = array();

//配列に保存
if ($status) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $value_ar[] = $row['value'];
        $date_ar[] = date('m/d',strtotime($row['date']));
    }
} else {
    exit('DbExecuteError');
}

// var_dump($date_ar);

?>
<!DOCTYPE html>
<html lang="en">
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
                        $month = date('Y-m', strtotime("-$i months"));
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

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('chart');
  
    new Chart(ctx, {
        type: 'line',
        data: {
        labels: <?=json_encode($date_ar)?>,
        datasets: [{
            data: <?=json_encode($value_ar)?>,
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
                    display: false // ラベルを非表示にする
                }
            }
        }
        
    
    });
  </script>
  
</body>
</html>