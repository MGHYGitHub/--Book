<!-- book_list.php -->
<?php
// 引入数据库连接文件
require_once 'db.php';

// 开启 session
session_start();

// 获取所有书籍
$query = "SELECT * FROM books";
$stmt = $pdo->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll();

// 获取用户收藏的书籍
$user_favorites = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT bid FROM favorites WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $user_favorites = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>书籍列表</title>
    <link rel="stylesheet" href="css/book_list.css">
</head>
<body>
    <div class="bookList">
        <h2>书籍列表</h2>
        <ul>
            <?php foreach ($books as $book): ?>
                <li>
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><?php echo htmlspecialchars($book['author']); ?></p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (in_array($book['id'], $user_favorites)): ?>
                            <form action="remove_favorite.php" method="POST" style="display:inline;">
                                <input type="hidden" name="bid" value="<?php echo $book['id']; ?>">
                                <button type="submit">移除收藏</button>
                            </form>
                        <?php else: ?>
                            <form action="add_favorite.php" method="POST" style="display:inline;">
                                <input type="hidden" name="bid" value="<?php echo $book['id']; ?>">
                                <button type="submit">添加收藏</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
