<!-- remove_from_cart.php -->
<?php
// 开启 session
session_start();

// 检查用户是否登录，若未登录则跳转至登录页面
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 引入数据库连接文件
require_once 'db.php';

// 创建数据库连接
$host = 'localhost';
$db = 'book';
$user = 'root';
$pass = 'mghy040122'; // 根据实际情况修改

$conn = new mysqli($host, $user, $pass, $db);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 检查并处理“删除购物车”请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["bid"])) {
    $bid = $_POST["bid"];
    $user_id = $_SESSION['user_id'];

    // 删除购物车中的书籍
    $sql = "DELETE FROM cart WHERE uid = ? AND bid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $bid);

    if ($stmt->execute()) {
        header('Location: cart.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>
