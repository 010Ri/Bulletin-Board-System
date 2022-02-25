<?php
session_start();  // session start

require 'bbs_ops.php';  // read bbs_ops.php for use BBS_OPS class 
$bbs_ops = new BBS_OPS();  // create BBS_OPS instance

$page = 'posts.php';  // set page name

$pdo = $bbs_ops->DB_connect();  // connect to database
$name = $bbs_ops->login_check($page);  // Login check
$_SESSION['name'] = $name;
$data = $bbs_ops->get_posts($pdo);  // get all posts
$array_length = count($data);

// redirect to index.php
if (isset($_POST['logout'])) {
    $bbs_ops->to_index();
}

// post process
if (isset($_POST['text'])) {  // if text is set
    $bbs_ops->post_comment($pdo, $name);
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
        <div class="welcome">
            <form method="post" action="posts.php">
                ようこそ <?php echo $name; ?> さん
                <input class="blue-button" type="submit" name="logout" value="ログアウト">
            </form>
        </div>
        <?php if($name != 'ゲスト') : ?>
            <H2>コメント</H2>
            <form action="posts.php" method="post">
                <textarea id="textarea" name="text" placeholder="コメントはこちらに記入してください。" required></textarea>
                <p>
                    <input class="red-button" type="submit" value="投稿する">
                </p>
            </form>
            <Hr>
        <?php endif; ?>
        <H1>投稿</H1>
        <div class="posts-list">
            <?php foreach ($data as $contents) : ?>
                <div class="posted-contents">
                    <div class="info">
                        <?php
                            if (htmlspecialchars($contents['name'], ENT_QUOTES, 'UTF-8') == '管理者' || htmlspecialchars($contents['name'], ENT_QUOTES, 'UTF-8') == 'admin') {
                                $username = "管理者";
                                $admin_flag = true;
                            }
                            elseif(htmlspecialchars($contents['name'], ENT_QUOTES, 'UTF-8') == $name) {
                                $username = "あなた";
                                $admin_flag = false;
                            }
                            else {
                                $username = htmlspecialchars($contents['name'], ENT_QUOTES, 'UTF-8');
                                $admin_flag = false;
                            }
                        ?>
                        <?php if($admin_flag == true) : ?>
                            <div class="admin-info"><?php echo $array_length . ". " . $username . "の投稿 : " . htmlspecialchars($contents['posted_time'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php else : ?>
                            <div class="user-info"><?php echo $array_length . ". " . $username . "の投稿 : " . htmlspecialchars($contents['posted_time'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <?php if(htmlspecialchars($contents['name'], ENT_QUOTES, 'UTF-8') == $name || $name == '管理者' || $name == 'admin') : ?>
                            <a class="green-button" href="edit.php?which=post&id=<?php echo $contents['id']; ?>">編集</a>
                            <a class="green-button" href="delete.php?which=post&id=<?php echo $contents['id']; ?>">削除</a>
                        <?php endif; ?>
                    </div>
                    <?php if($admin_flag == true) : ?>
                        <div class="admin-text"><a href="thread.php?id=<?php echo $contents['id']; ?>"><?php echo htmlspecialchars($contents['text'], ENT_QUOTES, 'UTF-8'); ?></a></div>
                    <?php else : ?>
                        <div class="user-text"><a href="thread.php?id=<?php echo $contents['id']; ?>"><?php echo htmlspecialchars($contents['text'], ENT_QUOTES, 'UTF-8'); ?></a></div>
                    <?php endif; ?>
                    <input type="hidden" name="id" value="<?php echo $contents['id']; ?>" />
                </div>
            <?php $array_length--; endforeach; ?>
        </div>
    </main>
    <?php include('bottom-right.html'); ?>
    <?php include('footer.html'); ?>
</body>
</html>
