<?php
session_start(); // 开启 session

// 包含数据库连接文件
include 'db.php'; // 数据库连接文件

// 检查用户是否已登录，未登录则跳转到登录页面
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// 查询用户收藏的书籍
$query = "SELECT favorites.id, book.bname, book.author, book.publishing, book.price, book.pic
          FROM favorites
          JOIN book ON favorites.book_id = book.bid
          WHERE favorites.user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- 设置字符编码 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 视口设置 -->
    <title>我的收藏</title>
    <link rel="stylesheet" href="css/favorites.css"> <!-- 引入样式表 -->
</head>
<body>
<nav class="navbar"> <!-- 导航栏 -->
    <div class="container">
        <div class="brand">
            <a href="index.php">优品书库</a> <!-- 网站品牌链接 -->
        </div>
        <ul class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">注销</a></li>
            <?php else: ?>
                <li><a href="login.html">登录</a></li>
                <li><a href="register.html">注册</a></li>
            <?php endif; ?>
            <li><a href="favorites.php">我的收藏</a></li>
            <li><a href="cart.php">购物车</a></li>
            <li><a href="contact.html">联系我们</a></li>
        </ul>
    </div>
</nav>
    <div class="container">
        <h1>我的收藏</h1> <!-- 页面标题 -->
        <div class="favorites-container"> <!-- 收藏容器 -->
            <?php
            if (count($favorites) > 0) {
                foreach ($favorites as $row) {
                    echo "<div class='favorite-item'>";
                    echo "<img src='" . $row['pic'] . "' alt='" . $row['bname'] . "'>"; // 书籍图片
                    echo "<p><strong>书名:</strong> " . $row['bname'] . "</p>"; // 书名
                    echo "<p><strong>作者:</strong> " . $row['author'] . "</p>"; // 作者
                    echo "<p><strong>出版社:</strong> " . $row['publishing'] . "</p>"; // 出版社
                    echo "<p><strong>价格:</strong> $" . $row['price'] . "</p>"; // 价格
                    echo "<form method='post' action='remove_favorite.php'>"; // 删除收藏表单
                    echo "<input type='hidden' name='favorite_id' value='" . $row['id'] . "'>"; // 隐藏输入域，收藏ID
                    echo "<button type='submit' class='btn-remove-favorite'>从收藏中删除</button>"; // 删除按钮
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>您还没有将任何书籍添加到您的收藏夹中。</p>"; // 没有收藏书籍时的提示
            }
            ?>
        </div>
    </div>
    <footer style="text-align: center; padding: 20px 0; background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
        <div style="margin-top: 10px;">
            © <?php echo date("Y"); ?>| 橙子🍊 | <a href="http://www.yopy.com" style="color: #007bff; text-decoration: none;">www.yopy.com</a>
        </div>
        <div style="margin-top: 5px;">
            <a href="https://www.yopy.com/" style="color: #007bff; text-decoration: none;">蜀ICP备13014270号</a>
        </div>
    </footer>
</body>
</html>
