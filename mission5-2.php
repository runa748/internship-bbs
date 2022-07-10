<!DOCTYPE html>
<html lang=jp>
    <head>
        <meta charset="utf-8">
        <title>M5_1</title>
    </head>
    <body>
        <form action="" method="post">
            
            <?php
                $dsn = 'データベース名';
                $user = 'ユーザ名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                
                $sql = "CREATE TABLE IF NOT EXISTS MESSAGE_TB("
                    . "posted_number INT PRIMARY KEY,"
                    . "name varchar(255) NOT NULL,"
                    . "comment TEXT NOT NULL,"
                    . "date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,"
                    . "password char(255)"
                    .");";
                $stmt = $pdo->query($sql);
                
                global $flg;
                global $pass;
                
                define('INSERT_POST',0);
                define('UPDATE_POST',1);
                
                // 編集指定
                if (isset($_POST['edit'])) {
                    $enum = htmlspecialchars($_POST['enum']); // 編集番号
                    $pass = htmlspecialchars($_POST["epass"]);
                    
                    $sql = 'SELECT posted_number,name,comment,password FROM MESSAGE_TB';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    
                    $_enum = "";
                    $_name = "山田太郎";
                    $_text = "こんにちは";
                    
                    foreach ($results as $row){
                        if(empty($pass)){
                        }else if($pass == $row["password"]){
                            if ($row["posted_number"] == $enum) {
                                $_enum = $row["posted_number"];
                                $_name = $row["name"];
                                $_text = $row["comment"];
                            }
                        }
                    }
                }else{
                    $_enum = "";
                    $_name = "山田太郎";
                    $_text = "こんにちは";
                }
            ?>
            
            <input type = "hidden" name = "ednum" value = "<?php print $_enum; ?>"><br>
            
            <h3>入力ホーム</h3>
            <label>Username:</label><br>
            <input type = "text" name = "name" value = "<?php print $_name; ?>"required><br>
            <label>Comment:</label><br>
            <input text = "text" name = "text" value = "<?php print $_text; ?>"required><br>
            <label>PassWord:</label><br>
            <input type = "password" name = "pass" minlength="8">
            <input type = "submit" name = "submit"><br>
            <h3>削除番号指定用ホーム</h3>
            <label>DeleteNo:</label><br>
            <input type = "number" name = "num" value = "1" required><br>
            <label>PassWord:</label><br>
            <input type = "password" name = "dpass" minlength="8">
            <input type = "submit" name = "delete" value = "削除"><br>
            <h3>編集番号指定用ホーム</h3>
            <label>UpdateNo:</label><br>
            <input type = "number" name = "enum" value = "1" required><br>
            <label>PassWord:</label><br>
            <input type = "password" name = "epass" minlength="8">
            <input type = "submit" name = "edit" value = "編集"><br><br>
        </form>
        
        
        <?php
            $dsn = 'データベース名';
            $user = 'ユーザ名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            $sql = "CREATE TABLE IF NOT EXISTS MESSAGE_TB("
                . "posted_number INT PRIMARY KEY,"
                . "name varchar(255) NOT NULL,"
                . "comment TEXT NOT NULL,"
                . "date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,"
                . "password char(255)"
                .");";
            $stmt = $pdo->query($sql);
            
            $flg = INSERT_POST; // UPDATE_POST:編集モード、INSERT_POST:新規投稿
            
            // 削除
            if (isset($_POST['delete'])) {
                $dnum = $_POST['num'];
                $pass = htmlspecialchars($_POST["dpass"]);
                
                $sql = 'SELECT posted_number,name,comment,date,password FROM MESSAGE_TB';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                
                foreach ($results as $row){
                    if(empty($pass)){
                    }else if($pass == $row["password"]){
                        if ($row["posted_number"] == $dnum) {
                            $pnum = $row["posted_number"];
                            $sql = "DELETE FROM MESSAGE_TB WHERE posted_number = :pnum";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':pnum', $pnum, PDO::PARAM_INT);
                            $stmt->execute();
                        }
                    }
                }
            }
            // 投稿
            if (isset($_POST['submit'])) {
                // 編集判定
                if (isset($_POST['ednum'])) {
                    $ednum = htmlspecialchars($_POST['ednum']); // 編集番号
                    // 編集エリアに値が入力されていたらフラグを編集1にする
                    if ($ednum != "") {
                        $flg = UPDATE_POST;
                    }
                }
                
                $name = $_POST['name'];
                $text = $_POST['text'];
                $pass = htmlspecialchars($_POST["pass"]);
                
                $sql = "SELECT * FROM MESSAGE_TB "
                    ."ORDER BY posted_number DESC "
                    ."LIMIT 1;";
                $stmt = $pdo->query($sql);
                $row = $stmt->fetch();
                    
                // 追加
                if ($flg == INSERT_POST) {
                    if (is_numeric($row["posted_number"])) {
                        $num = (int)$row["posted_number"] + 1;
                    } else {
                        $num = 1;
                    }
                    
                    $sql = $pdo -> prepare("INSERT INTO MESSAGE_TB (posted_number,name,comment,password) VALUES (:pnum,:name,:comment,:pass)");
                    $sql -> bindParam(':pnum', $num, PDO::PARAM_INT);
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                    $name = $_POST['name'];
                    $comment = $_POST['text'];
                    $pass = $_POST['pass'];
                    $sql -> execute();
                    
                // 編集
                } else {
                    $sql = 'SELECT posted_number,name,comment,date,password FROM MESSAGE_TB';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    
                    foreach ($results as $row){
                        if($row["posted_number"] == $ednum) {
                            $name = $_POST['name'];
                            $comment = $_POST['text'];
                            $sql = "UPDATE MESSAGE_TB SET name = :name,comment = :comment WHERE posted_number = :ednum";
                            $stmt = $pdo->prepare($sql);
                            $stmt -> bindParam(':ednum', $ednum, PDO::PARAM_INT);
                            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                            $stmt -> execute();
                        }
                    }
                }
            }
            
            $sql = 'SELECT * FROM MESSAGE_TB';
            $stmt = $pdo -> query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                echo $row['posted_number'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
                echo "<hr>";
            }
        
        ?>
    </body>
</html>