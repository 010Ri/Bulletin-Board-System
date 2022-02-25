<?php

// Class For BBS Operations
class BBS_OPS
{
    // define variable for BBS_OPS
    private $dsn;
    private $user;
    private $password;

    // constructor
    function __construct() {
        // set variable for database connection
        $this->dsn = 'mysql:charset=UTF8;dbname=test;host=mysql';
        $this->user = 'test';
        $this->password = 'test';
    }

    // function for Database connection
    public function DB_connect() {
        // connect Database
        try {
            // create PDO instance
            $pdo = new PDO($this->dsn, $this->user, $this->password, array(PDO::ATTR_PERSISTENT => TRUE));
            return $pdo;
        }
        catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage;
        }
    }

    // function for registering
    public function register_user($pdo) {
        // try executing SQL statement for registering new user
        try {
            // check if the same name exists
            $sql = "SELECT name FROM user_info WHERE name=:name";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->execute();  // execute SQL statement
            $data = $stmt->fetch(PDO::FETCH_COLUMN);  // fetch data
            if ($data == $_POST['name']) {  // if the name has already exists
                return $_POST['name'] . " は既に使用されています。";
            }
            // registering new user
            $sql = "INSERT INTO user_info (name, password, email) VALUES (:name, :password, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $_POST['password'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->execute();
            return $_POST['name'] . "さん、ご登録ありがとうございます！ログイン画面よりログインしてください。";
        }
        catch (PDOException $e) {
            echo 'REGISTER Error: ' . $e->getMessage;
        }
    }

    // function for Login
    public function Login($pdo) {
        // try executing SQL statement for getting posts 
        try {
            $sql = "SELECT * FROM user_info WHERE name=:name AND password=:password AND invalid=0";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->bindParam(':password', $_POST['password'], PDO::PARAM_STR);
            $stmt->execute();  // execute SQL statement
            $data = $stmt->fetch(PDO::FETCH_ASSOC);  // fetch data
            // if username and password is correct
            if ($data['name'] == $_POST['name'] && $data['password'] == $_POST['password']) {
                $_SESSION["name"] = $_POST['name'];// substitute POSTed username for $_SESSION["name"] because it's clear that the user doesn't logged in
                header('Location: posts.php'); // redirect to posts.php
                exit;
            }
            else {
                return "ユーザー名かパスワードが間違っています。";
            }
        }
        catch (PDOException $e) {
            echo 'SQL Error: ' . $e->getMessage;
        }
    }

    // function for Login checking
    public function login_check($page) {
        // if name is set
        if (isset($_SESSION['name'])) {
            return $this->logged_in_already($page);
        }
        else {
            return $this->not_logged_in_yet($page);
        }
    }

    // function for when logged in
    public function logged_in_already($page) {
        // if page name is index.php
        if ($page == 'index.php') {
            header('Location: posts.php'); // redirect to posts.php
            exit; // 処理終了
        }
        // if page name is posts.php
        elseif ($page == 'posts.php') {
            return $_SESSION["name"];  // return username
        }
    }

    // function for when NOT logged in
    public function not_logged_in_yet($page) {
        // if page name is index.php
        if ($page == 'posts.php') {
            return "ゲスト";
        }
    }

    // function for redirecting index.php
    public function to_index() {
        $_SESSION = [];
        session_destroy(); // session finish
        header('Location: index.php'); // redirect to index.php
        exit;
    }

    // function for redirecting register.php
    public function to_register() {
        $_SESSION = [];
        session_destroy(); // session finish
        header('Location: register.php'); // redirect to register.php
        exit;
    }

    // function for redirecting posts.php
    public function to_posts() {
        header('Location: posts.php'); // redirect to posts.php
        exit;
    }

    // function for redirecting thread.php
    public function to_thread($id) {
        header('Location: thread.php?id=' . $id); // redirect to thread.php
        exit;
    }

    // function for getting posts from database
    public function get_posts($pdo) {
        // try executing SQL statement for getting posts 
        try {
            $sql = "SELECT * FROM exchanges WHERE invalid=0";  // create SQL statement
            // if id is set
            if (isset($_GET['id'])) {
                $sql = $sql . " AND id=" . $_GET['id'];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute();  // execute SQL statement
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);  // fetch ALL data
            $reversed_data = array_reverse($data);
            return $reversed_data;
        }
        catch (PDOException $e) {
            echo 'SQL Statement Execution Error: ' . $e->getMessage;
        }
    }

    // function for getting posts from database
    public function get_posts_ext($pdo) {
        // try executing SQL statement for getting posts 
        try {
            $sql = "SELECT * FROM exchanges WHERE invalid=0";  // create SQL statement
            // if id is set
            if (isset($_GET['id'])) {
                $sql = $sql . " AND id=" . $_GET['id'];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute();  // execute SQL statement
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);  // fetch ALL data
            $reversed_data = array_reverse($data);
            foreach ($data as $d) {
                if ($d['name'] == $_SESSION['name'] || $_SESSION['name'] == 'admin' || $_SESSION['name'] == '管理者') {
                    return $reversed_data;
                }
                else {
                    header('Location: posts.php'); // redirect to thread.php
                    exit;
                }
            }
        }
        catch (PDOException $e) {
            echo 'SQL Statement Execution Error: ' . $e->getMessage;
        }
    }

    // function for getting replies from database
    public function get_replies($pdo) {
        // try executing SQL statement for getting replies
        try {
            $sql = "SELECT * FROM replies WHERE post_id=:post_id AND invalid=0";  // create SQL statement
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':post_id', $_GET['id'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->execute();  // execute SQL statement
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);  // fetch ALL data
            $reversed_data = array_reverse($data);
            return $reversed_data;
        }
        catch (PDOException $e) {
            echo 'SQL Statement Execution Error: ' . $e->getMessage;
        }
    }

    // function for getting replies for editing from database
    public function get_replies_ext($pdo) {
        // try executing SQL statement for getting replies
        try {
            $sql = "SELECT * FROM replies WHERE id=:id AND invalid=0";  // create SQL statement
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->execute();  // execute SQL statement
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);  // fetch ALL data
            foreach ($data as $d) {
                if ($d['name'] == $_SESSION['name'] || $_SESSION['name'] == 'admin' || $_SESSION['name'] == '管理者') {
                    return $data;
                }
                else {
                    header('Location: posts.php'); // redirect to thread.php
                    exit;
                }
            }
        }
        catch (PDOException $e) {
            echo 'SQL Statement Execution Error: ' . $e->getMessage;
        }
    }

    // function for posting
    public function post_comment($pdo, $name) {
        // try executing SQL statement for posting
        try {
            $sql = "INSERT INTO exchanges (name, text) VALUES (:name, :text)";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->bindParam(':text', $_POST['text'], PDO::PARAM_STR);
            $stmt->execute();  // execute SQL statement
            header('Location: posts.php');  // redirecting to posts.php for reflecting the change
            exit;
        }
        catch (PDOException $e) {
            echo 'POST Error: ' . $e->getMessage;
        }
    }

    // function for replying
    public function post_reply($pdo, $name) {
        // try executing SQL statement for replying
        try {
            $sql = "INSERT INTO replies (post_id, name, text) VALUES (:post_id, :name, :text)";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':post_id', $_POST['reply_id'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':text', $_POST['text'], PDO::PARAM_STR);
            $stmt->execute();  // execute SQL statement
            header('Location: thread.php?id=' . $_POST['reply_id']);  // redirecting to posts.php for reflecting the change
            exit;
        }
        catch (PDOException $e) {
            echo 'POST Error: ' . $e->getMessage;
        }
    }

    // function for editing the post
    public function edit_post($pdo) {
        // try executing SQL statement for editing the post
        try {
            $sql = "UPDATE exchanges SET text=:text WHERE id=:id";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->bindParam(':text', $_POST['text'], PDO::PARAM_STR);
            $stmt->execute();  // execute SQL statement
            header('Location: posts.php');  // redirecting to posts.php for reflecting the change
            exit;
        }
        catch (PDOException $e) {
            echo 'EDIT Error: ' . $e->getMessage;
        }
    }

    // function for editing the reply
    public function edit_reply($pdo) {
        // try executing SQL statement for editing the reply
        try {
            $sql = "UPDATE replies SET text=:text WHERE id=:id";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->bindParam(':text', $_POST['text'], PDO::PARAM_STR);
            $stmt->execute();  // execute SQL statement
            header('Location: thread.php?id=' . $_SESSION['id']);  // redirecting to posts.php for reflecting the change
            exit;
        }
        catch (PDOException $e) {
            echo 'EDIT Error: ' . $e->getMessage;
        }
    }

    // function for Deleting the post
    public function delete_post($pdo) {
        // try executing SQL statement for deleting the post
        try {
            $sql = "UPDATE exchanges SET invalid=1 WHERE id=:id";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->execute();  // execute SQL statement
            header('Location: posts.php');  // redirecting to posts.php for reflecting the change
            exit;
        }
        catch (PDOException $e) {
            echo 'DELETE Error: ' . $e->getMessage;
        }
    }

    // function for Deleting the reply
    public function delete_reply($pdo) {
        // try executing SQL statement for deleting the post
        try {
            $sql = "UPDATE replies SET invalid=1 WHERE id=:id";  // create SQL statement
            $stmt = $pdo->prepare($sql);  // prepare executing SQL statement
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);  // bind search conditions to SQL statement
            $stmt->execute();  // execute SQL statement
            $this->to_thread($_SESSION['id']);
        }
        catch (PDOException $e) {
            echo 'DELETE Error: ' . $e->getMessage;
        }
    }

}

?>