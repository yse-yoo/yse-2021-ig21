<?php
session_start();

function getByid($id, $con)
{
    $sql = "SELECT * FROM books WHERE id = {$id}";
    return $con->query($sql)->fetch(PDO::FETCH_ASSOC);
}

function updateByid($id, $con, $total)
{
    $sql = "UPDATE books SET stock = {$total} WHERE id = {$id}";
    $con->query($sql);
}

if (empty($_SESSION['login'])) {
    $_SESSION['error2'] = 'ログインしてください';
    header('Location: login.php');
    exit;
}

if (empty($_POST['books'])) {
    //⑨SESSIONの「success」に「入荷する商品が選択されていません」と設定する。
    $_SESSION['success'] = '入荷する商品が選択されていません';
    //⑩在庫一覧画面へ遷移する。
    header('Location: zaiko_ichiran.php');
    exit;
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

foreach ($_POST['books'] as $index => $book_id) {
    $stock = $_POST['stock'][$index];
    if (!is_numeric($stock)) {
        $_SESSION['error'] = '数値以外が入力されています';
        include 'nyuka.php';
        exit;
    }
    $book = getByid($book_id, $pdo);
    $total_stock = $book['stock'] + $stock;
    if ($total_stock > 100) {
        $_SESSION['error'] = '最大在庫数を超える数は入力できません';
        include 'nyuka.php';
        exit;
    }
}

if (isset($_POST['add'])) {
    foreach ($_POST['books'] as $index => $book_id) {
        $book = getByid($book_id, $pdo);
        $total_stock = $book['stock'] + $_POST['stock'][$index];
        updateByid($book_id, $pdo, $total_stock);
    }
    $_SESSION['success'] = '入荷が完了しました';
    header('Location: zaiko_ichiran.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>入荷確認</title>
    <link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
    <div id="header">
        <h1>入荷確認</h1>
    </div>
    <form action="nyuka_kakunin.php" method="post" id="test">
        <div id="pagebody">
            <div id="center">
                <table>
                    <thead>
                        <tr>
                            <th id="book_name">書籍名</th>
                            <th id="stock">在庫数</th>
                            <th id="stock">入荷数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_POST['books'] as $index => $book_id) : ?>
                            <?php $book = getByid($book_id, $pdo); ?>
                            <tr>
                                <td><?= $book['title'] ?></td>
                                <td><?= $book['stock'] ?></td>
                                <td><?= $stock = $_POST['stock'][$index] ?></td>
                            </tr>
                            <input type="hidden" name="books[]" value="<?= $book_id ?>">
                            <input type="hidden" name="stock[]" value='<?= $stock ?>'>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <div id="kakunin">
                    <p>
                        上記の書籍を入荷します。<br>
                        よろしいですか？
                    </p>
                    <button type="submit" id="message" formmethod="POST" name="add" value="ok">はい</button>
                    <button type="submit" id="message" formaction="nyuka.php">いいえ</button>
                </div>
            </div>
        </div>
    </form>
    <div id="footer">
        <footer>株式会社アクロイト</footer>
    </div>
</body>

</html>