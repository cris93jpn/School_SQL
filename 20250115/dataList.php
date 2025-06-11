
<?php

//データベース接続情報を格納
include("config.php");

$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //POSTデータの取得
    $hid_news_no = $_POST['hid_news_no']; //削除される対象のseq_no

    if($hid_student_no !== ""){

        //UPDATE文（更新）
        $sql = "update news_v1_tbl set"
        ." delete_type = '1'"
        ." where news_no = :news_no;";

        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':news_no', $hid_news_no, PDO::PARAM_INT);
            $stmt->execute();
        }catch (PDOException $e){
            echo($e->getMessage());
            die();
        }
    }
}

try{
    
    $sql = "select"
            ." nt.news_no"
            .",nt.title"
            .",cm.category_name"
            .",nt.display_type"
            .",nt.update_at"
            ." from news_v1_tbl nt"
            ." left join news_category_v1_mst cm"
            ." on nt.category_no = cm.category_no"
            ." where nt.delete_type = '0'"
            ." and cm.delete_type = '0'"
            ." order by nt.update_at desc;";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $count = $stmt->rowCount();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $data[] = $row;
    }

}catch (PDOException $e){
    echo($e->getMessage());
    die();
}


?>
<html>
    <head>
        <script type="text/javascript">

        document.getElementById('hid_news_no').value = "";

        function MJ_UPD(argSEQ_NO){
            document.location.href="dataUpd.php?news_no="+argSEQ_NO;
        }
        function MJ_DEL(argSEQ_NO){
            document.getElementById('hid_news_no').value = argSEQ_NO;
            document.fr01.submit();
        }
        </script>
        <style>
            body {
                background-color: #D9EAFD;
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
            table {
                border: 2px solid #155E95;
                margin: 20px auto;
            }

            tr {
                background-color: #E8F9FF;
                height: 25px;
                text-align: center;
                border: 2px solid #155E95;
            }

            .btn-square {
                display: inline-block;
                padding: 0.5em 1em;
                text-decoration: none;
                background: #577BC1;/*ボタン色*/
                color: #FFF;
                border-bottom: solid 4px #627295;
                border-radius: 3px;
                margin:0 15px;
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
    <h2>ニュース管理</h2>
    <div style="margin-top:10px;margin-bottom:10px;">
        <a href="dataAdd.php" class="btn-square">新規登録</a>｜<a href="menu.php" class="btn-square">メニューへ戻る</a>
    </div>
    <form name="fr01" action="" method="POST">
        <input type="hidden" id="hid_news_no" name="hid_news_no" value="">
        <table style="font-size:11px;" width="100%">
            <tr>
                <th>NEWS_NO</th>
                <th>TITLE</th>
                <th>CATEGORY_NAME</th>
                <th>DISPLAY_TYPE</th>
                <th>UPDATE_AT</th>
                <th>機能</th>
            </tr>
            <?php foreach($data as $row): ?>
                <tr>
                <td><?php echo $row['news_no'];?></td>
                <td><?php echo $row['title'];?></td>
                <td><?php echo $row['category_name'];?></td>
                <td>
                    <?php
                        if($row['display_type'] === "0"){
                            echo "表示";
                        }else{
                            echo "非表示";
                        }
                    ?>
                </td>
                <td><?php echo $row['update_at'];?></td>
                <td>＜<a href="javascript:" onclick="MJ_UPD(<?php echo $row['news_no']; ?>);">更新</a>＞｜＜<a href="javascript:" onclick="MJ_DEL(<?php echo $row['news_no']; ?>);">削除</a>＞</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </form>
</body>
</html>