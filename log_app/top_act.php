<?php
session_start();
if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
    echo "LOGIN Error!";
    exit();
}

$user_id=$_SESSION["id"];
$target_id=$_POST["tabId"];

//1.DBに接続する（エラー処理追加）
include("funcs.php");
$pdo = db_conn();

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

echo $view;
?>
