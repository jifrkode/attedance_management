<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atte</title>
  <!-- リセットCSS -->
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <!-- 共通CSS -->
  <link rel="stylesheet" href="{{ asset('css/common.css')}}">
  @yield('css')
</head>

<!-- 共通ヘッダーおよびコンテンツ -->
<header class="header">
  <h1 class="header__heading">
    <a href="{{ route('login') }}">Atte</a>
  </h1>
  @yield('link')
</header>

<body>
  <div class="app">
    @yield('content')
  </div>
  <footer class="footer">
    <p>Atte, inc.</p>
  </footer>
</body>

</html>