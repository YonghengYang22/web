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

// 检查请求方法
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 解析 JSON 数据
    $data = json_decode(file_get_contents("php://input"));

    // 获取用户 ID
    $userId = $data->userId;

    // 获取用户信息，仅返回 text_id 列
    $userQuery = "SELECT text_id FROM favorite_websites WHERE user_id = $userId";
    $userResult = $conn->query($userQuery);
    

    // 初始化用户数据数组
    $textIds = array();


        // 检查查询是否成功，并且是否有匹配的行
    if ($userResult !== false && $userResult->num_rows > 0) {
        // 将数据库中获取的 text_id 添加到数组中
        while ($row = $userResult->fetch_assoc()) {
            $textIds[] = $row['text_id'];
        }
    }

    // 返回 text_id 数组
    header('Content-Type: application/json'); // 设置响应头部为 JSON
    
    $textIds[0] = 2;
    $textIds[1] = 3;
    $textIds[2] = 4;
    $textIds[3] = 5;
    $textIds[4] = 6;
    echo json_encode(['data' => $textIds]);
} else {
    // 非法请求
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}

// 关闭数据库连接
$conn->close();
?>
