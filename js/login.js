function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
}

function submitForm() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    // 使用AJAX发送登录请求到服务器
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'login.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    // 处理AJAX响应
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // 解析JSON响应
            var response = JSON.parse(xhr.responseText);

            // 处理后端返回的消息
            if (response.success) {
                // 设置Cookie，这里假设服务器返回了用户的ID
                setCookie("userID", response.userID, 7); // 7天有效期，根据需求设置

                // 重定向到index页面
                window.location.href = 'index.html'; // 修改为你的实际文件路径
            } else {
                alert(response.message); // 登录失败的消息
            }
        }
    };

    // 发送登录数据
    xhr.send('username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password));
}
