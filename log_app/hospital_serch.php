<?php
session_start();
if (!function_exists('db_conn')) {
    include("funcs.php");
}
sschk();

//データ受け取り
$target_id=$_GET["id"];

$keyword=$_GET["keyword"] ?? '';
$category=$_GET["category"] ?? '';
$start_date=$_GET["start_date"] ?? '';
$end_date=$_GET["end_date"] ?? '';
$photo =$_GET['photo'] ?? '';

if (empty($keyword) && empty($category) && empty($start_date) && empty($end_date) && empty($photo)) {
    $results = []; // 空の結果配列を作成
} else {
    //db接続
    $pdo = db_conn();

    //sqlを作成
    $sql = "SELECT * FROM hospital_table WHERE target_id = :target_id";

    //keyword検索
    if(!empty($keyword)){
        $sql .= " AND (title LIKE CONCAT('%',:keyword,'%') OR memo LIKE CONCAT('%',:keyword,'%'))";
    }

    //カテゴリ検索
    if(!empty($category)){
        $sql .=" AND category = :category";
    }

    //期間検索
    if(!empty($start_date)){
        $sql .= " AND date >= :start_date";
    }
    if(!empty($end_date)){
        $sql .= " AND date >= :end_date";
    }

    //写真有無
    if(!empty($photo)){
        if($photo === 'あり'){
            $sql .=" AND photo IS NOT NULL";
        }elseif($photo==='なし'){
            $sql .=" AND photo IS NULL";
        }
    }

    $sql .= " ORDER BY date DESC";

    //SQLクエリを実行して検索結果を取得
    $stmt = $pdo->prepare($sql);
    // echo "SQL: " . $sql . "\n";
    $stmt->bindValue(':target_id', $target_id, PDO::PARAM_STR);
    // echo "Binding :target_id with value: " . $target_id . "\n";
    if(!empty($keyword)){
        $stmt->bindValue(':keyword', $keyword, PDO::PARAM_STR);
        // echo "Binding :keyword with value: " . $keyword . "\n";
    }
    if(!empty($category)){
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        // echo "Binding :category with value: " . $category . "\n";
    }
    if(!empty($start_date)){
        $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
        // echo "Binding :start_date with value: " . $start_date . "\n";
    }
    if(!empty($end_date)){
        $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
        // echo "Binding :end_date with value: " . $end_date . "\n";
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //検索結果を表示
    $view = '';
    foreach ($results as $result) {
        // 検索結果の表示を行う処理を記述
        $view .= '<div class="h_log">';
        $view .= '<div>title: '.$result['title'].'</div>';
        $view .= '<div>'.$result['date'].'</div>';
        $view .= '<div>'.$result['category'].'</div>';
        $view .= '<div>'.$result['memo'].'</div>';
        if(!$result['photo']==NULL){
            $view .= '<div><img src="h_img/'.$result['photo'].'" width="300px"></div>';
        }
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
    <title>通院記録</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrap">
        <div class="main">

            <div class="title">
                <h2>通院記録</h2>
            </div>
            <div class="box add">
                <a href="hospital.php?id=<?=$target_id?>"><p><i class="fa-solid fa-pen-to-square"></i> 記録をつける</p></a>
            </div>
            <div class="box">    
                <form class="form" action="hospital_serch.php" method="GET">
                <div class="f_box">
                    <label for="keyword"><i class="fa-solid fa-key"></i> キーワード</label><br>
                    <input class="text_box" type="text" name="keyword">
                </div>
                <div class="f_box">
                    <label for="category"><i class="fa-solid fa-square-check"></i> カテゴリ</label><br>
                    <select  class="category" name="category">
                        <option value="">選択してください</option>
                        <option value="通常受診">通常受診</option>
                        <option value="ワクチン・予防接種">ワクチン・予防接種</option>
                        <option value="定期健診">定期健診</option>
                        <option value="検査">検査</option>
                        <option value="その他">その他</option>
                    </select>
                </div>
                <div class="f_box">
                    <label for="s_topic"><i class="fa-solid fa-calendar-day"></i> 期間</label><br>
                    <input class="date" type="date" name="start_date" placeholder="開始日"> 〜
                    <input class="date" type="date" name="end_date" placeholder="終了日">
                </div>
                <div class="f_box">
                    <label for="s_topic"><i class="fa-solid fa-image"></i> 写真</label>　
                    <input type="radio" name="photo" value="あり">あり
                    <input type="radio" name="photo" value="なし">なし
                </div>
                <div>
                <input type="hidden" name="id" value="<?=$target_id?>">
                    <input class="s_btn" type="submit" value="検索">
                </div>
                </form>
            </div>
            <div class="box">
                <!-- 検索結果の表示 -->
                <?php if (!empty($results)): ?>
                    <?=$view?>
                <?php endif; ?>
            </div>
        </div>
        <?php include("menu.php"); ?>
    </div>
</body>
</html>