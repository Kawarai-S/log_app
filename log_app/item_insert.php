<?php
//入力チェック（受信確認処理追加）
if(
    !isset($_POST["item"]) || $_POST["item"]=="" ||
    !isset($_POST["icon"]) || $_POST["icon"]=="" ||
    !isset($_POST["type"]) || $_POST["type"]=="" ||
    !isset($_POST["unit"]) 
){
    exit('ParamError');
}

//1.POSTデータ取得
$item=$_POST["item"];
$icon=$_POST["icon"];
$type=$_POST["type"];
// $unit=$_POST["unit"];
$unit = isset($_POST["unit"]) ? $_POST["unit"] : null;


//2.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
try{
    $pdo=new PDO('mysql:dbname=cat_db;charaset=utf8;host=localhost','root','');//host名,ID,パスワード
}catch(PDOException $e){
    exit('DbConnectError:'.$e->getMessage());
}

//3.データ登録SQL作成  
$sql = "INSERT INTO item_table(id, item, icon, type, unit )VALUES(NULL, :item, :icon, :type, :unit)";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':item',$item,PDO::PARAM_STR);
$stmt->bindValue(':icon',$icon,PDO::PARAM_STR);
$stmt->bindValue(':type',$type,PDO::PARAM_STR);
$stmt->bindValue(':unit',$unit,PDO::PARAM_STR);

$status=$stmt->execute();

//4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    //5.index.phpへリダイレクト
    header("Location: top.php");
    exit;
}

?>
