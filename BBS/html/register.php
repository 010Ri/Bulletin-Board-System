<?php
session_start();  // session start

require 'bbs_ops.php';  // read bbs_ops.php for use BBS_OPS class 
$bbs_ops = new BBS_OPS();  // create BBS_OPS instance

$page = 'register.php';  // set page name
$status = "　";  // initial status

$pdo = $bbs_ops->DB_connect();  // connect to database

// redirect to index.php
if (isset($_POST['login'])) {
    $bbs_ops->to_index();
}

// register process
if (!empty($_POST['name'] && $_POST['password'] == $_POST['confirm'] && $_POST['email'])) {  // if form completed
    $status = $bbs_ops->register_user($pdo);
}
elseif ($_POST['password'] != $_POST['confirm']) {  // if form completed
    $status = "パスワードが異なっています。";
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
        <H1>新規登録</H1>
        <form action="register.php" method="post">
            <p>
                　　ユーザー名　　 <input type="text" name="name" required>
            </p>
            <p>
                　　パスワード　　 <input type="password" name="password" required>
            </p>
            <p>
                パスワード（確認） <input type="password" name="confirm" required>
            </p>
            <p>
                　メールアドレス　 <input type="text" name="email" required>
            </p>
            <p><?php echo $status; ?></p>
            <p>
                <input class="red-button" type="submit" value="登録する">
            </p>
        </form>
        <form action="register.php" method="post">
            <p>
                <input class="gray-button" type="submit" name="login" value="ログイン画面へ">
            </p>
        </form>
    </main>
    <?php include('footer.html'); ?>
</body>
</html>
