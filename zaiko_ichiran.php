<?php
session_start();

if (empty($_SESSION['login'])) {
    $_SESSION['error2'] = 'ログインしてください';
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $error_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

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
function getBooks($pdo, $limit = 0, $offset = null)
{
    $sql = "SELECT * FROM books";
    if (!empty($_GET['sort_column']) && !empty($_GET['sort_value'])) {
        $sql .= " ORDER BY {$_GET['sort_column']} {$_GET['sort_value']}";
    }
    if ($limit > 0) {
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    // return $stmt;
    $books = [];
    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $books[] = $book;
    }
    return $books;
}

function bookCount($pdo)
{
    $sql = "SELECT count(id) AS count FROM books";
    $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    return $row['count'];
}


$count = bookCount($pdo);
$limit = 10;
$page = ceil($count / $limit);
$current_page = (empty($_GET['page'])) ? 1 : $_GET['page'];
$offset = ($current_page - 1) * $limit;
$paginate = paginate($count, $current_page, $limit, 5);
extract($paginate);

function paginate($count, $current_page, $limit = 10, $per_count = 10)
{
    $page_count = ceil($count / $limit);
    $page_start = $current_page;
    $page_end = $page_start + $per_count - 1;
    if ($page_end > $page_count) {
        $page_end = $page_count;
        $page_start = $page_end - $per_count + 1;
    }
    if ($page_start < 0) $page_start = 1;

    $page_prev = ($current_page <= 1) ? 1 : $current_page - 1;
    $page_next = ($current_page < $page_count) ? $current_page + 1 : $page_count;

    $pages = range($page_start, $page_end);

    $paginate = compact(
        'page_count',
        'page_start',
        'page_end',
        'page_prev',
        'page_next',
        'pages',
    );
    return $paginate;
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
                            <th id="salesDate">
                                発売日
                                <div>
                                    <a href="?sort_column=salesDate&sort_value=ASC">[古い順]</a>
                                    <a href="?sort_column=salesDate&sort_value=DESC">[新しい順]</a>
                                </div>
                            </th>
                            <th id="itemPrice">
                                金額
                                <div>
                                    <a href="?sort_column=price&sort_value=ASC">[安い順]</a>
                                    <a href="?sort_column=price&sort_value=DESC">[高い順]</a>
                                </div>
                            </th>
                            <th id="stock">在庫数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (getBooks($pdo, $limit, $offset) as $book) : ?>
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

            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="?page=1">最初</a></li>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page_prev ?>">前へ</a></li>
                    <?php foreach ($pages as $page) : ?>
                        <?php if ($current_page == $page) : ?>
                            <li class="page-item active"><a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a></li>
                        <?php else : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a></li>
                        <?php endif ?>
                    <?php endforeach ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page_next ?>">次へ</a></li>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page_count ?>">最後</a></li>
                </ul>
            </nav>
        </div>
    </form>
    <div id="footer">
        <footer>株式会社アクロイト</footer>
    </div>
</body>

</html>