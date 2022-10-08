<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){ 
         //データベース接続
        $dsn = 'databaseName';
        $user = "userName";
        $password = "passWord";
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //新規投稿
        if(empty($_POST["id"]) && $_POST["submit"] && !empty($_POST["name"]) && !empty($_POST["comment"] && !empty($_POST["passwordP"]))){
            //テーブルの作成
	        $sql = "CREATE TABLE IF NOT EXISTS thread"
	        ." ("
	        . "id INT AUTO_INCREMENT PRIMARY KEY,"
	        . "name char(32),"
	        . "comment TEXT,"
	        . "time TEXT,"
	        . "password TEXT"
	        .");";
	        $stmt = $pdo->query($sql);

            //受け取ったデータをDBに入れる。
            $sql = $pdo -> prepare("INSERT INTO thread (name, comment, time, password) VALUES (:name, :comment, :time, :password)");
    	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    	    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    	    $sql -> bindParam(':time', $time, PDO::PARAM_STR);
    	    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
    	    $name = $_POST["name"];
    	    $comment = $_POST["comment"]; 
    	    $time = date("Y年m月d日 H時i分s秒");
    	    $password = $_POST["passwordP"];
    	    $sql -> execute();
    	    $successPost = "投稿が完了しました。";
            header("Location: ".$_SERVER['PHP_SELF']); 
            //編集投稿
        }elseif(!empty($_POST["id"]) && $_POST["submit"] && !empty($_POST["name"]) && !empty($_POST["comment"] && !empty($_POST["passwordP"]))){
            $id = $_POST["id"];
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $password = $_POST["passwordP"];
            $sql = 'UPDATE thread SET name=:name,comment=:comment,password = :password WHERE id=:id';
            $stmt = $pdo -> prepare($sql);
            $stmt ->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt -> bindParam(":name", $name, PDO::PARAM_STR);
            $stmt -> bindParam(":comment", $comment, PDO::PARAM_STR);
            $stmt -> bindParam(":password", $password, PDO::PARAM_STR);
            $stmt -> execute();
        }
        
        //削除機能
        if($_POST["delete"] && !empty($_POST["deleteId"]) && !empty($_POST["passwordD"])){
            //削除IDのレコード取得する
            $deleteId = $_POST["deleteId"];
            $password = $_POST["passwordD"];
            $sql = "select * from thread where id = :id";
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(":id", $deleteId, PDO::PARAM_INT);
            $stmt ->execute();
            $result = $stmt -> fetchAll();
            //上記でとったレコードのパスワードを確認
            $record = $result[0];
            if($record["password"] == $password){ //レコードと削除IDが等しければ、削除する
                $sql = "delete from thread where id = :id";
                $stmt = $pdo -> prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $id = $deleteId;
                $stmt -> execute();
                echo "<h3>パスワードが一致したので指定された投稿を削除しました。</h3>";
                }else{
                    echo "<h3>パスワードが違います</h3>";
                }
        }
        //編集機能
        if($_POST["edit"] && !empty($_POST["editId"]) && !empty($_POST["passwordE"])){
            $editId = $_POST["editId"];
            $password = $_POST["passwordE"];
            $sql = "select * from thread where id = :id"; //編集番号のレコードを取得
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(':id', $editId, PDO::PARAM_INT);
            $stmt -> execute();
            $result = $stmt -> fetchAll();
            //上記でとったレコードのパスワードを確認する
            $record = $result[0];
            if($record["password"] == $password){//同じであれば編集をする
                echo "<h2>投稿フォームに編集したものを書き込んで送信してください</h2>";
                echo "<h3>パスワードが一致しました</h3><br>";
                $editId1 = $record["id"];
                $editName = $record["name"];
                $editComment = $record["comment"];
                $editPassword = $record["password"];
                
            }else{
                echo "<h3>パスワードが違います</h3>";
            }
        }
    }
    
    ?>
    <form action="" method = "post">
        <h1>掲示板</h1>
        <br>
        <input type = "hidden" name = "id" value = "<?php echo $editId1;?>">
        <h3>投稿</h3>
        名前<input type = "text" name = "name" value = "<?php echo $editName; ?>">
        <br>
        投稿内容<input type = "text" name = "comment" value = "<?php echo $editComment; ?>">
        <br>
        パスワード<input type = "password" name = "passwordP" value = "<?php echo $editPassword; ?>" >
        <br>
        <input type = "submit" name = "submit">
        
        <hr>
        
        <h3>削除申請</h3>
        削除番号<input type = "text" name = "deleteId"> 
        <br>
        パスワード<input type = "password" name = "passwordD">
        <br>
        <input type = "submit" name = "delete">
        
        <hr>
        <h3>編集申請</h3>
        投稿番号<input type = "text" name = "editId">
        <br>
        パスワード<input type = "password" name = "passwordE">
        <br>
        <input type = "submit" name ="edit">
    </form>
    <hr>
    <h3>投稿一覧</h3>
    <?php
    //投稿確認
    //データベース接続
        if(!empty($successPost)){
            echo $successPost."<br>";
        }
        $dsn = 'www';
        $user = "tb";
        $password = "password";
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "SELECT * FROM thread";
        $stmt = $pdo -> query($sql);
        $result = $stmt -> fetchAll();
        foreach($result as $row){
            echo $row["id"]." ";
            echo "名前 : ".$row["name"]." ";
            echo $row["time"];
            echo " パスワード:".$row["password"];//後で消す
            echo "<br>";
            echo $row["comment"];
            echo "<br>";
            echo "<br>";
        }
    ?>
</body>
