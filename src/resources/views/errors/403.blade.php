<!-- resources/views/errors/403.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            font-size: 50px;
            color: #ff0000;
        }
        p {
            font-size: 20px;
        }
    </style>
    <script>
        setTimeout(function() {
            window.history.back();
        }, 10000); // 5000ミリ秒 = 5秒
    </script>
</head>
<body>
    <div class="container">
        <h1>権限不足/403 Forbidden</h1>
        <p>申し訳ございません。権限がないためページを表示できません。
          <br>５秒後に前のページに戻ります。</p>
    </div>
</body>
</html>
