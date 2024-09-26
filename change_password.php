<?php
// change_password.php

// 检查请求方法
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 解析 JSON 数据
    $data = json_decode(file_get_contents("php://input"));

    // 获取用户 ID 和新密码
    $userId = $data->userId;
    $newPassword = $data->newPassword;

    // TODO: 在数据库中更新用户密码

    // 示例成功响应
    echo json_encode(['success' => true]);
} else {
    // 非法请求
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
