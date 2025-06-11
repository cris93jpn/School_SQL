
<?php

//データベース接続情報を格納
include("config.php");
    //Ｐ1⃣----------------------------------------//
            //ページング機能（その１）
            $req_page = $_GET['page'];  //ページ数を取得
            if($req_page == 0 || $req_page == ""){
                $req_page = 1;
            }
            $page_count = 1;   //１ページあたりの件数
    //----------------------------------------//

$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //POSTデータの取得
    $hid_news_no = $_POST['hid_news_no']; //削除される対象のseq_no

    if($hid_news_no !== ""){

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
    //Ｐ2⃣----------------------------------------//
    //ページング機能として総件数を取得
    $sql = "select"
            ." Count(nt.news_no) as count"
            ." from news_v1_tbl nt"
            ." left join news_category_v1_mst cm"
            ." on nt.category_no = cm.category_no"
            ." where nt.delete_type = '0'"
            ." and cm.delete_type = '0';";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    $count = $result['count'];
    //----------------------------------------//
    
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
            ." order by nt.update_at desc"
            //Ｐ3⃣----------------------------------------//
            ." LIMIT :page_count OFFSET :page_start;";   //ページング機能（範囲を取得）→関数：LIMIT (取得したい行数) OFFSET (開始位置);
             //----------------------------------------//
    $stmt = $dbh->prepare($sql);
    //Ｐ4⃣----------------------------------------//
    $stmt->bindValue(':page_count', $page_count, PDO::PARAM_INT);
    $stmt->bindValue(':page_start', $page_count * ($req_page - 1), PDO::PARAM_INT);
    //----------------------------------------//
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $data[] = $row;
    }

}catch (PDOException $e){
    echo($e->getMessage());
    die();
}

    //Ｐ5⃣----------------------------------------//
   //ページング機能（その２）
   $max_page = ceil($count / $page_count);   //ceil割り算切り上げ（総データ数÷１ページあたりの件数）

   if($req_page == 1){
       $prev_page = 1;     //前
   } else {
       $prev_page = $req_page - 1;     //前
   }
   if($req_page == $max_page){
       $next_page = $req_page;     //次
   } else {
       $next_page = $req_page + 1;     //次
   }
   //----------------------------------------//
?>
<html>
    <hed>
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
    </head>
<body>
    <div style="text-align: center;margin-top:20px;margin-bottom:20px;">ニュース管理</div>
    <div style="margin-top:10px;margin-bottom:10px;">
        <a href="dataAdd.php">新規登録</a>｜<a href="menu.php">メニューへ戻る</a>
    </div>
    <!-- //Ｐ6⃣----------------------------------------// -->
    <div style="text-align:center;margin-bottom:20px;">
        <div><?php echo "現ページ：".$req_page."　／　総ページ：".$max_page; ?></div>
        <?php echo"<a href='?page=$prev_page'>前のページ</a>" ?>
        ｜
        <?php echo"<a href='?page=$next_page'>次のページ</a>" ?>
    </div>
    <!-- //----------------------------------------// -->
    <form name="fr01" action="" method="POST">
        <input type="hidden" id="hid_news_no" name="hid_news_no" value="">
        <table border="1" style="font-size:11px;" width="100%">
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