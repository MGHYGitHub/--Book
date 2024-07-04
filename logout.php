<!-- logout.php -->
<?php
// 开启 session
session_start();

// 销毁 session
session_destroy();

// 跳转到登录页面
header("Location: index.php");
exit;
?>
