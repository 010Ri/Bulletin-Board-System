<?php
session_start();  // session start

require 'bbs_ops.php';  // read bbs_ops.php for use BBS_OPS class 
$bbs_ops = new BBS_OPS();  // create BBS_OPS instance

$page = 'index.php';  // set page name
$status = "全ての項目に入力してください。";  // initial status

$pdo = $bbs_ops->DB_connect();  // connect to database
$bbs_ops->login_check($page);  // Login check

// redirect to register.php
if (isset($_POST['register'])){  // if name is set
    $bbs_ops->to_register();
}
// redirect to posts.php
if (isset($_POST['view'])){  // if name is set
    $bbs_ops->to_posts();
}
// Login process
if (!empty($_POST['name'] && $_POST['password'])) {  // if name and password are set
    $status = $bbs_ops->Login($pdo);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <script type="text/javascript" src="./js/script.js"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BBS</title>
</head>
<body>
    <?php include('header.html'); ?>
    <main>
        <H1>ログイン</H1>
        <form action="index.php" method="post">
            <p>
                ユーザー名 <input type="text" name="name" required>
            </p>
            <p>
                パスワード <input type="password" name="password" required>
            </p>
            <p><?php echo $status; ?></p>
            <p>
                <input class="red-button" type="submit" name="login" value="ログイン">
            </p>
        </form>
        <form action="index.php" method="post">
            <p>
                <input class="blue-button" type="submit" name="view" value="閲覧（ログインしない）">
                <input class="green-button" type="submit" name="register" value="新規登録">
            </p>
        </form>
    </main>
    <?php include('footer.html'); ?>
</body>
</html>
