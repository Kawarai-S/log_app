{/* 削除ポップアップ処理 */}
{/* ごみ箱クリックで発火 */}
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