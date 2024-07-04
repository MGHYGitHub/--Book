<!-- book.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db.php'); // 数据库连接的文件

// 创建数据库连接
$host = 'localhost';
$db = 'book';
$user = 'root';
$pass = '123456789'; // 根据实际情况修改

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
