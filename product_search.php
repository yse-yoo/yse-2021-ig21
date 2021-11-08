<?php
require_once 'Book.php';

session_start();
if (empty($_SESSION['login'])){
    $_SESSION['error2'] = 'ログインしてください';
    header('Location: login.php');
    exit;
}

$options['price'] = Book::$priceOptions;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>商品検索</title>
    <link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
    <div id="header">
        <h1>商品検索</h1>
    </div>
    <form action="search_result.php" method="get" id="test">
        <div id="pagebody">
            <div id="center">
                <table>
                    <thead>
                        <tr>
                            <th id="keyword">キーワード</th>
                            <th id="sales_year">年代別</th>
                            <th id="price">価格</th>
                            <th id="stock">在庫数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="keyword"></td>
                            <td><input type="text" name="year"></td>
                            <td>
                                <select name="price" id="">
                                    <option value="">----</option>
                                <?php foreach ($options['price'] as $index => $prices): ?>
                                    <option value="<?= $index ?>"><?= $prices[0] ?>円台</option>
                                <?php endforeach ?>
                                </select>
                            </td>
                            <td><input type="text" name="stock"></td>
                        </tr>
                    </tbody>
                </table>
                <div id="kakunin">
                    <button type="submit" id="message">検索</button>
                </div>
            </div>
        </div>
    </form>
    <div id="footer">
        <footer>株式会社アクロイト</footer>
    </div>
</body>

</html>