<?php

//データベース接続情報を格納
include("config.php");

//変数初期宣言
$txt_category_name = "";

if($_SERVER['REQUEST_METHOD'] === 'GET'){

    //GETデータの取得
    $req_category_no = $_GET['category_no'];

    if($req_category_no == ""){
        header('Location: categoryList.php');
        exit();
    }
}

$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //POSTデータの取得
    $req_category_no = $_POST['hid_category_no'];
    $txt_category_name = $_POST['txt_category_name'];
    $err = "";

    if($txt_category_name == ""){
        $err .= "【カテゴリー名】";
    }

    if($err == ""){

        $sql = "update news_category_v1_mst set "
                ." category_name = :category_name"
                .",update_at = now()"
                ." where category_no = :category_no;";
        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':category_no', $req_category_no , PDO::PARAM_INT);
            $stmt->bindValue(':category_name', $txt_category_name, PDO::PARAM_STR);
            $stmt->execute();
            header("Location:categoryList.php");
            exit();
        } catch(Exception $e){
            echo($e->getMessage());
            die();
        }
    }else{
        echo $err."を修正してください。";
    }
} else {

    try{
    
        $sql = "select"
                ." cm.category_name"
                ." from news_category_v1_mst cm"                
                ." where cm.category_no = :category_no";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':category_no', $req_category_no, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        //POSTデータの取得
        $txt_category_name = $result['category_name'];
        
    }catch (PDOException $e){
        echo($e->getMessage());
        die();
    }
}


?>

<html>
<head>
        <style>
            body {
                background-color: #D9EAFD;
                margin: 20px;
            }

            h2 {
                text-align: center;
                margin: 20px;
                color: #2E5077;
            }
            a {
                list-style-type: none;
                text-decoration: none;
                color: #155E95;
            }
            .btn-square {
                display: inline-block;
                padding: 0.5em 1em;
                text-decoration: none;
                background: #577BC1;/*ボタン色*/
                color: #FFF;
                border-bottom: solid 4px #627295;
                border-radius: 3px;
                margin: 10px 0;
            }
            .btn-square:active {
                /*ボタンを押したとき*/
                -webkit-transform: translateY(4px);
                transform: translateY(4px);/*下に動く*/
                border-bottom: none;/*線を消す*/
            }
        </style>
    </head>
<body>

<div class="btn-square">データ更新</div>
<div class="btn-square"><a href="categoryList.php">一覧へ戻る</a></div>
    
<form action="" method="POST">
    <input type="hidden" name="hid_category_no" value="<?php echo $req_news_no; ?>" />
    カテゴリー名<br>
    <input type="text" name="txt_category_name" value="<?php echo $txt_category_name; ?>" /><br>

    <input type="submit" value="更新">
</form>
</body>
</html>