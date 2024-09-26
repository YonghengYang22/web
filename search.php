<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "search_engine";
$port = 3305; // 指定端口号


$conn = new mysqli($servername, $username, $password, $dbname, $port);

// 检查连接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 处理搜索逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取用户提交的关键词
    $key = isset($_POST['key_word']) ? $_POST['key_word'] : null;

    // 查询匹配的总数据条数
    $countSql = "SELECT COUNT(*) as total FROM news WHERE title LIKE '%$key%'";
    $countResult = $conn->query($countSql);
    $totalCount = $countResult->fetch_assoc()['total'];

    // 每页显示的条数
    $itemsPerPage = 10;

    // 计算总页数
    $totalPages = ceil($totalCount / $itemsPerPage);

    // 获取用户请求的页码，默认为第一页
    $currentPage = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    // 计算偏移量，用于数据库查询
    $offset = ($currentPage - 1) * $itemsPerPage;

    $sql = "SELECT * FROM news WHERE title LIKE '%$key%' LIMIT $offset, $itemsPerPage";
    $result = $conn->query($sql);

    // 处理查询结果
    $docs = []; 

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doc = [
                'id' => $row['id'],
                'url' => $row['url'],
                'title' => $row['title'],
                'datetime' => $row['datetime'],
                'body' => $row['body'],
            ];
            $docs[] = $doc;
        }
    } else {
        // 没有匹配的新闻
        $docs = [];
        $error = true;
    }

    // 将结果以 JSON 格式返回，包括分页信息
    header('Content-Type: application/json');
    echo json_encode(['docs' => $docs, 'total_pages' => $totalPages, 'current_page' => $currentPage, 'error' => $error]);
}



//分页逻辑在后端处理

$conn->close();
?>
