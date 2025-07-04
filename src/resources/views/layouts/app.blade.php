<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  </head>
  <body>
    <header>
      <a href="{{ route('attendance') }}">
          <img src="{{ asset('image/App Logo.svg') }}" alt="coachtech logo->attendance"/>
      </a>
      @auth
        <a href="{{ route('attendance') }}">勤怠</a>
        <a href="{{ route('attendance.list') }}">勤怠一覧</a>
        <a href="{{ route('correction.request.index') }}">申請</a>
        <form action="{{ route('logout') }}" method="POST">
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