<?php
//入力チェック（受信確認処理追加）
if(
    !isset($_POST["name"]) || $_POST["name"]=="" ||
    !isset($_POST["gender"]) || $_POST["gender"]=="" ||
    !isset($_POST["birth"]) || $_POST["birth"]=="" ||
    !isset($_POST["type"]) ||
    !isset($_FILES["photo"]["name"])
){
    exit('ParamError');
}

//1.POSTデータ取得
$name=$_POST["name"];
$gender=$_POST["gender"];
$birth=$_POST["birth"];
$type=$_POST["type"];
$photo=$_FILES["photo"]["name"];

//1-2. FileUPLOAD
$upload="img/";
if(move_uploaded_file($_FILES['photo']['tmp_name'],$upload.$photo)){

}else{
    echo "uapload failed";
    echo $_FILES['photo']['error'];
}

//2.DBに接続する（エラー処理追加）*DB接続時はこれをまるっとセットで書けばOK!必要なとこだけ変更してね。
include("funcs.php");
$pdo = db_conn();

//3.データ登録SQL作成
$sql = "INSERT INTO target_table(id, name, gender, birth, type, photo, indate )
VALUES(NULL, :name, :gender, :birth, :type, :photo, sysdate())";

$stmt=$pdo->prepare($sql);

$stmt->bindValue(':name',$name,PDO::PARAM_STR);
$stmt->bindValue(':gender',$gender,PDO::PARAM_STR);
$stmt->bindValue(':birth',$birth,PDO::PARAM_STR);
$stmt->bindValue(':type',$type,PDO::PARAM_STR);
$stmt->bindValue(':photo',$photo,PDO::PARAM_STR);
$status=$stmt->execute();

//4.データ等力処理後 *書き換えることほぼない。そのまま使っていいよ。
if($status==false){
    //SQL実行時にエラーがある場合
    $erro = $stmt->errorINfo();
    exit("QueryError:".$error[2]);
}else{
    //5.index.phpへリダイレクト
    header("Location: select.php");
    exit;
}

?>
