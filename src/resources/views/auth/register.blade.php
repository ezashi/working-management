@extends('layouts.app')
@section('content')
<div>
  <h2>会員登録</h2>
</div>
<form method="POST" action="{{ route('register') }}">
  @csrf
  <div class="form-group">
    <label for="name">名前</label>
    <input id="name" type="text" name="name" value="{{ old('name') }}">
    @error('name')
      <div class="error-message">{{ $message }}</div>
    @enderror
  </div>
  <div class="form-group">
    <label for="email">メールアドレス</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}">
    @error('email')
      <div class="error-message">{{ $message }}</div>
    @enderror
  </div>
  <div class="form-group">
    <label for="password">パスワード</label>
    <input id="password" type="password" name="password">
    @error('password')
      <div class="error-message">{{ $message }}</div>
    @enderror
  </div>
  <div class="form-group">
    <label for="password-confirm">パスワード確認</label>
    <input id="password-confirm" type="password" name="password_confirmation">
    @error('password_confirmation')
      <div class="error-message">{{ $message }}</div>
    @enderror
  </div>
  <div class="form-group">
    <button type="submit">登録する</button>
  </div>
</form>
<a href="{{ route('login') }}" class="login">ログインはこちら</a>
@endsection