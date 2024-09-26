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


// 获取新闻 ID
$newsId = isset($_GET['id']) ? $_GET['id'] : null;

// 查询数据库获取新闻详情
$sql = "SELECT * FROM news WHERE id = $newsId";
$result = $conn->query($sql);

// 处理查询结果
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // 将新闻详情以 JSON 格式返回
    header('Content-Type: application/json');
    echo json_encode([
        'id' => $row['id'],
        'title' => $row['title'],
        'datetime' => $row['datetime'],
        'body' => $row['body'],
        'url' => $row['url'],
    ]);
} else {
    // 新闻不存在时返回空对象
    echo json_encode([]);
}



// 关闭数据库连接
$conn->close();
?>
