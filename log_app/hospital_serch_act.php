<?php
if (!function_exists('db_conn')) {
    include("funcs.php");
}
// sschk();

//データ受け取り
$keyword=$_GET["keyword"] ?? '';
$category=$_GET["category"] ?? '';
$start_date=$_GET["start_date"] ?? '';
$end_date=$_GET["end_date"] ?? '';
$photo = $_GET['s_topic'];
$target_id=$_GET["target_id"];

//db接続
$pdo = db_conn();

$sql = "SELECT * FROM hospital_table WHERE target_id = :target_id";


//SQLクエリを実行して検索結果を取得
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':keyword',$keyword,PDO::PARAM_STR);
$stmt->bindValue(':category',$category,PDO::PARAM_STR);
$stmt->bindValue(':category',$category,PDO::PARAM_STR);
$stmt->bindValue(':target_id',$target_id,PDO::PARAM_STR);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

//検索結果を表示
$view = '';
foreach ($results as $result) {
    // 検索結果の表示を行う処理を記述
    $view .= "<div>";
    $view .= "<div>".$result['title']."</div>";
    $view .= "<div>".$result['date']."</div>";
    $view .= "<div>".$result['category']."</div>";
    $view .= "<div>".$result['memo']."</div>";
    $view .= "<div>".$result['photo']."</div>";
    $view .= "</div>";
}

echo $view;

?>