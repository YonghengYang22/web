 function submitForm() {
        var username = document.getElementById('username').value;
        var password = document.getElementById('password').value;

        // 使用AJAX发送注册请求到服务器
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'register.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        // 处理AJAX响应
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // 解析JSON响应
                var response = JSON.parse(xhr.responseText);

                // 处理后端返回的消息
                if (response.success) {
                    alert(response.message); // 注册成功的消息
                    window.location.href = 'login.html'; // 修改为你的实际文件路径
                } else {
                    alert(response.message); // 注册失败的消息
                }
            }
        };

        // 发送注册数据
        xhr.send('username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password));
    }
