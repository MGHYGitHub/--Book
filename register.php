<!-- register.php -->
<?php
// 引入数据库连接文件
require_once 'db.php';

// 检查表单是否已提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据并进行简单的防注入处理
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // 检查密码和确认密码是否匹配
    if ($password !== $confirm_password) {
        echo "<script>alert('密码和确认密码不匹配，请重试。'); window.history.back();</script>";
        exit;
    }

    // 对密码进行加密处理
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 检查用户是否已存在
    $check_user_query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($check_user_query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // 用户已存在时显示弹窗提示并跳转到登录页面
        echo "<script>alert('用户已存在，请登录。'); window.location.href = 'login.html';</script>";
        exit;
    } else {
        // 插入新用户数据
        $insert_query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($insert_query);
        $params = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
        ];

        if ($stmt->execute($params)) {
            // 注册成功后跳转到登录页面
            header("Location: login.html");
            exit;
        } else {
            echo "<script>alert('注册失败，请重试。'); window.history.back();</script>";
        }
    }
}
?>
