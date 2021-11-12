<?php
require_once 'Book.php';

session_start();
if (empty($_SESSION['login'])) {
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
    <form action="search_result.php" method="get">
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
                            <td>
                                <select name="year" id="">
                                    <option value="">----</option>
                                    <option value="1970">1970年代</option>
                                    <option value="1980">1980年代</option>
                                    <option value="1990">1990年代</option>
                                    <option value="2000">2000年代</option>
                                    <option value="2010">2010年代</option>
                                </select>
                            </td>
                            <td>
                                <select name="price" id="">
                                    <option value="">----</option>
                                    <option value="400">400円台</option>
                                    <option value="500">500円台</option>
                                    <option value="700">600円台</option>
                                    <option value="800">700円台</option>
                                    <option value="1000">1000円台</option>
                                    <option value="2000">2000円台</option>
                                </select>
                            </td>
                            <td>
                                <select name="stock">
                                    <option value="">----</option>
                                    <option value="10">10冊未満</option>
                                    <option value="20">20冊未満</option>
                                    <option value="30">30冊未満</option>
                                    <option value="40">40冊未満</option>
                                    <option value="50">50冊以上</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="kakunin">
                    <button type="submit" id="message">検索</button>
                    <button type="submit" id="message" formaction="zaiko_ichiran.php">戻る</button>
                </div>
            </div>
        </div>
    </form>
    <div id="footer">
        <footer>株式会社アクロイト</footer>
    </div>
</body>

</html>