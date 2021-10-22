<?php
session_start();

if (empty($_SESSION['login'])){
    $_SESSION['error2'] = 'ログインしてください';
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['error'])) unset($_SESSION['error']);

//⑤データベースへ接続し、接続情報を変数に保存する
//⑥データベースで使用する文字コードを「UTF8」にする
$db_name = 'zaiko2021_yse';
$db_host = 'localhost';
$db_port = '3306';
$db_user = 'zaiko2021_yse';
$db_password = '2021zaiko';
$dsn = "mysql:dbname={$db_name};host={$db_host};charset=utf8;port={$db_port}";
try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    echo "接続失敗: " . $e->getMessage();
    exit;
}

//⑦書籍テーブルから書籍情報を取得するSQLを実行する。また実行結果を変数に保存する
$books = getBooks($pdo);

function getBooks($pdo, $limit = 20, $offset = 0)
{
    // $sql = "SELECT * FROM books LIMIT {$limit}";
    $sql = "SELECT * FROM books";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    // return $stmt;
    $books = [];
    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $books[] = $book;
    }
    return $books;
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>書籍一覧</title>
    <link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
    <div id="header">
        <h1>書籍一覧</h1>
    </div>
    <form action="zaiko_ichiran.php" method="post" id="myform" name="myform">
        <div id="pagebody">
            <!-- エラーメッセージ表示 -->
            <div id="error">
                <?php
                /*
				 * ⑧SESSIONの「success」にメッセージが設定されているかを判定する。
				 * 設定されていた場合はif文の中に入る。
				 */
                // if(/* ⑧の処理を書く */){
                // 	//⑨SESSIONの「success」の中身を表示する。
                // }
                ?>
            </div>

            <!-- 左メニュー -->
            <div id="left">
                <p id="ninsyou_ippan">
                    <?php
                    echo @$_SESSION["account_name"];
                    ?><br>
                    <button type="button" id="logout" onclick="location.href='logout.php'">ログアウト</button>
                </p>
                <button type="submit" id="btn1" formmethod="POST" name="decision" value="3" formaction="nyuka.php">入荷</button>

                <button type="submit" id="btn1" formmethod="POST" name="decision" value="4" formaction="syukka.php">出荷</button>
            </div>
            <!-- 中央表示 -->
            <div id="center">

                <!-- 書籍一覧の表示 -->
                <table>
                    <thead>
                        <tr>
                            <th id="check"></th>
                            <th id="id">ID</th>
                            <th id="book_name">書籍名</th>
                            <th id="author">著者名</th>
                            <th id="salesDate">発売日</th>
                            <th id="itemPrice">金額</th>
                            <th id="stock">在庫数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book) : ?>
                            <?php extract($book); ?>
                            <tr id="book">
                                <td id="check"><input type="checkbox" name="books[]" value="<?= $book['id'] ?>"></td>
                                <td id="id"><?= $id ?></td>
                                <td id="title"><?= $title ?></td>
                                <td id="author"><?= $author ?></td>
                                <td id="date"><?= $salesDate ?></td>
                                <td id="price"><?= $price ?></td>
                                <td id="stock"><?= $stock ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <div id="footer">
        <footer>株式会社アクロイト</footer>
    </div>
</body>

</html>