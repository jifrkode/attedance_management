<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>二段階認証メール送信</title>
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <h1>二段階認証メールを送信しました！</h1>
        <p>登録したメールアドレスをご確認ください。</p>
        <p>認証が完了したら、以下のリンクからログインしてください。</p>
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
</body>
</html>
