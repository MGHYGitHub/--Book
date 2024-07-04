<?php
// add_favorite.php

// 启动会话以访问会话变量
session_start();

// 包含数据库连接文件
require 'db.php';

// 设置响应头，指示返回JSON内容
header('Content-Type: application/json');

// 验证POST数据和会话中的user_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id']) && isset($_SESSION['user_id'])) {
    // 清理并获取POST数据
    $bookId = intval($_POST['book_id']);
    $userId = intval($_SESSION['user_id']);

    try {
        // 检查用户是否已经收藏了这本书
        $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ? AND book_id = ?');
        $checkStmt->execute([$userId, $bookId]);
        $isFavorite = $checkStmt->fetchColumn() > 0;

        if ($isFavorite) {
            // 如果书籍已经在收藏夹中，返回消息
            echo json_encode(['success' => false, 'message' => '已收藏该书，请查看收藏页']);
        } else {
            // 准备SQL语句插入到收藏夹表中
            $stmt = $pdo->prepare('INSERT INTO favorites (user_id, book_id) VALUES (?, ?)');
            $stmt->execute([$userId, $bookId]);

            // 检查插入是否成功
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => '添加至收藏夹失败。']);
            }
        }
    } catch (PDOException $e) {
        // 处理数据库错误并返回错误消息
        echo json_encode(['success' => false, 'message' => '数据库错误：' . $e->getMessage()]);
    }
} else {
    // 如果POST数据或会话中的user_id无效或未设置，返回错误消息
    echo json_encode(['success' => false, 'message' => '无效请求']);
}
?>
