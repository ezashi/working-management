@extends('layouts.admin')
@section('content')
<div>
  <h2>申請一覧</h2>
</div>
<div>
  <div>
    <h3>
      <a href="{{ route('correction.request.index') }}">承認待ち</a>
      承認済み
    </h3>
    @if($approvalRequests->isEmpty())
      <p>承認済みの申請はありません。</p>
    @else
      <table>
        <thead>
          <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
          </tr>
        </thead>
        <tbody>
          @foreach($approvalRequests as $request)
          <tr>
            <td>
              <span>承認済み</span>
            </td>
            <td>{{ $request->user->name }}</td>
            <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
            <td>{{ $request->modified_note }}</td>
            <td>{{ $request->modified_approval_at->format('Y/m/d') }}</td>
            <td>
              <a href="{{ route('correction.request.approval.show', $request->id) }}">詳細</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection