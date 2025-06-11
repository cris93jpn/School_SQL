
<?php

//データベース接続情報を格納
include("config.php");

//Ｐ1⃣----------------------------------------//
//ページング機能（その１）
$req_page = $_GET['page'];  //ページ数を取得
if($req_page == 0 || $req_page == ""){
    $req_page = 1;
}
$page_count = 3;   //１ページあたりの件数
//----------------------------------------//

//★Ｓ１　検索ボックスから値を受け取る----------------------------------------//
$txt_search = $_POST['txt_search']; //文字検索列
$sel_category_no = $_POST['sel_category_no']; //カテゴリー選択列
//----------------------------------------//

$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//★Ｓ２　セレクトボックスを生成する----------------------------------------//
//カテゴリー選択用のセレクトボックスを生成する
try{
    //一覧取得
    $sql = "select"
            ." cm.category_no"
            .",cm.category_name"
            ." from news_category_v1_mst cm"
            ." where cm.delete_type = '0'" //削除されていないデータのみ表示
            ." order by cm.category_no asc;";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $dataCategory[] = $row;
    }
}catch (PDOException $e){
    echo($e->getMessage());
    die();
}
//----------------------------------------//

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //POSTデータの取得
    $hid_category_no = $_POST['hid_category_no']; //削除される対象のseq_no

    if($hid_category_no !== ""){

        //UPDATE文（更新）
        $sql = "update news_category_v1_mst set"
        ." delete_type = '1'"
        ." where category_no = :category_no;";

        try{
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':category_no', $hid_category_no, PDO::PARAM_INT);
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
            ." Count(cm.category_no) as count"
            ." from news_category_v1_mst cm"
            ." where cm.delete_type = '0'";
            //★Ｓ３-１　検索データを抽出するSQL文の追加----------------------------------------//
            if($txt_search != ""){
                $sql .=" and cm.category_name like :txt_search";
            }
            if($sel_category_no != ""){
                $sql .= " and cm.category_no = :sel_category_no";
            }
            //----------------------------------------//
    $stmt = $dbh->prepare($sql);
    //★Ｓ４-１　検索データをＳＱＬ文に送るための記述----------------------------------------//
    if($txt_search != ""){$stmt->bindValue(':txt_search', "%".$txt_search."%", PDO::PARAM_STR);}
    if($sel_category_no != ""){$stmt->bindValue(':sel_category_no', $sel_category_no, PDO::PARAM_INT);}
    //----------------------------------------//
    $stmt->execute();
    $result = $stmt->fetch();
    $count = $result['count'];
    //----------------------------------------//

    $sql = "select"
            ." cm.category_no"
            .",cm.category_name"
            .",cm.update_at"
            ." from news_category_v1_mst cm"
            ." where cm.delete_type = '0'";
            //★Ｓ３-２　検索データを抽出するSQL文の追加----------------------------------------//
            if($txt_search != ""){
                $sql .=" and cm.category_name like :txt_search";
            }
            if($sel_category_no != ""){
                $sql .= " and cm.category_no = :sel_category_no";
            }
            //----------------------------------------//
            $sql .= " order by cm.category_no desc"
            //Ｐ3⃣----------------------------------------//
            ." LIMIT :page_count OFFSET :page_start;";   //ページング機能（範囲を取得）→関数：LIMIT (取得したい行数) OFFSET (開始位置);
            //----------------------------------------//
    $stmt = $dbh->prepare($sql);
    //Ｐ4⃣----------------------------------------//
    $stmt->bindValue(':page_count', $page_count, PDO::PARAM_INT);
    $stmt->bindValue(':page_start', $page_count * ($req_page - 1), PDO::PARAM_INT);
    //★Ｓ４-２　検索データをＳＱＬ文に送るための記述----------------------------------------//
    if($txt_search != ""){$stmt->bindValue(':txt_search', "%".$txt_search."%", PDO::PARAM_STR);}
    if($sel_category_no != ""){$stmt->bindValue(':sel_category_no', $sel_category_no, PDO::PARAM_INT);}
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
    <haed>
        <script type="text/javascript">

        document.getElementById('hid_category_no').value = "";

        function MJ_UPD(argSEQ_NO){
            document.location.href="categoryUpd.php?category_no="+argSEQ_NO;
        }
        function MJ_DEL(argSEQ_NO){
            document.getElementById('hid_category_no').value = argSEQ_NO;
            document.fr01.submit();
        }
        </script>
                <style>
            body {
                background-color: #E7F2F8;
                margin: auto;
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
                margin: 30px auto;
                border-collapse: collapse;
            }

            .search_table th{
                background-color: #5885AF;
                color: white;
                height: 40px;
                width: 180px;
            }

            .search_table td, tr {
                width: 300px;
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
                background: #6AABD2;/*ボタン色*/
                color: #FFF;
                border-bottom: solid 4px #385E72;
                border-radius: 3px;
                margin:0 15px;
            }
            .btn-square:active {
                /*ボタンを押したとき*/
                -webkit-transform: translateY(4px);
                transform: translateY(4px);/*下に動く*/
                border-bottom: none;/*線を消す*/
            }

            .sub_btn {
                background-color: #6AABD2;
                width: 100px;
                height: 30px;
                margin: 5px;
                color: #274472;
                font-weight: bold;
                border: 2px solid #274472;
                border-radius: 20px;
            }

        </style>
    </head>
<body>
    <h2>カテゴリー管理</h2>
    <div style="margin-top:10px;margin-bottom:10px;">
        <a href="categoryAdd.php" class="btn-square">新規登録</a>｜<a href="menu.php" class="btn-square">メニューへ戻る</a>
    </div>
    <!-- //★Ｓ５　検索ボックスエリア----------------------------------------// -->
    <form name="fr01" action="" method="POST">
        <table class="search_table">
            <tr>
                <th>文字検索</th>
                <td><input type="text" name="txt_search" value="<?php echo $txt_search ?>"></td>
            </tr>
            <tr>
                <th>カテゴリー</th>
                <td>
                    <select name="sel_category_no">
                        <option value="">選択してください</option>
                        <?php
                        foreach($dataCategory as $row):
                            $strSelected = "";
                            if($sel_category_no == $row['category_no']){$strSelected = " selected";}
                            echo "<option value=".$row['category_no'].$strSelected.">".$row['category_name']."</option>";
                        endforeach;
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;"><input type="submit" value="検索"></td>
            </tr>
        </table>
    </form>
    <!-- //----------------------------------------// -->


    <br><hr><br>
    <!-- //Ｐ6⃣----------------------------------------// -->
    <div style="text-align:center;margin-bottom:20px;">
        <div><?php echo "現ページ：".$req_page."　／　総ページ：".$max_page; ?></div>
        <?php echo"<a href='?page=$prev_page'>前のページ</a>" ?>
        ｜
        <?php echo"<a href='?page=$next_page'>次のページ</a>" ?>
    </div>
    <!-- //----------------------------------------// -->
    <form name="fr01" action="" method="POST">
        <input type="hidden" id="hid_category_no" name="hid_category_no" value="">
        <table border="1" style="font-size:11px;" width="100%">
            <tr>
                <th>CATEGORY_NO</th>
                <th>CATEGORY_NAME</th>
                <th>UPDATE_AT</th>
                <th>機能</th>
            </tr>
            <?php foreach($data as $row): ?>
                <tr>
                <td><?php echo $row['category_no'];?></td>
                <td><?php echo $row['category_name'];?></td>
                <td><?php echo $row['update_at'];?></td>
                <td>＜<a href="javascript:" onclick="MJ_UPD(<?php echo $row['category_no']; ?>);">更新</a>＞｜＜<a href="javascript:" onclick="MJ_DEL(<?php echo $row['category_no']; ?>);">削除</a>＞</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </form>
</body>
</html>