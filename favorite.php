<?php
// 数据库连接配置
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "search_engine";
$port = 3305; // 指定端口号

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// 检查连接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 从请求中获取操作（add_favorite或remove_favorite）
$action = isset($_POST['action']) ? $_POST['action'] : '';

// 从请求中获取用户ID和新闻ID
$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;

$newsId = isset($_POST['newsId']) ? intval($_POST['newsId']) : 0;

print_r($_POST);


if ($action == 'add_favorite') {
    // 检查记录是否已存在
    $checkQuery = "SELECT * FROM user_profile WHERE user_id = $userId AND text_id = $newsId";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult && $checkResult->num_rows == 0) {
        // 如果不存在，则插入记录
        $insertQuery = "INSERT INTO user_profile (user_id, text_id) VALUES ($userId, $newsId)";
        $insertResult = $conn->query($insertQuery);

        if ($insertResult) {
            // 返回成功响应
            echo json_encode(['success' => true]);
        } else {
            // 返回失败响应，指定原因
            echo json_encode(['success' => false, 'reason' => 'Insert failed']);
        }
    } else {
        // 记录已存在，返回成功
        echo json_encode(['success' => true]);
    }
} elseif ($action == 'remove_favorite') {
    // 从user_profile中删除记录
    $deleteQuery = "DELETE FROM user_profile WHERE user_id = $userId AND text_id = $newsId";
    $deleteResult = $conn->query($deleteQuery);

    if ($deleteResult) {
        // 返回成功响应
        echo json_encode(['success' => true]);
    } else {
        // 返回失败响应，指定原因
        echo json_encode(['success' => false, 'reason' => 'Delete failed']);
    }
} else {
    // 无效的操作，返回失败响应
    echo json_encode(['success' => false, 'reason' => 'Invalid action']);
}



// 关闭数据库连接
$conn->close();
?>