<?php
//入力確認
if (
    !isset($_POST["date"]) || $_POST["date"] == "" ||
    !isset($_POST["item_id"]) || $_POST["item_id"] == "" ||
    (!isset($_POST["value"]) && !isset($_POST["checkbox"])) ||
    (isset($_POST["value"]) && isset($_POST["checkbox"]))
) {
    exit('ParamError');
}

//1.POSTデータ取得
$date=$_POST["date"];
$value=$_POST["value"];
$checkbox=$_POST["checkbox"];
$memo=$_POST["memo"];
$item_id=$_POST["item_id"];


//2.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
try{
    $pdo=new PDO('mysql:dbname=cat_db;charaset=utf8;host=localhost','root','');//host名,ID,パスワード
}catch(PDOException $e){
    exit('DbConnectError:'.$e->getMessage());
}

//3.データ登録SQL作成  
$sql = "INSERT INTO log_table(id, item_id, value, checkbox, memo, date )
VALUES(NULL, :item_id, :value, :checkbox, :memo, :date)";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':item_id',$item_id,PDO::PARAM_INT);
$stmt->bindValue(':value',$value,PDO::PARAM_STR);
$stmt->bindValue(':checkbox',$checkbox,PDO::PARAM_INT);
$stmt->bindValue(':memo',$memo,PDO::PARAM_STR);
$stmt->bindValue(':date',$date,PDO::PARAM_STR);

$status=$stmt->execute();

//4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorInfo();
    exit("QueryError:".$error[2]);
}else{
    //5.index.phpへリダイレクト
    header("Location: top.php?id=1");
    exit;
}

?>
