<!-- login.php -->
<?php
// 引入数据库连接文件
require_once 'db.php';

// 开启 session
session_start();

// 检查表单是否已提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据并进行简单的防注入处理
    $login = htmlspecialchars(trim($_POST['login']));
    $password = htmlspecialchars(trim($_POST['password']));

    // 查找用户，用户输入的内容既可以是邮箱也可以是用户名
    $query = "SELECT * FROM users WHERE email = :loginEmail OR username = :loginUsername";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['loginEmail' => $login, 'loginUsername' => $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // 登录成功，保存用户信息到 session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // 跳转到首页
        header("Location: index.php");
        exit;
    } else {
        // 登录失败时显示弹窗提示并跳转回登录页面
        echo "<script>alert('邮箱/用户名或密码错误，请重试。'); window.location.href = 'login.html';</script>";
        exit;
    }
}
?>
