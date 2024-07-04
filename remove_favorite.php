<?php
// 开启会话，使用 session 来保存用户信息
session_start();

// 引入数据库连接文件
include 'db.php'; // 这里应包含你的数据库连接文件

// 检查用户是否已登录，如果没有登录则重定向到登录页面
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 处理 POST 请求，当用户提交删除收藏的请求时执行
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取要删除的收藏记录的 ID
    $favorite_id = $_POST['favorite_id'];

    // 准备 SQL 查询语句，用于从数据库中删除指定的收藏记录
    $query = "DELETE FROM favorites WHERE id = ? AND user_id = ?";
    // 预处理 SQL 语句，防止 SQL 注入攻击
    $stmt = $pdo->prepare($query);
    // 执行 SQL 查询，删除收藏记录
    $stmt->execute([$favorite_id, $_SESSION['user_id']]);

    // 删除完成后重定向回收藏页面
    header('Location: favorites.php');
    exit();
}
?>
