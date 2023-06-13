<?php
session_start();
include("funcs.php");
sschk();

//0.GETでid値を取得
$item_id = $_GET["id"];
$target_id = $_GET["target_id"];

//1.DBに接続する
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
    $error = $stmt->errorINfo();
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
            <div class="log_view">
                <?=$view?>
            </div>        
        </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
//ごみ箱クリックで発火
$(document).ready(function() {
$(".trash").on("click",function(){
    const logId = $(this).data("log_id");
    Swal.fire({
            title: 'この記録を削除しますか？',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            //ウィンドウを閉じるとDismissReason（キャンセルボタン押した場合は"cancel")という値が返される
            if (result.dismiss !== Swal.DismissReason.cancel) { //キャンセルボタン以外がクリックされたら
                ajax(logId);
            } else { // キャンセルボタンがクリックされたら
            Swal.fire(
                'キャンセルしました',
                '投稿は削除されませんでした',
                'info'
            )
            }});
})});

function ajax(logId){
    $.ajax({
        type: "post", //HTTPメソッド
        url:"log_delete.php", //データの送信先
        data: { log_id:logId }, //送信するデータ
        dataType:"json" //レスポンスの型、種類
    })
    .done(function(data){
        if(data.status === "success"){
            Swal.fire({
                title: '削除しました',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(()=>{
                location.reload();//
            });
        }else{
            Swal.fire({
                title: '削除に失敗しました',
                icon: 'error',
                timer: 1500,
                showConfirmButton: false
            });
        }
        
    })
};


</script>

</body>
</html>