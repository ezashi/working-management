@extends('layouts.app')
@section('content')
<div>
  <h2>ログイン</h2>
</div>
<form method="POST" action="{{ route('login') }}">
  @csrf
  <div>
    <label for="email">メールアドレス</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}">
    @error('email')
      <div class="error-message">{{ $message }}</div>
    @enderror
  </div>
  <div>
    <label for="password">パスワード</label>
    <input id="password" type="password" name="password">
    @error('password')
      <div class="error-message">{{ $message }}</div>
    @enderror
  </div>
  <div>
    <button type="submit">ログインする</button>
  </div>
</form>
<a href="{{ route('register') }}" class="register">会員登録はこちら</a>
@endsection