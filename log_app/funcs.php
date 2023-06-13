<?php
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

// DB接続関数：db_conn()
function db_conn(){
    try {
        $db_name = "cat_db";    //データベース名
        $db_id   = "root";      //アカウント名
        $db_pw   = "";          //パスワード：XAMPPはパスワード無し or MAMPはパスワード”root”に修正してください。
        $db_host = "localhost"; //DBホスト
        return new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:'.$e->getMessage());
    }
}

// function db_conn(){
//     try {
//         $db_name = "khakiwombat30_cat_db";    //データベース名
//         $db_id   = "khakiwombat30";      //アカウント名
//         $db_pw   = "cat_db0517";          //パスワード：XAMPPはパスワード無し or MAMPはパスワード”root”に修正してください。
//         $db_host = "mysql57.khakiwombat30.sakura.ne.jp"; //DBホスト
//         return new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
//     } catch (PDOException $e) {
//         exit('DB Connection Error:'.$e->getMessage());
//     }
// }

//SQLエラー関数：sql_error($stmt)

function sql_error($stmt){
    $error = $stmt->errorInfo();
    exit("SQLError:".$error[2]);
}


//リダイレクト関数: redirect($file_name)

function redirect($page) {
    header("Location: ".$page);
    exit();
}

//SessionCheck(スケルトン)
function sschk()
{
    if (!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id()) {
        $timeout = 3;
        echo "Login Error";
        echo "<br>";
        echo $timeout."秒後にログイン画面にもどります";
        header("Refresh: $timeout; url=" . "entrance.php");
        exit();
    } else {
        session_regenerate_id(true);
        $_SESSION["chk_ssid"] = session_id();
    }
}

//fileUpload("送信名","アップロード先フォルダ");
function fileUpload($fname,$path){ //何を,どこに
    if (isset($_FILES[$fname] ) && $_FILES[$fname]["error"] ==0 ) {
        //ファイル名取得
        $file_name = $_FILES[$fname]["name"];
        //一時保存場所取得// ex)/home/tmt/1.jpg
        $tmp_path  = $_FILES[$fname]["tmp_name"];
        //拡張子取得// "jpg" "png"
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        //ユニークファイル名作成// 同じ名前のファイル名で上書きするのを避けるため。
        $file_name = date("YmdHis").md5(session_id()) . "." . $extension; //md5で固定のハッシュ化処理をしてる
        // FileUpload [--Start--]
        $file_dir_path = $path.$file_name; //"upload/...jpg"
        if ( is_uploaded_file( $tmp_path ) ) {
            if ( move_uploaded_file( $tmp_path, $file_dir_path ) ) { //一時保存場所から,どこへ
                chmod( $file_dir_path, 0644 );//0644は読み込み権限
                return $file_name; //成功時：ファイル名を返す
            } else {
                return 1; //失敗時：ファイル移動に失敗
            }
        }
     }else{
         return 2; //失敗時：ファイル取得エラー
     }
}

//fileUpload("送信名","アップロード先フォルダ"); *画像が任意の場合
function fileUpload2($fname,$path){ //何を,どこに
    if (isset($_FILES[$fname]) && $_FILES[$fname]["error"] == 0 && $_FILES[$fname]["size"] > 0) { //ファイルのサイズが0バイトより大きい場合のみ処理を行う
        //ファイル名取得
        $file_name = $_FILES[$fname]["name"];
        //一時保存場所取得// ex)/home/tmt/1.jpg
        $tmp_path  = $_FILES[$fname]["tmp_name"];
        //拡張子取得// "jpg" "png"
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        //ユニークファイル名作成// 同じ名前のファイル名で上書きするのを避けるため。
        $file_name = date("YmdHis").md5(session_id()) . "." . $extension; //md5で固定のハッシュ化処理をしてる
        // FileUpload [--Start--]
        $file_dir_path = $path.$file_name; //"upload/...jpg"
        if ( is_uploaded_file( $tmp_path ) ) {
            if ( move_uploaded_file( $tmp_path, $file_dir_path ) ) { //一時保存場所から,どこへ
                chmod( $file_dir_path, 0644 );//0644は読み込み権限
                return $file_name; //成功時：ファイル名を返す
            } else {
                return 1; //失敗時：ファイル移動に失敗
            }
        }
     }else{
        return NULL; // 画像ファイルが送信されていない場合はNULLを返す
     }
}