function submitForm() {
    var searchBoxValue = document.getElementById('searchBox').value;
    console.log('Sending key_word:', searchBoxValue);


    // 使用 AJAX 发送搜索请求
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'search.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // 处理后端返回的数据
            document.getElementById('resultContainer').innerHTML = xhr.responseText;
        }
    };

    xhr.send('key_word=' + encodeURIComponent(searchBoxValue));


    // 阻止表单默认提交行为
    return false;
}

// Function to check if the user is logged in
function checkLoginStatus() {
    var userID = getCookie("userID");

    var userDropdownContent = document.getElementById("userDropdownContent");

    if (userID) {
        // User is logged in
        userDropdownContent.innerHTML = `
            <a href="#">User Profile</a>
            <a href="#" onclick="logout()">Logout</a>
        `;
    } else {
        // User is not logged in
        userDropdownContent.innerHTML = `
            <a href="login.html">Login</a>
            <a href="register.html">Register</a>
        `;
    }
}

// Function to get the value of a cookie by name
function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}

// Function to log out (clear the user's cookie)
function logout() {
    document.cookie = "userID=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;";
    checkLoginStatus(); // Update the dropdown content after logout
}

// Check login status on page load
checkLoginStatus();



