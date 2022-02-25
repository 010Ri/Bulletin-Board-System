<?php
session_start();  // session start

require 'bbs_ops.php';  // read bbs_ops.php for use BBS_OPS class 
$bbs_ops = new BBS_OPS();  // create BBS_OPS instance

$page = 'delete.php';  // set page name
$id = $_GET['id'];  // catch post's id

$pdo = $bbs_ops->DB_connect();  // connect to database
$name = $_SESSION['name'];
$bbs_ops->login_check($page);  // Login check
$data = $bbs_ops->get_posts($pdo);  // get post
// select table
if ($_GET['which'] == "post") {
    $data = $bbs_ops->get_posts_ext($pdo);  // get post
}
elseif ($_GET['which'] == "reply") {
    $data = $bbs_ops->get_replies_ext($pdo);  // get reply
}

// redirect to posts.php or thread.php
if (isset($_POST['cancel']) && $_POST['which'] == "post") {
    $bbs_ops->to_posts();
}
elseif (isset($_POST['cancel']) && $_POST['which'] == "reply") {
    $bbs_ops->to_thread($_SESSION['id']);
}

// redirect to index.php
if (isset($_POST['logout'])) {
    $bbs_ops->to_index();
}

// delete process
if (isset($_POST['id']) && isset($_POST['delete']) && $_POST['which'] == "post") {
    $bbs_ops->delete_post($pdo);
}
elseif (isset($_POST['id']) && isset($_POST['delete']) && $_POST['which'] == "reply") {
    $bbs_ops->delete_reply($pdo);
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
        <form method="post" action="posts.php">
            <?php echo $name; ?> としてログインしています
            <input class="blue-button" type="submit" name="logout" value="ログアウト">
        </form>
        <H1>投稿の削除</H1>
        <form action="" method="post">
            <?php foreach ($data as $contents) : ?>
                <textarea class="e-d-textarea" id="textarea" readonly="true"><?php echo htmlspecialchars($contents['text'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <p>
                    <input class="red-button" type="submit" name="delete" value="この投稿を削除する">
                    <input class="gray-button" type="submit" name="cancel" value="キャンセル">
                    <input type="hidden" name="id" value="<?php echo $contents['id']; ?>" />
                    <input type="hidden" name="which" value="<?php echo $_GET['which']; ?>" />
                </p>
            <?php $array_length--; endforeach; ?>
        </form>
    </main>
    <?php include('footer.html'); ?>
</body>
</html>
