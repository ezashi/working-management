@extends('layouts.admin')
@section('content')
<div>
  <h2>スタッフ一覧</h2>
  <div>
    <table>
      <thead>
        <tr>
          <th>名前</th>
          <th>メールアドレス</th>
          <th>月次勤怠</th>
        </tr>
      </thead>
      <tbody>
        @foreach($staffs as $staff)
          <tr>
            <td>{{ $staff->name }}</td>
            <td>{{ $staff->email }}</td>
            <td>
              <a href="{{ route('admin.list', ['id' => $staff->id]) }}">詳細</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection