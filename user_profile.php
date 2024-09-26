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

    // 从数据库中获取用户信息和收藏网页
    $userData = array();


    // 获取用户信息
    $userQuery = "SELECT * FROM users WHERE id = $userId";
    $userResult = $conn->query($userQuery);
    // 示例数据
    $userData = array(
        'userId' => $userId,
        'username' => 'klull', // 从数据库获取真实用户名
        'userPic' => 'path/to/profile_picture.jpg', // 从数据库获取真实用户头像路径
        'favoriteWebsites' => array(
            'https://example.com',
            'https://example2.com',
            // 从数据库获取真实用户收藏网页
        ),
    );

    // 返回用户数据
    echo json_encode($userData);
} else {
    // 非法请求
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
