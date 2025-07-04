<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理 - 管理者</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  </head>
  <body>
    <header>
      <a href="{{ route('admin.attendance') }}">
          <img src="{{ asset('image/App Logo.svg') }}" alt="coachtech logo->attendance"/>
      </a>
      @auth
        <a href="{{ route('admin.attendance') }}">勤怠一覧</a>
        <a href="{{ route('admin.staff') }}">スタッフ一覧</a>
        <a href="{{ route('correction.request.index') }}">申請一覧</a>
        <form action="{{ route('admin.logout') }}" method="POST">
          @csrf
          <button type="submit">ログアウト</button>
        </form>
      @endauth
    </header>
    <main>
      @yield('content')
    </main>
  </body>
</html>