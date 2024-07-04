<!-- cart.php -->
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
$pass = '123456789'; // 根据实际情况修改

$conn = new mysqli($host, $user, $pass, $db);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 检查并处理“删除购物车”请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "delete") {
    $bid = $_POST["bid"];
    $user_id = $_SESSION['user_id'];

    // 删除购物车中的书籍
    $sql_delete = "DELETE FROM cart WHERE uid = ? AND bid = ?";
    $stmt_delete = $conn->prepare($sql_delete);

    if (!$stmt_delete) {
        die("删除购物车准备语句失败: " . $conn->error);
    }

    $stmt_delete->bind_param("ii", $user_id, $bid);

    if ($stmt_delete->execute()) {
        header('Location: cart.php');
        exit();
    } else {
        echo "Error: " . $stmt_delete->error;
    }
}

// 处理数量更新请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update_quantity") {
    $bid = $_POST["bid"];
    $quantity = $_POST["quantity"];
    $user_id = $_SESSION['user_id'];

    // 准备更新数量的SQL语句
    $sql_update = "UPDATE cart SET quantity = ? WHERE uid = ? AND bid = ?";
    $stmt_update = $conn->prepare($sql_update);

    if (!$stmt_update) {
        die("更新购物车数量准备语句失败: " . $conn->error);
    }

    $stmt_update->bind_param("iii", $quantity, $user_id, $bid);

    if ($stmt_update->execute()) {
        header('Location: cart.php');
        exit();
    } else {
        echo "Error: " . $stmt_update->error;
    }
}

// 获取当前用户的购物车内容
$user_id = $_SESSION['user_id'];
$query = "SELECT book.bid, book.bname, book.author, book.publishing, book.pic, book.price, cart.quantity 
          FROM cart 
          INNER JOIN book ON cart.bid = book.bid 
          WHERE cart.uid = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("获取购物车内容准备语句失败: " . $conn->error);
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    die("执行购物车内容查询失败: " . $stmt->error);
}

$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);

// 初始化 $totalPrice 变量
$totalPrice = 0;

// 计算总价格
if ($cartItems) {
    $totalPrice = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $cartItems));
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cart.css">
    <title>购物车</title>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="brand">
                    <a href="index.php">优品书库</a>
                </div>
                <ul class="nav-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php">注销</a></li>
                        <li><a href="cart.php">购物车</a></li>
                    <?php else: ?>
                        <li><a href="login.html">登录</a></li>
                        <li><a href="register.html">注册</a></li>
                    <?php endif; ?>
                    <li><a href="contact.html">联系我们</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- 主要内容区域开始 -->
        <section class="cart-items">
            <div class="container">
                <h2>购物车</h2>
                <?php if (count($cartItems) > 0): ?>
                    <!-- 如果购物车中有商品，则显示商品列表 -->
                    <table>
                        <thead>
                            <tr>
                                <th>封面</th>
                                <th>书名</th>
                                <th>作者</th>
                                <th>出版社</th>
                                <th>价格</th>
                                <th>数量</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <!-- 循环遍历购物车中的每个商品项 -->
                                <tr>
                                    <td><img src="<?php echo $item['pic']; ?>" alt="封面图" class="cart-item-img"></td>
                                    <td><?php echo $item['bname']; ?></td>
                                    <td><?php echo $item['author']; ?></td>
                                    <td><?php echo $item['publishing']; ?></td>
                                    <td>￥<?php echo $item['price']; ?></td>
                                    <td>
                                        <!-- 数量控制按钮 -->
                                        <div class="quantity-container">
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['bid']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" onchange="updateQuantity(<?php echo $item['bid']; ?>, this.value)">
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['bid']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- 删除商品表单 -->
                                        <form action="cart.php" method="POST">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="bid" value="<?php echo $item['bid']; ?>">
                                            <button type="submit" class="delete-btn">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- 结算按钮和总计信息 -->
                    <div class="checkout">
                        <p class="modern-total-items">总商品数：<?php echo count($cartItems); ?></p>
                        <p class="modern-total-price">总价格：￥<?php echo number_format($totalPrice, 2); ?></p>
                        <button class="modern-checkout-button" onclick="checkout()">结算</button>
                    </div>
                <?php else: ?>
                    <!-- 如果购物车为空，则显示提示信息 -->
                    <p>购物车为空。</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer style="text-align: center; padding: 20px 0; background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
        <!-- <div>
            <img src="path/to/logo.png" alt="页面商标" style="height: 50px;">
        </div> -->
        <div style="margin-top: 10px;">
            © <?php echo date("Y"); ?>| 橙子🍊 | <a href="http://www.yopy.com" style="color: #007bff; text-decoration: none;">www.yopy.com</a> |
            <a href="https://github.com/MGHYGitHub/Book_Mall" target="_blank" >GitHub </a>
        </div>
        <div style="margin-top: 5px;">
            <a href="https://www.yopy.com/" style="color: #007bff; text-decoration: none;">蜀ICP备13014270号</a>
        </div>
    </footer>
    <script>
    // 定义一个函数用于更新购物车中商品的数量
    function updateQuantity(bid, quantity) {
        // 如果数量小于1，则将数量设为1，确保不会小于最小值
        if (quantity < 1) {
            quantity = 1;
        }

        // 创建一个 FormData 对象，用于存储要发送到服务器的数据
        var formData = new FormData();
        formData.append('action', 'update_quantity'); // 添加操作标识
        formData.append('bid', bid); // 添加书籍 ID
        formData.append('quantity', quantity); // 添加更新后的数量

        // 使用 Fetch API 发送 POST 请求到 cart.php
        fetch('cart.php', {
            method: 'POST',
            body: formData // 将 FormData 对象作为请求体发送
        })
        .then(response => response.text()) // 解析响应的文本内容
        .then(data => {
            // 在控制台输出更新成功的信息，可以根据需要处理响应
            console.log('更新成功', data);
            location.reload();  // 刷新页面，显示更新后的购物车内容
        })
        .catch(error => {
            // 捕获并输出更新失败的错误信息
            console.error('更新失败', error);
        });
    }

    // 定义一个结算函数，暂时弹出提示消息表示结算功能待实现
    function checkout() {
        alert("结算功能待实现。");
    }
</script>
</body>
</html>
