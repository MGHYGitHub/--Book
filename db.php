<?php
// db.php - 数据库连接文件

// 数据库连接参数
$host = 'localhost'; // 数据库主机
$db = 'book'; // 数据库名称
$user = 'root'; // 数据库用户名
$pass = '123456789'; // 数据库密码
$charset = 'utf8mb4'; // 字符集

// 创建数据源名称（DSN）
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO 选项数组
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // 设置错误模式为抛出异常
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 设置默认获取模式为关联数组
    PDO::ATTR_EMULATE_PREPARES => false, // 禁用模拟预处理语句，使用真正的预处理语句
];

try {
    // 创建 PDO 实例，并建立数据库连接
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 捕获异常并抛出新的 PDO 异常，带有错误信息和错误代码
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
