<?php

//データベース接続情報を格納
include("config.php");

//変数初期宣言
$txt_category_name = "";

$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //POSTデータの取得
    $txt_category_name = $_POST['txt_category_name'];

    $err = "";

    if($txt_category_name == ""){
        $err .= "【カテゴリー名】";
    }

    if($err == ""){

        $sql = "insert into news_category_v1_mst("
                ." category_name"
                .",delete_type"
                .",insert_at"
                .",update_at"
                .") values ("
                .":category_name"
                .",'0'"
                .",now()"
                .",now()"
                .");";

        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':category_name', $txt_category_name, PDO::PARAM_STR);
            $stmt->execute();

            header("Location:categoryList.php");
        } catch(Exception $e){
            echo "データの書き込みに失敗しました。". $e->getMessage();
        }
    }else{
        echo $err."を修正してください。";
    }
}
?>

<html>
<body>

<div style="text-align: center;margin-top:20px;margin-bottom:20px;">データ登録</div>
<div style="margin-top:10px;margin-bottom:10px;"><a href="categoryList.php">一覧へ戻る</a></div>
    
<form action="" method="POST">
    カテゴリー名<br>
    <input type="text" name="txt_category_name" value="<?php echo $txt_category_name; ?>" /><br>
    
    <input type="submit" value="登録">
</form>
</body>
</html>
