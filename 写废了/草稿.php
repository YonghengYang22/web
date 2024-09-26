

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
// 手动运行 SQL 语句查看是否有错误
$userId = 2;  // 替换为实际的用户 ID
$newsId = 6;  // 替换为实际的新闻 ID

$result = $conn->query("INSERT INTO user_profile (user_id, text_id) VALUES ($userId, $newsId)");

if ($result === TRUE) {
    echo "Record inserted successfully";
} else {
    echo "Error inserting record: " . $conn->error;
}
?>
