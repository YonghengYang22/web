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

// 处理用户注册
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // 使用密码哈希进行存储

    // 检查用户名是否已经存在
    $checkUserQuery = "SELECT * FROM users WHERE username = '$username'";
    $checkUserResult = $conn->query($checkUserQuery);

    if ($checkUserResult->num_rows > 0) {
        // 用户名已存在
        echo json_encode(["success" => false, "message" => "Username already exists"]);
    } else {
        // 插入新用户到数据库
        $insertUserQuery = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        $insertUserResult = $conn->query($insertUserQuery);

        if ($insertUserResult) {
            // 注册成功
            echo json_encode(["success" => true, "message" => "Registration successful"]);
        } else {
            // 注册失败
            echo json_encode(["success" => false, "message" => "Registration failed"]);
        }
    }
}

// 关闭数据库连接
$conn->close();
?>
