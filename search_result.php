<?php
require_once 'Book.php';

session_start();

if (empty($_SESSION['login'])) {
    $_SESSION['error2'] = 'ログインしてください';
    header('Location: login.php');
    exit;
}

function check($values)
{
    foreach ($values as $index => $value) {
        $values[$index] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    return $values;
}


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


$searchs = check($_GET);

function getBooks($pdo, $searchs)
{
    $conditions = [];

    //keyword
    if (!empty($_GET['keyword'])) {
        $conditions[] = "(title LIKE '%{$searchs['keyword']}%' OR author LIKE '%{$searchs['keyword']}%')";
    }
    //year
    if (is_numeric($_GET['year'])) {
        $start_year = $_GET['year'];
        $end_year = $start_year + 10;
        $conditions[] = "salesDate >= '{$start_year}年1月1日'";
        $conditions[] = "salesDate < '{$end_year}年1月1日'";
    }
    //price
    if (is_numeric($_GET['price'])) {
        $start_price = $_GET['price'];
        $end_price = ($start_price >= 1000) ? $start_price + 1000 : $start_price + 100;
        $conditions[] = " price >= {$start_price}";
        $conditions[] = " price < {$end_price}";
    }

    $sql = "SELECT * FROM books";
    if ($conditions) {
        $condition = implode(' AND ', $conditions);
        $sql .= " WHERE {$condition}";
    }

    var_dump($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
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
        <h1>商品検索</h1>
    </div>
    <form action="zaiko_ichiran.php" method="post" id="myform" name="myform">
        <div id="pagebody">
            <div id="menu">
                <nav>
                    <ul>
                        <li><a href="zaiko_ichiran.php?page=0">書籍一覧</a></li>
                    </ul>
                </nav>
            </div>
            <!-- エラーメッセージ表示 -->
            <div id="error">
                <?= @$error_message ?>
            </div>

            <!-- 左メニュー -->
            <div id="left">
                <p id="ninsyou_ippan">
                    <?= @$_SESSION["account_name"] ?>
                    <br>
                    <button type="button" id="logout" onclick="location.href='logout.php'">ログアウト</button>
                </p>
                <button type="submit" id="btn1" formmethod="POST" name="decision" value="3" formaction="nyuka.php">入荷</button>

                <button type="submit" id="btn1" formmethod="POST" name="decision" value="4" formaction="syukka.php">出荷</button>

                <button type="submit" id="btn1" formmethod="POST" name="decision" value="3" formaction="new_product.php">新商品追加</button>

                <button type="submit" id="btn1" formmethod="POST" name="decision" value="4" formaction="delete_product.php">商品削除</button>

                <button type="submit" id="btn1" formmethod="POST" name="decision" value="5" formaction="product_search.php">商品検索</button>
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
                        <?php foreach (getBooks($pdo, $searchs) as $book) : ?>
                            <?php extract($book); ?>
                            <tr>
                                <td><input type="checkbox" name="books[]" value="<?= $book['id'] ?>"></td>
                                <td><?= $id ?></td>
                                <td><?= $title ?></td>
                                <td><?= $author ?></td>
                                <td><?= $salesDate ?></td>
                                <td><?= $price ?></td>
                                <td><?= $stock ?></td>
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