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

// 处理错误
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 处理用户登录
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // 查询数据库
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // 用户存在，验证密码
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // 登录成功

                // 设置Cookie，这里假设使用用户的ID作为标识信息
                $userID = $row["id"];
                setcookie("userID", $userID, time() + (7 * 24 * 60 * 60), "/"); // 7天有效期，根据需求设置

                echo json_encode(["success" => true, "message" => "Login successful"]);
            } else {
                // 密码错误
                echo json_encode(["success" => false, "message" => "Incorrect password"]);
            }
        } else {
            // 用户不存在
            echo json_encode(["success" => false, "message" => "User not found"]);
        }
    } else {
        // 查询失败
        echo json_encode(["success" => false, "message" => "Query failed: " . $conn->error]);
    }
}

// 关闭数据库连接
$conn->close();
?>
