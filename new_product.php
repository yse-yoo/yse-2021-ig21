<?php
/**
 * 次の ID を返す
 */
function latestID($con) {
    $sql = "SELECT MAX(id) AS max_id FROM books;";
    $row = $con->query($sql)->fetch(PDO::FETCH_ASSOC);
    $id = $row['max_id'] + 1;
    return $id;
}

/**
 * 新商品追加
 */
function insert($data, $pdo)
{
    unset($data['decision']);
    $sql = "INSERT INTO books (title, author, salesDate, isbn, price, stock)
            VALUES (:title, :author, :salesDate, :isbn, :price, :stock)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

function check($posts)
{
    if (empty($posts)) return;
    foreach ($posts as $column => $post) {
        $posts[$column] = htmlspecialchars($post, ENT_QUOTES);
    }
    return $posts;
}

function validate($data)
{
    $errors = [];
    if (empty($data['title'])) $errors['title'] = '書籍名を入力してください。';
    if (empty($data['author'])) $errors['author'] = '著者名を入力してください。';
    if (empty($data['salesDate'])) $errors['salesDate'] = '発売日を入力してください。';
    if (empty($data['isbn'])) $errors['isbn'] = 'ISBNを入力してください。';
    if ($data['price'] < 0) $errors['price'] = '価格を入力してください。';
    if ($data['stock'] < 0) $errors['stock'] = '在庫数を入力してください。';
    return $errors;
}


session_start();

if (empty($_SESSION['login'])){
    $_SESSION['error2'] = 'ログインしてください';
    header('Location: login.php');
    exit;
}

//DB接続
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

$id = latestID($pdo);

if (isset($_POST['decision']) && $_POST['decision'] == 'add') {
    //データチェック
    $posts = check($_POST);
    $errors = validate($posts);
    if (!$errors) {
        //追加
        insert($posts, $pdo);
        header('Location: zaiko_ichiran.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>入荷</title>
    <link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
    <div id="header">
        <h1>商品追加</h1>
    </div>

    <div id="menu">
        <nav>
            <ul>
                <li><a href="zaiko_ichiran.php?page=1">書籍一覧</a></li>
            </ul>
        </nav>
    </div>

    <form action="" method="post">
        <div id="pagebody">
            <div id="error">
                <?php if (isset($errors)): ?>
                <?php foreach ($errors as $error): ?>
                <p><?= $error ?></p>
                <?php endforeach ?>
                <?php endif ?>
            </div>
            <div id="center">
                <table>
                    <thead>
                        <tr>
                            <th id="id">ID</th>
                            <th id="book_name">書籍名</th>
                            <th id="author">著者名</th>
                            <th id="salesDate">発売日</th>
                            <th id="isbn">ISBN</th>
                            <th id="itemPrice">金額(円)</th>
                            <th id="stock">在庫数</th>
                        </tr>
                    </thead>
                    <tr>
                        <td><?= $id ?></td>
                        <td><input type="text" name="title" value="<?= @$posts['title'] ?>"></td>
                        <td><input type="text" name="author" value="<?= @$posts['author'] ?>"></td>
                        <td><input type="text" name="salesDate" value="<?= @$posts['salesDate'] ?>"></td>
                        <td><input type="text" name="isbn" value="<?= @$posts['isbn'] ?>"></td>
                        <td><input type="text" name="price" value="<?= @$posts['price'] ?>"></td>
                        <td><input type="text" name="stock" value="<?= @$posts['stock'] ?>"></td>
                    </tr>
                </table>
                <button type="submit" id="kakutei" formmethod="POST" name="decision" value="add">追加</button>
            </div>
        </div>
    </form>
    <div id="footer">
        <footer>株式会社アクロイト</footer>
    </div>
</body>

</html>