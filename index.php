<?php
// 开启 session
session_start();

// 引入数据库连接文件
include('db.php');

// 创建数据库连接
$host = 'localhost';
$db = 'book';
$user = 'root';
$pass = '123456789';

$conn = new mysqli($host, $user, $pass, $db);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 检查并处理“加入购物车”请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["book_id"])) {
    $book_id = $_POST["book_id"];
    $user_id = $_SESSION['user_id'];

    // 检查用户是否已经登录
    if (!isset($user_id)) {
        header('Location: login.html');
        exit();
    }

    // 添加书籍到购物车
    $sql = "INSERT INTO cart (uid, bid, quantity) VALUES ('$user_id', '$book_id', 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
    if ($conn->query($sql) === TRUE) {
        header('Location: cart.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// 获取书籍数据以显示在页面上
// 获取当前页码
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // 每页显示的书籍数量
$offset = ($page - 1) * $limit;

// 查询随机不同的书籍，包括 bid 列，分页显示
$sql = "SELECT DISTINCT bid, bname, author, publishing, pic, price FROM book GROUP BY bid ORDER BY RAND() LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// 检查查询结果
$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// 获取总书籍数以计算页数
$sql_total = "SELECT COUNT(DISTINCT bid) AS total FROM book";
$result_total = $conn->query($sql_total);
$total_books = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_books / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>优品书库</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="brand">
                <a href="#">优品书库</a>
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
                <li><a href="README.html">项目说明</a></li>
            </ul>
        </div>
    </nav>
    <main>
        <section class="hero">
            <h1>欢迎来到优品书库</h1>
            <p>在这里找到您喜爱的书籍</p>
            <!-- <a href="#" class="btn" >立即购买</a> -->
            <button class="btn" onclick="checkout()">立即购买</button>
        </section>
    </main>
    <!-- home区域 -->
    <section id="home">
        <div class="lvjing"> <!-- 滤镜效果的容器 -->
            <div class="container">
                <div class="row wow animated bounceInUp"> <!-- 使用 WOW.js 和 Animate.css 实现动画效果的行 -->
                    <div class="col-md-1"></div> <!-- 左侧占位列 -->
                    <div class="col-md-10">
                        <h1 class="text-center">2023年“影响世界华人终身成就大奖”获得者!</h1> <!-- 主要标题，居中显示 -->
                        <h1 class="text-center">
                            九十四岁史学大家<br>
                            许倬云 倾心新作
                        </h1> <!-- 副标题，居中显示 -->
                        <img src="images/cjxs.png" alt="" class="img-responsive"> <!-- 图片，响应式布局 -->
                    </div>
                    <div class="col-md-1"></div> <!-- 右侧占位列 -->
                </div>
            </div>
        </div>
    </section>

    <section id="random-books">
    <div class="container">
        <?php if (count($books) > 0): ?> <!-- 检查是否有书籍 -->
            <?php for ($i = 0; $i < count($books); $i++): ?> <!-- 循环遍历所有书籍 -->
                <?php if ($i % 5 == 0): ?> <!-- 每5本书开启一个新的行 -->
                    <div class="row">
                <?php endif; ?>

                <div class="col-md-2 book-container"> <!-- 每本书的容器 -->
                    <img src="<?php echo $books[$i]['pic']; ?>" alt="<?php echo $books[$i]['bname']; ?>" class="img-responsive"> <!-- 书籍图片 -->
                    <h2><?php echo $books[$i]['bname']; ?></h2> <!-- 书名 -->
                    <p>作者: <?php echo $books[$i]['author']; ?></p> <!-- 作者 -->
                    <p>出版社: <?php echo $books[$i]['publishing']; ?></p> <!-- 出版社 -->
                    <p>价格: ￥<?php echo $books[$i]['price']; ?></p> <!-- 价格 -->
                    <div class="book-actions"> <!-- 书籍操作按钮 -->
                    <button class="btn-action favorite-btn" data-book-id="<?php echo $books[$i]['bid']; ?>">
                        <i class="fas fa-star"></i> 收藏
                    </button> <!-- 收藏按钮，带有书籍ID -->
                        <form method="post" action="index.php" style="display:inline;">
                            <input type="hidden" name="book_id" value="<?php echo $books[$i]['bid']; ?>"> <!-- 隐藏的书籍ID输入框 -->
                            <button type="submit" class="btn-action"><i class="fas fa-shopping-cart"></i> 加入购物车</button>
                        </form> <!-- 加入购物车表单 -->
                    </div>
                </div>

                <?php if (($i + 1) % 5 == 0 || $i == count($books) - 1): ?> <!-- 每5本书或是最后一本书时结束当前行 -->
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        <?php else: ?>
            <p>暂无书籍展示。</p> <!-- 没有书籍时显示的消息 -->
        <?php endif; ?>
    </div>
</section>

<!-- 分页器 -->
<div class="pagination pagination-bottom">
    <?php if (isset($page) && $page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="prev">上一页</a>
    <?php endif; ?>

    <?php for ($i = 1; isset($total_pages) && $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo isset($page) && $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if (isset($page) && isset($total_pages) && $page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="next">下一页</a>
    <?php endif; ?>
</div>

<footer style="text-align: center; padding: 20px 0; background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
    <div style="margin-top: 10px;">
        © <?php echo date("Y"); ?> | 橙子🍊 |<a href="http://www.yopy.com" style="color: #007bff; text-decoration: none;">www.yopy.com</a> |
        <a href="https://github.com/MGHYGitHub/Book_Mall" target="_blank" >GitHub </a>
    </div>
    <div style="margin-top: 5px;">
        <a href="https://www.yopy.com/" style="color: #007bff; text-decoration: none;">蜀ICP备13014270号</a>
    </div>
</footer>
<script>
function addFavorite(button) {
    // 获取书籍ID
    var bookId = button.dataset.bookId;

    // 创建XMLHttpRequest对象
    var xhr = new XMLHttpRequest();

    // 初始化一个POST请求，指向 add_favorite.php
    xhr.open('POST', 'add_favorite.php', true);

    // 设置请求头，以表明数据是 URL 编码的表单数据
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // 定义请求状态变化时的处理函数
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) { // 请求已完成
            if (xhr.status === 200) { // 请求成功
                try {
                    // 尝试解析响应的JSON数据
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // 收藏成功的提示
                        alert('已添加至收藏夹！');
                    } else {
                        // 收藏失败的提示
                        alert('添加至收藏夹失败: ' + response.message);
                    }
                } catch (e) {
                    // JSON解析错误的提示
                    alert('JSON parse error: ' + e.message);
                }
            } else {
                // 请求失败的提示
                alert('请求失败，状态为: ' + xhr.status);
            }
        }
    };

    // 发送请求数据，其中包含书籍ID
    xhr.send('book_id=' + encodeURIComponent(bookId));
}

// 当DOM内容加载完毕时执行
document.addEventListener('DOMContentLoaded', function() {
    // 获取所有具有 'favorite-btn' 类的按钮
    var favoriteBtns = document.querySelectorAll('.favorite-btn');

    // 为每个按钮添加点击事件监听器
    favoriteBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // 调用 addFavorite 函数
            addFavorite(btn);
        });
    });
});

function checkout() {
    // 结算功能待实现的提示
    alert("功能待实现。");
}
</script>
</body>
</html>
