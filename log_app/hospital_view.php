<?php
session_start();
include("funcs.php");
sschk();

//0.GETでid値を取得
$target_id = $_GET["id"];
$date = $_GET["date"];

//1.DBに接続する
$pdo = db_conn();

//2.データ取得SQL
$sql = "SELECT * FROM hospital_table WHERE target_id=:target_id AND DATE(date)=:date";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':target_id',$target_id,PDO::PARAM_INT);
$stmt->bindValue(':date',$date,PDO::PARAM_STR);
$status = $stmt->execute();

//3.データ表示
$view="";
if($status==false){
    //SQL実行時にエラーがある場合
    $error = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    while($result=$stmt->fetch(PDO::FETCH_ASSOC)){
        $view .= '<div class="diary">';
        $view .= '<div class="log_title">'.$result["title"].'</div>';
        $view .= '<div class="log_txt">';
        $view .= '<div>'.$result["category"].' / '.$result["date"].'</div>';
        $view .= '<div>'.$result["memo"].'</div>';
        $view .= '<div class="trash" data-log_id="'.$result["id"].'">'.'<i class="fa-solid fa-trash-can"></i>'.'</div>';
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
            <div class="box">
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